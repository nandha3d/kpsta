import 'dart:async';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/api_constants.dart';
import '../network/api_client.dart';
import '../../features/auth/presentation/auth_providers.dart';

final pushNotificationServiceProvider = Provider<PushNotificationService>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PushNotificationService(apiClient);
});

/// PushNotificationService
/// Handles device registration, native Android background sync + system notifications,
/// foreground/resume polling, and deep-link navigation for Orders/Circulars & News.
class PushNotificationService {
  static const MethodChannel _channel =
      MethodChannel('org.kpsta.kpsta_app/notifications');
  static const String _seenIdsPrefsKey = 'kpsta_seen_notification_ids';
  static const int _maxSeenIds = 60;

  final ApiClient _apiClient;
  bool _initialized = false;
  Timer? _pollingTimer;
  GlobalKey<ScaffoldMessengerState>? _messengerKey;
  GoRouter? _router;

  PushNotificationService(this._apiClient);

  Future<void> initialize({
    required GlobalKey<ScaffoldMessengerState> messengerKey,
    required GoRouter router,
  }) async {
    _messengerKey = messengerKey;
    _router = router;

    if (_initialized) return;
    _initialized = true;

    try {
      // 1. Setup native Android notification channel + background OS periodic sync
      if (!kIsWeb && Platform.isAndroid) {
        _channel.setMethodCallHandler((call) async {
          if (call.method == 'onNotificationTapped') {
            final route = call.arguments?.toString() ?? '';
            if (route.isNotEmpty) {
              _router?.push(route);
            }
          }
        });

        try {
          await _channel.invokeMethod('initializeSync', {
            'apiBaseUrl': ApiConstants.baseUrl,
          });
          final initialRoute =
              await _channel.invokeMethod<String>('consumeInitialRoute');
          if (initialRoute != null && initialRoute.isNotEmpty) {
            Future.microtask(() => _router?.push(initialRoute));
          }
        } catch (_) {
          // Safe fallback in unit tests or unsupported shells
        }
      }

      // 2. Get or generate persistent device push token
      final prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('kpsta_device_push_token');

      if (token == null || token.isEmpty) {
        token =
            'kpsta_device_${DateTime.now().millisecondsSinceEpoch}_${1000 + (DateTime.now().microsecond % 9000)}';
        await prefs.setString('kpsta_device_push_token', token);
      }

      // 3. Register push token with backend
      final platform = kIsWeb
          ? 'web'
          : (Platform.isAndroid
              ? 'android'
              : (Platform.isIOS ? 'ios' : 'windows'));
      await _apiClient.post(
        ApiConstants.registerPushToken,
        data: {
          'token': token,
          'platform': platform,
          'device_model':
              kIsWeb ? 'Web Browser' : Platform.operatingSystemVersion,
        },
      );

      // 4. Sync immediately on startup
      await syncNow();

      // 5. Foreground polling every 2 minutes for instant Orders & News notifications
      _pollingTimer?.cancel();
      _pollingTimer = Timer.periodic(const Duration(minutes: 2), (_) {
        syncNow();
      });
    } catch (_) {
      // Graceful offline fallback
    }
  }

  /// Syncs latest Orders/Circulars & Website News notifications immediately.
  Future<void> syncNow() async {
    if (!_initialized) return;
    try {
      final prefs = await SharedPreferences.getInstance();
      final seenList = prefs.getStringList(_seenIdsPrefsKey) ?? <String>[];

      // Migrate legacy single-id preference if present
      final legacyId = prefs.getString('last_seen_notification_id') ?? '';
      if (legacyId.isNotEmpty && !seenList.contains(legacyId)) {
        seenList.add(legacyId);
      }

      final response = await _apiClient.get(
        ApiConstants.latestNotifications,
        cacheDuration: const Duration(seconds: 15),
      );
      final list = (response['data'] as List<dynamic>?) ?? [];
      if (list.isEmpty) return;

      final incomingIds = <String>[];
      final unseenItems = <Map<String, dynamic>>[];

      for (final raw in list) {
        if (raw is! Map) continue;
        final item = Map<String, dynamic>.from(raw);
        final id = item['id']?.toString() ?? '';
        if (id.isEmpty) continue;
        incomingIds.add(id);
        if (!seenList.contains(id)) {
          unseenItems.add(item);
        }
      }

      if (incomingIds.isEmpty) return;

      final updatedSeen = <String>{...incomingIds, ...seenList}
          .take(_maxSeenIds)
          .toList(growable: false);
      await prefs.setStringList(_seenIdsPrefsKey, updatedSeen);
      await _syncSeenIdsToNative(updatedSeen);

      // On first-ever run, initialize seen list quietly without flooding old notices
      if (seenList.isEmpty) {
        return;
      }

      if (unseenItems.isNotEmpty) {
        // Trigger real Android system notification(s) + prominent in-app banner
        for (final item in unseenItems.take(2)) {
          await _triggerNativeNotification(item);
        }
        _showInAppBanner(unseenItems.first);
      }
    } catch (_) {
      // Silent fallback on transient network unavailability
    }
  }

  Future<void> _syncSeenIdsToNative(List<String> ids) async {
    if (kIsWeb || !Platform.isAndroid) return;
    try {
      await _channel.invokeMethod('markSeenIds', {'ids': ids});
    } catch (_) {}
  }

  Future<void> _triggerNativeNotification(Map<String, dynamic> item) async {
    if (kIsWeb || !Platform.isAndroid) return;
    try {
      await _channel.invokeMethod('showSystemNotification', {
        'id': item['id']?.toString() ?? 'kpsta_notice',
        'title': item['title']?.toString() ?? 'KPSTA Notice',
        'body': item['body']?.toString() ?? '',
        'targetUrl': item['target_url']?.toString() ?? '/home',
      });
    } catch (_) {}
  }

  void _showInAppBanner(Map<String, dynamic> notification) {
    final messengerState = _messengerKey?.currentState;
    if (messengerState == null) return;

    HapticFeedback.heavyImpact();

    final title = notification['title']?.toString() ?? 'KPSTA Notice';
    final body = notification['body']?.toString() ?? '';
    final targetUrl = notification['target_url']?.toString() ?? '/home';
    final type = notification['type']?.toString() ?? '';

    messengerState.hideCurrentSnackBar();
    messengerState.showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.fromLTRB(14, 10, 14, 20),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        backgroundColor: const Color(0xFF0F2C33),
        elevation: 8,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: Color(0xFFEE6A2A), width: 1.2),
        ),
        duration: const Duration(seconds: 6),
        content: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: const BoxDecoration(
                color: Color(0xFFEE6A2A),
                shape: BoxShape.circle,
              ),
              child: Icon(
                type == 'news'
                    ? Icons.newspaper_rounded
                    : Icons.notifications_active_rounded,
                color: Colors.white,
                size: 20,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    body,
                    style: const TextStyle(
                      fontSize: 11.5,
                      color: Color(0xFFE2E8F0),
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ],
        ),
        action: SnackBarAction(
          label: 'VIEW',
          textColor: const Color(0xFFEE6A2A),
          onPressed: () {
            _router?.push(targetUrl);
          },
        ),
      ),
    );
  }

  void dispose() {
    _pollingTimer?.cancel();
  }
}
