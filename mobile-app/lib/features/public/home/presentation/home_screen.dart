import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/widgets/hero_carousel_slider.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/news_ticker_bar.dart';
import '../../../../core/widgets/ribbon_header.dart';
import '../../../auth/presentation/auth_providers.dart';

final homeDataProvider = FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.home);
  return response['data'] as Map<String, dynamic>;
});

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final homeAsync = ref.watch(homeDataProvider);
    final authState = ref.watch(authNotifierProvider);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(68),
        child: Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Color(0x0F000000),
                blurRadius: 8,
                offset: Offset(0, 2),
              ),
            ],
          ),
          child: SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              child: Row(
                children: [
                  // Official KPSTA Logo
                  ClipOval(
                    child: Image.asset(
                      'assets/images/logo.png',
                      width: 44,
                      height: 44,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => const CircleAvatar(
                        radius: 22,
                        backgroundColor: Color(0xFF016D77),
                        child: Icon(Icons.school, color: Colors.white, size: 22),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),

                  // Brand Text
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Text(
                          'KERALA PRADESH',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F1E24),
                            letterSpacing: 0.3,
                            height: 1.1,
                          ),
                        ),
                        Text(
                          "SCHOOL TEACHERS' ASSOCIATION",
                          style: TextStyle(
                            fontSize: 10.5,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF0F1E24),
                            letterSpacing: 0.2,
                            height: 1.1,
                          ),
                        ),
                        SizedBox(height: 1),
                        Text(
                          'AFFILIATED TO AIPTF, AIFTO & EDUCATION INTERNATIONAL',
                          style: TextStyle(
                            fontSize: 7.2,
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

                  // Auth / Menu Action
                  if (authState.status == AuthStatus.authenticated)
                    IconButton(
                      icon: const Icon(Icons.dashboard_outlined, color: Color(0xFF016D77)),
                      tooltip: 'Dashboard',
                      onPressed: () {
                        if (authState.user?.isAdmin == true) {
                          context.push('/admin/dashboard');
                        } else {
                          context.push('/membership/dashboard');
                        }
                      },
                    )
                  else
                    IconButton(
                      icon: const Icon(Icons.login_outlined, color: Color(0xFF016D77)),
                      tooltip: 'Login',
                      onPressed: () => context.push('/login'),
                    ),
                  IconButton(
                    icon: const Icon(Icons.menu, color: Color(0xFF016D77), size: 26),
                    tooltip: 'Menu',
                    onPressed: () => context.go('/more'),
                  ),
                ],
              ),
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
          final sliders = data['sliders'] as List<dynamic>? ?? [];
          final flashNews = data['flash_news'] as List<dynamic>? ?? [];
          final latestNews = data['latest_news'] as List<dynamic>? ?? [];
          final circulars = data['circulars'] as List<dynamic>? ?? [];
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
                  NewsTickerBar(items: flashNews),

                const SizedBox(height: 18),

                // 3. WELCOME TO KPSTA HERO CARD
                _buildWelcomeCard(context),

                const SizedBox(height: 32),

                // 4. STATE LEADERSHIP / OFFICE BEARERS
                if (officeBearers.isNotEmpty) ...[
                  const Text(
                    'Office Bearers',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF111111),
                      letterSpacing: 0.2,
                    ),
                  ),
                  const SizedBox(height: 18),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: officeBearers.map((ob) {
                          return Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 8),
                            child: BearerCard(
                              name: ob['name']?.toString() ?? '',
                              designation: ob['designation']?.toString() ?? '',
                              photoUrl: ob['photo_url']?.toString(),
                              phone: ob['phone']?.toString(),
                              width: 140,
                              photoHeight: 155,
                            ),
                          );
                        }).toList(),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Center(
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF2E9E14),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 26, vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(6),
                        ),
                        elevation: 1,
                      ),
                      onPressed: () => context.push('/organization'),
                      child: const Text(
                        'More Office Bearers',
                        style: TextStyle(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 32),
                ],

                // 5. FOUR MAJOR ACTION TILES (MEMBERSHIP, CIRCULARS, ACADEMIC, SERVICE)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF036672),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      children: [
                        _buildActionTile(
                          icon: Icons.card_membership,
                          title: 'Membership & Magazine',
                          onTap: () {
                            if (authState.status == AuthStatus.authenticated) {
                              context.push('/membership/dashboard');
                            } else {
                              context.push('/login');
                            }
                          },
                        ),
                        const SizedBox(height: 12),
                        _buildActionTile(
                          icon: Icons.description,
                          title: 'Order & Circular',
                          onTap: () => context.go('/circulars'),
                        ),
                        const SizedBox(height: 12),
                        _buildActionTile(
                          icon: Icons.school,
                          title: 'Academic Corner',
                          onTap: () => context.push('/services'),
                        ),
                        const SizedBox(height: 12),
                        _buildActionTile(
                          icon: Icons.volunteer_activism,
                          title: 'Service Corner',
                          onTap: () => context.push('/services'),
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 28),

                // 6. RECENT ORDERS & CIRCULARS
                if (circulars.isNotEmpty) ...[
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: RibbonHeader(
                      title: 'Recent Orders & Circulars',
                      color: const Color(0xFF016D77),
                      margin: const EdgeInsets.only(bottom: 12),
                    ),
                  ),
                  ...circulars.take(4).map((circ) {
                    return DocumentRow(
                      title: circ['title']?.toString() ?? '',
                      date: (circ['circular_date'] ?? circ['date'] ?? circ['created_date'] ?? circ['created_at'])?.toString(),
                      category: (circ['category_name'] ?? circ['category'])?.toString(),
                      fileUrl: circ['file_url']?.toString(),
                      onTap: () {
                        final id = circ['id']?.toString() ?? '';
                        context.push('/circulars/$id');
                      },
                    );
                  }),
                  Center(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      child: TextButton.icon(
                        icon: const Icon(Icons.arrow_forward, size: 16, color: Color(0xFF016D77)),
                        label: const Text(
                          'View All Circulars & Orders',
                          style: TextStyle(
                            color: Color(0xFF016D77),
                            fontWeight: FontWeight.w700,
                            fontSize: 13,
                          ),
                        ),
                        onPressed: () => context.go('/circulars'),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // 7. NEWS WINDOW SECTION
                Container(
                  color: const Color(0xFFE5F3F4),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 28),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'News Window',
                            style: TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.w800,
                              color: Color(0xFF0F1E24),
                            ),
                          ),
                          OutlinedButton(
                            style: OutlinedButton.styleFrom(
                              foregroundColor: const Color(0xFF016D77),
                              side: const BorderSide(color: Color(0xFF016D77), width: 1.5),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(6),
                              ),
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                              minimumSize: const Size(0, 36),
                            ),
                            onPressed: () => context.go('/news'),
                            child: const Text(
                              'View All',
                              style: TextStyle(
                                fontWeight: FontWeight.w700,
                                fontSize: 13,
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
                              borderRadius: BorderRadius.circular(10),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.04),
                                  blurRadius: 8,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: Material(
                              color: Colors.transparent,
                              child: InkWell(
                                onTap: () => context.push('/news/$id'),
                                borderRadius: BorderRadius.circular(10),
                                child: Padding(
                                  padding: const EdgeInsets.all(12),
                                  child: Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      ClipRRect(
                                        borderRadius: BorderRadius.circular(8),
                                        child: AppNetworkImage(
                                          imageUrl: imageUrl,
                                          width: 100,
                                          height: 78,
                                          fit: BoxFit.cover,
                                          placeholder: Container(
                                            color: const Color(0xFFE2E8F0),
                                            width: 100,
                                            height: 78,
                                          ),
                                          errorWidget: Container(
                                            color: const Color(0xFF016D77).withValues(alpha: 0.1),
                                            width: 100,
                                            height: 78,
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
                                              'Read More',
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

                const SizedBox(height: 36),

                // 7. REACTION GALLERY POSTER
                Container(
                  color: const Color(0xFF1F6B76),
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 32),
                  child: Column(
                    children: [
                      const Text(
                        'Reaction Gallery',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                          letterSpacing: 0.3,
                        ),
                      ),
                      const SizedBox(height: 20),
                      InkWell(
                        onTap: () => context.push('/gallery'),
                        borderRadius: BorderRadius.circular(14),
                        child: Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(14),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.2),
                                blurRadius: 14,
                                offset: const Offset(0, 6),
                              ),
                            ],
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(14),
                            child: SizedBox(
                              height: 220,
                              width: double.infinity,
                              child: AppNetworkImage(
                                imageUrl: reactionGallery.isNotEmpty
                                    ? reactionGallery.first['image_url']?.toString()
                                    : null,
                                fit: BoxFit.cover,
                                width: double.infinity,
                                height: 220,
                                placeholder: Container(
                                  color: const Color(0xFF0A5863),
                                  width: double.infinity,
                                  height: 220,
                                ),
                                errorWidget: Image.asset(
                                  'assets/images/reaction_gallery.png',
                                  fit: BoxFit.cover,
                                  width: double.infinity,
                                  height: 220,
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 36),

                // 8. GALLERY HIGHLIGHTS
                if (galleryHighlights.isNotEmpty) ...[
                  const Text(
                    'Gallery',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF111111),
                      letterSpacing: 0.3,
                    ),
                  ),
                  const SizedBox(height: 18),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: galleryHighlights.take(8).map((gh) {
                          final imgUrl = gh['image_url']?.toString() ?? '';
                          final caption = gh['caption']?.toString() ?? gh['album_name']?.toString() ?? '';

                          return Container(
                            width: 130,
                            margin: const EdgeInsets.only(right: 12),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(12),
                                  child: AppNetworkImage(
                                    imageUrl: imgUrl,
                                    width: 130,
                                    height: 180,
                                    fit: BoxFit.cover,
                                    placeholder: Container(
                                      width: 130,
                                      height: 180,
                                      color: const Color(0xFFE2E8F0),
                                    ),
                                    errorWidget: Container(
                                      width: 130,
                                      height: 180,
                                      color: const Color(0xFF016D77).withValues(alpha: 0.15),
                                      child: const Icon(Icons.image, color: Color(0xFF016D77)),
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  caption.isNotEmpty ? caption : 'KPSTA State Conference',
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



  Widget _buildWelcomeCard(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            Color(0xFF0A5863),
            Color(0xFF033138),
          ],
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.12),
            blurRadius: 14,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        children: [
          const Text(
            'Welcome To KPSTA',
            style: TextStyle(
              fontSize: 21,
              fontWeight: FontWeight.w800,
              color: Colors.white,
              letterSpacing: 0.3,
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'KPSTA – Kerala Pradesh School Teachers Association is the largest and most prestigious organization of school teachers in Kerala. Various organizations representing school teachers at various levels in the state and formed over a long period of time since 1931 came under a single umbrella called \'Kerala Pradesh School Teachers Association\'. These were formed with the aim of providing better services to the school teachers of the kerala state.',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Color(0xFFE2E8F0),
              fontSize: 12.5,
              height: 1.55,
            ),
          ),
          const SizedBox(height: 18),
          OutlinedButton(
            style: OutlinedButton.styleFrom(
              foregroundColor: Colors.white,
              side: const BorderSide(color: Colors.white, width: 1.5),
              padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 10),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(6),
              ),
            ),
            onPressed: () => context.push('/contact'),
            child: const Text(
              'Read More',
              style: TextStyle(
                fontWeight: FontWeight.w700,
                fontSize: 13,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionTile({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          decoration: BoxDecoration(
            color: const Color(0xFF86CCD2),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Row(
            children: [
              Container(
                width: 64,
                height: 60,
                decoration: const BoxDecoration(
                  color: Color(0xFFF05A22),
                  borderRadius: BorderRadius.only(
                    topLeft: Radius.circular(12),
                    bottomLeft: Radius.circular(12),
                  ),
                ),
                alignment: Alignment.center,
                child: Icon(icon, color: Colors.white, size: 30),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF0F1E24),
                    letterSpacing: 0.2,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
