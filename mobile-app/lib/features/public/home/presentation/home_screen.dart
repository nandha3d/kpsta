import 'dart:async';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/widgets/hero_carousel_slider.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/news_ticker_bar.dart';
import '../../../auth/presentation/auth_providers.dart';
import 'widgets/quick_menu_widget.dart';
import 'widgets/view_all_circulars_banner.dart';
import 'widgets/portrait_reaction_gallery.dart';
import 'widgets/reaction_gallery_popup_dialog.dart';

final homeDataProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.home, cacheDuration: const Duration(minutes: 5));
  return response['data'] as Map<String, dynamic>;
});

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  Timer? _reactionPopupTimer;
  static bool _hasShownReactionPopupInSession = false;

  @override
  void dispose() {
    _reactionPopupTimer?.cancel();
    super.dispose();
  }

  void _scheduleReactionPopupIfNeeded(Map<String, dynamic> data) {
    if (_hasShownReactionPopupInSession) return;

    final popupConfig = data['reaction_popup'] as Map<String, dynamic>?;
    final rawEnabled = popupConfig?['raw_enabled'] ?? popupConfig?['enabled'] ?? true;
    final isEnabled = rawEnabled == true || rawEnabled == 1 || rawEnabled == '1';
    if (!isEnabled) return;

    // Client-side Start & End Date schedule guard
    final now = DateTime.now();
    final todayIso =
        '${now.year.toString().padLeft(4, '0')}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}';
    final startDate = popupConfig?['start_date']?.toString().trim() ?? '';
    final endDate = popupConfig?['end_date']?.toString().trim() ?? '';
    if (startDate.isNotEmpty && todayIso.compareTo(startDate) < 0) return;
    if (endDate.isNotEmpty && todayIso.compareTo(endDate) > 0) return;

    final durationSec = (popupConfig?['duration'] is int)
        ? popupConfig!['duration'] as int
        : (int.tryParse(popupConfig?['duration']?.toString() ?? '') ?? 20);

    final List<Map<String, dynamic>> popupItems = [];
    final serverItems = popupConfig?['items'] as List<dynamic>?;
    if (serverItems != null && serverItems.isNotEmpty) {
      for (final item in serverItems) {
        if (item is Map) {
          popupItems.add(Map<String, dynamic>.from(item));
        }
      }
    } else {
      final source = popupConfig?['source']?.toString() ?? 'both';
      final reactions = data['reaction_gallery'] as List<dynamic>? ?? [];
      final newsList = data['latest_news'] as List<dynamic>? ?? [];

      if ((source == 'both' || source == 'reaction_gallery') && reactions.isNotEmpty) {
        final r = Map<String, dynamic>.from(reactions.first as Map);
        r['type'] = 'reaction';
        r['route'] = '/reaction-gallery';
        popupItems.add(r);
      }
      if ((source == 'both' || source == 'news') && newsList.isNotEmpty) {
        final n = Map<String, dynamic>.from(newsList.first as Map);
        final nid = n['id']?.toString() ?? '';
        n['type'] = 'news';
        n['route'] = (nid.isNotEmpty && nid != '0') ? '/news/$nid' : '/news';
        popupItems.add(n);
      }
    }

    if (popupItems.isNotEmpty) {
      _reactionPopupTimer?.cancel();
      _reactionPopupTimer = Timer(Duration(seconds: durationSec), () {
        if (mounted && !_hasShownReactionPopupInSession) {
          _hasShownReactionPopupInSession = true;
          ReactionGalleryPopupDialog.showList(context, popupItems);
        }
      });
    }
  }

  void _precacheTopImages(Map<String, dynamic> data) {
    try {
      final sliders = data['sliders'] as List<dynamic>? ?? [];
      for (final s in sliders.take(3)) {
        final img = s['image_url']?.toString();
        if (img != null && img.startsWith('http')) {
          precacheImage(CachedNetworkImageProvider(img), context);
        }
      }
      final reactions = data['reaction_gallery'] as List<dynamic>? ?? [];
      if (reactions.isNotEmpty) {
        final img = reactions.first['image_url']?.toString();
        if (img != null && img.startsWith('http')) {
          precacheImage(CachedNetworkImageProvider(img), context);
        }
      }
      final latestNews = data['latest_news'] as List<dynamic>? ?? [];
      if (latestNews.isNotEmpty) {
        final img = latestNews.first['image_url']?.toString();
        if (img != null && img.startsWith('http')) {
          precacheImage(CachedNetworkImageProvider(img), context);
        }
      }
      final bearers = data['office_bearers'] as List<dynamic>? ?? [];
      for (final b in bearers.take(3)) {
        final img = b['photo_url']?.toString();
        if (img != null && img.startsWith('http')) {
          precacheImage(CachedNetworkImageProvider(img), context);
        }
      }
      final gallery = data['gallery_highlights'] as List<dynamic>? ?? [];
      for (final g in gallery.take(4)) {
        final img = (g['thumb_url'] ?? g['image_url'])?.toString();
        if (img != null && img.startsWith('http')) {
          precacheImage(CachedNetworkImageProvider(img), context);
        }
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final homeAsync = ref.watch(homeDataProvider);
    final authState = ref.watch(authNotifierProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(70),
        child: Container(
          decoration: const BoxDecoration(
            color: Color(0xFFFAFCFD),
            boxShadow: [
              BoxShadow(
                color: Color(0x0A000000),
                blurRadius: 8,
                offset: Offset(0, 2),
              ),
            ],
          ),
          child: SafeArea(
                bottom: false,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                        child: Row(
                          children: [
                            // Official KPSTA Logo
                            ClipOval(
                              child: Image.asset(
                                'assets/images/logo.png',
                                width: 42,
                                height: 42,
                                fit: BoxFit.cover,
                                errorBuilder: (_, _, _) => const CircleAvatar(
                                  radius: 21,
                                  backgroundColor: Color(0xFF016D77),
                                  child: Icon(Icons.school, color: Colors.white, size: 20),
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),

                            // Brand Text (Full width without top-right portal button)
                            const Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(
                                    'KERALA PRADESH',
                                    style: TextStyle(
                                      fontSize: 13.5,
                                      fontWeight: FontWeight.w900,
                                      color: Color(0xFF0F1E24),
                                      letterSpacing: 0.3,
                                      height: 1.1,
                                    ),
                                  ),
                                  Text(
                                    "SCHOOL TEACHERS' ASSOCIATION",
                                    style: TextStyle(
                                      fontSize: 10.8,
                                      fontWeight: FontWeight.w800,
                                      color: Color(0xFF0F1E24),
                                      letterSpacing: 0.2,
                                      height: 1.15,
                                    ),
                                  ),
                                  SizedBox(height: 1),
                                  Text(
                                    'AFFILIATED TO AIPTF, AIFTO & EDUCATION INTERNATIONAL',
                                    style: TextStyle(
                                      fontSize: 7.4,
                                      fontWeight: FontWeight.w600,
                                      color: Color(0xFF64748B),
                                      letterSpacing: 0.1,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 8),

                            // Official KPSTA Flag neatly displayed in the title header
                            Image.asset(
                              'assets/images/flag.png',
                              width: 60,
                              height: 42,
                              fit: BoxFit.contain,
                              errorBuilder: (_, _, _) => const SizedBox.shrink(),
                            ),
                          ],
                        ),
                      ),
                    ),
          // Flag Header Tricolor Strip (Saffron, White, Green)
          Container(
            height: 2.5,
            width: double.infinity,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  Color(0xFFEE6A2A),
                  Color(0xFFFFA26B),
                  Colors.white,
                  Color(0xFF76D15E),
                  Color(0xFF2E9E14),
                ],
                stops: [0.0, 0.25, 0.5, 0.75, 1.0],
              ),
            ),
          ),
        ],
      ),
    ),
  ),
),
      body: homeAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(homeDataProvider),
        ),
        data: (data) {
          // Background precache top images & schedule reaction popup
          WidgetsBinding.instance.addPostFrameCallback((_) {
            _precacheTopImages(data);
            _scheduleReactionPopupIfNeeded(data);
          });

          final sliders = data['sliders'] as List<dynamic>? ?? [];
          final flashNews = data['flash_news'] as List<dynamic>? ?? [];
          final latestNews = data['latest_news'] as List<dynamic>? ?? [];
          final officeBearers = data['office_bearers'] as List<dynamic>? ?? [];
          final reactionGallery = data['reaction_gallery'] as List<dynamic>? ?? [];
          final galleryHighlights = data['gallery_highlights'] as List<dynamic>? ?? [];

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(homeDataProvider),
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: EdgeInsets.zero,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // 1. HERO SLIDER CAROUSEL WITH FLAG AND SLOGANS
                  HeroCarouselSlider(sliders: sliders),

                  // 2. NEWS TICKER MARQUEE
                  if (flashNews.isNotEmpty)
                    NewsTickerBar(
                      items: flashNews,
                      speed: (data['flash_news_speed'] is num)
                          ? (data['flash_news_speed'] as num).toDouble()
                          : (double.tryParse(data['flash_news_speed']?.toString() ?? '') ?? 65.0),
                    ),

                  const SizedBox(height: 18),

                  // 3. CHRONICLE OF KPSTA LINK HEADER + STATE LEADERSHIP PHOTOS
                  Center(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Material(
                        color: Colors.transparent,
                        child: InkWell(
                          onTap: () {
                            HapticFeedback.lightImpact();
                            context.push('/chronicle');
                          },
                          borderRadius: BorderRadius.circular(16),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 18,
                              vertical: 10,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.78),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: const Color(0xFF016D77).withValues(alpha: 0.22),
                                width: 1.2,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(0xFF016D77).withValues(alpha: 0.08),
                                  blurRadius: 10,
                                  offset: const Offset(0, 3),
                                ),
                              ],
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(
                                  Icons.history_edu_rounded,
                                  color: Color(0xFF016D77),
                                  size: 22,
                                ),
                                const SizedBox(width: 8),
                                Column(
                                  mainAxisSize: MainAxisSize.min,
                                  children: const [
                                    Text(
                                      'Chronicle of KPSTA',
                                      textAlign: TextAlign.center,
                                      style: TextStyle(
                                        fontSize: 19,
                                        fontWeight: FontWeight.w800,
                                        color: Color(0xFF016D77),
                                        letterSpacing: 0.2,
                                      ),
                                    ),
                                    SizedBox(height: 1),
                                    Text(
                                      'കെ.പി.എസ്.ടി.എ. ചരിത്ര നാൾവഴികൾ',
                                      textAlign: TextAlign.center,
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w600,
                                        color: Color(0xFF64748B),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(width: 8),
                                const Icon(
                                  Icons.arrow_forward_rounded,
                                  color: Color(0xFFEE6A2A),
                                  size: 19,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  if (officeBearers.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: officeBearers.take(3).map((ob) {
                          return Expanded(
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 3),
                              child: BearerCard(
                                name: ob['name']?.toString() ?? '',
                                designation: ob['designation']?.toString() ?? '',
                                photoUrl: ob['photo_url']?.toString(),
                                phone: ob['phone']?.toString(),
                                aspectRatio: 1.0,
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                    const SizedBox(height: 14),
                    Center(
                      child: ElevatedButton.icon(
                        icon: const Icon(Icons.people_outline, size: 16),
                        label: const Text(
                          'More Office Bearers',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF016D77),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 10),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(20),
                          ),
                          elevation: 1,
                        ),
                        onPressed: () => context.push('/organization'),
                      ),
                    ),
                    const SizedBox(height: 16),
                  ],

                  // 4. EXPANDABLE QUICK MENU (Clean, Beautiful, Collapsible by Default)
                  QuickMenuWidget(
                    isAuthenticated: authState.status == AuthStatus.authenticated,
                  ),

                  // 5. VIEW ALL CIRCULARS & ORDERS ACTION BANNER
                  const ViewAllCircularsBanner(),

                  const SizedBox(height: 16),

                  // 6. NEWS WINDOW SECTION
                  Container(
                    color: const Color(0xFFE5F3F4),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 26),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: const [
                                Text(
                                  'News Window',
                                  style: TextStyle(
                                    fontSize: 21,
                                    fontWeight: FontWeight.w800,
                                    color: Color(0xFF0F1E24),
                                  ),
                                ),
                                SizedBox(height: 2),
                                Text(
                                  'വാർത്തകൾ & അറിയിപ്പുകൾ',
                                  style: TextStyle(
                                    fontSize: 11.5,
                                    color: Color(0xFF64748B),
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ],
                            ),
                            OutlinedButton(
                              style: OutlinedButton.styleFrom(
                                foregroundColor: const Color(0xFF016D77),
                                side: const BorderSide(color: Color(0xFF016D77), width: 1.4),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                                minimumSize: const Size(0, 34),
                              ),
                              onPressed: () => context.go('/news'),
                              child: const Text(
                                'View All',
                                style: TextStyle(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 12.5,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        if (latestNews.isEmpty)
                          const Padding(
                            padding: EdgeInsets.symmetric(vertical: 16),
                            child: EmptyState(
                              title: 'No news published yet',
                              icon: Icons.newspaper_outlined,
                            ),
                          )
                        else
                          ...latestNews.take(3).map((item) {
                            final title = item['title']?.toString() ?? '';
                            final date = (item['date'] ?? item['published_at'] ?? item['created_at'])?.toString() ?? '';
                            final id = item['id']?.toString() ?? '';
                            final imageUrl = (item['image_url'] ?? item['photo_url'])?.toString();

                            return Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: const Color(0xFF016D77).withValues(alpha: 0.12),
                                  width: 1,
                                ),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withValues(alpha: 0.04),
                                    blurRadius: 8,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Material(
                                color: Colors.transparent,
                                child: InkWell(
                                  onTap: () => context.push('/news/$id'),
                                  borderRadius: BorderRadius.circular(12),
                                  child: Padding(
                                    padding: const EdgeInsets.all(12),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(8),
                                          child: AppNetworkImage(
                                            imageUrl: imageUrl,
                                            width: 96,
                                            height: 76,
                                            fit: BoxFit.cover,
                                            placeholder: Container(
                                              color: const Color(0xFFE2E8F0),
                                              width: 96,
                                              height: 76,
                                            ),
                                            errorWidget: Container(
                                              color: const Color(0xFF016D77).withValues(alpha: 0.1),
                                              width: 96,
                                              height: 76,
                                              child: const Icon(
                                                Icons.newspaper,
                                                color: Color(0xFF016D77),
                                              ),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              if (date.isNotEmpty)
                                                Text(
                                                  date,
                                                  style: const TextStyle(
                                                    fontSize: 11,
                                                    color: Color(0xFF64748B),
                                                    fontWeight: FontWeight.w500,
                                                  ),
                                                ),
                                              const SizedBox(height: 4),
                                              Text(
                                                title,
                                                style: const TextStyle(
                                                  fontSize: 13.5,
                                                  fontWeight: FontWeight.w700,
                                                  color: Color(0xFF0F1E24),
                                                  height: 1.3,
                                                ),
                                                maxLines: 2,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                              const SizedBox(height: 6),
                                              const Text(
                                                'Read More →',
                                                style: TextStyle(
                                                  fontSize: 12,
                                                  fontWeight: FontWeight.w700,
                                                  color: Color(0xFFF05A22),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            );
                          }),
                      ],
                    ),
                  ),

                  // 7. REACTION GALLERY POSTERS (Portrait Mode)
                  if (reactionGallery.isNotEmpty)
                    PortraitReactionGallery(reactions: reactionGallery),

                  const SizedBox(height: 20),

                  // 8. GALLERY HIGHLIGHTS
                  if (galleryHighlights.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const SizedBox(width: 70), // Balance center title
                          Expanded(
                            child: InkWell(
                              onTap: () => context.push('/gallery'),
                              borderRadius: BorderRadius.circular(8),
                              child: const Column(
                                children: [
                                  Text(
                                    'Gallery',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(
                                      fontSize: 21,
                                      fontWeight: FontWeight.w800,
                                      color: Color(0xFF0F1E24),
                                      letterSpacing: 0.3,
                                    ),
                                  ),
                                  SizedBox(height: 2),
                                  Text(
                                    'ചിത്രശാല · Events & Conferences',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w500,
                                      color: Color(0xFF64748B),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          TextButton(
                            style: TextButton.styleFrom(
                              foregroundColor: const Color(0xFF016D77),
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              minimumSize: Size.zero,
                              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                            ),
                            onPressed: () => context.push('/gallery'),
                            child: const Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  'View All',
                                  style: TextStyle(
                                    fontWeight: FontWeight.w700,
                                    fontSize: 12.5,
                                  ),
                                ),
                                SizedBox(width: 2),
                                Icon(Icons.arrow_forward_ios_rounded, size: 12),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: SingleChildScrollView(
                        scrollDirection: Axis.horizontal,
                        physics: const BouncingScrollPhysics(),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: galleryHighlights.take(12).map((gh) {
                            String imgUrl = (gh['thumb_url'] ?? gh['image_url'])?.toString() ?? '';
                            if (imgUrl.contains('/uploads/gallery/') &&
                                !imgUrl.contains('/uploads/gallery/thumb/') &&
                                !imgUrl.contains('/uploads/gallery/original/')) {
                              imgUrl = imgUrl.replaceAll('/uploads/gallery/', '/uploads/gallery/thumb/');
                            }
                            final caption = gh['caption']?.toString() ?? gh['album_name']?.toString() ?? '';
                            final albumId = gh['album_id']?.toString() ?? gh['album_guid']?.toString() ?? '';

                            return Container(
                              width: 130,
                              margin: const EdgeInsets.only(right: 12),
                              child: Material(
                                color: Colors.transparent,
                                child: InkWell(
                                  borderRadius: BorderRadius.circular(12),
                                  onTap: () {
                                    if (albumId.isNotEmpty && albumId != '0') {
                                      context.push('/gallery/$albumId');
                                    } else {
                                      context.push('/gallery');
                                    }
                                  },
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.center,
                                    children: [
                                      ClipRRect(
                                        borderRadius: BorderRadius.circular(12),
                                        child: AppNetworkImage(
                                          imageUrl: imgUrl,
                                          width: 130,
                                          height: 170,
                                          fit: BoxFit.cover,
                                          placeholder: Container(
                                            width: 130,
                                            height: 170,
                                            color: const Color(0xFFE2E8F0),
                                            child: const Center(
                                              child: SizedBox(
                                                width: 20,
                                                height: 20,
                                                child: CircularProgressIndicator(strokeWidth: 2),
                                              ),
                                            ),
                                          ),
                                          errorWidget: Container(
                                            width: 130,
                                            height: 170,
                                            color: const Color(0xFF016D77).withValues(alpha: 0.15),
                                            child: const Icon(
                                              Icons.photo_library_outlined,
                                              color: Color(0xFF016D77),
                                              size: 32,
                                            ),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        caption.isNotEmpty ? caption : 'KPSTA Event',
                                        textAlign: TextAlign.center,
                                        style: const TextStyle(
                                          fontSize: 11,
                                          fontWeight: FontWeight.w600,
                                          color: Color(0xFF0F1E24),
                                          height: 1.2,
                                        ),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            );
                          }).toList(),
                        ),
                      ),
                    ),
                    const SizedBox(height: 28),
                  ],

                  // 9. OFFICIAL KPSTA FOOTER
                  const KpstaFooter(),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
