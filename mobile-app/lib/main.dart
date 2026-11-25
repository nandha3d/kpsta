import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/services/push_notification_service.dart';
import 'core/theme/app_theme.dart';
import 'core/widgets/flag_background_wrapper.dart';
import 'router/app_router.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  // High-performance image cache: keep decoded textures in RAM for 0ms instant display
  PaintingBinding.instance.imageCache.maximumSize = 1000;
  PaintingBinding.instance.imageCache.maximumSizeBytes = 250 << 20; // 250 MB

  runApp(
    const ProviderScope(
      child: KpstaApp(),
    ),
  );
}

class KpstaApp extends ConsumerStatefulWidget {
  const KpstaApp({super.key});

  @override
  ConsumerState<KpstaApp> createState() => _KpstaAppState();
}

class _KpstaAppState extends ConsumerState<KpstaApp> with WidgetsBindingObserver {
  final GlobalKey<ScaffoldMessengerState> _scaffoldMessengerKey =
      GlobalKey<ScaffoldMessengerState>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    // Initialize Push Notification Service with global keys so deep links and banners work from any screen
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final router = ref.read(routerProvider);
      ref.read(pushNotificationServiceProvider).initialize(
            messengerKey: _scaffoldMessengerKey,
            router: router,
          );
    });
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    // Check immediately for newly published Orders/News whenever user returns to app
    if (state == AppLifecycleState.resumed) {
      ref.read(pushNotificationServiceProvider).syncNow();
    }
  }

  @override
  Widget build(BuildContext context) {
    final router = ref.watch(routerProvider);

    return MaterialApp.router(
      title: 'KPSTA',
      debugShowCheckedModeBanner: false,
      scaffoldMessengerKey: _scaffoldMessengerKey,
      theme: AppTheme.lightTheme,
      routerConfig: router,
      builder: (context, child) {
        return FlagBackgroundWrapper(
          child: child ?? const SizedBox.shrink(),
        );
      },
    );
  }
}
