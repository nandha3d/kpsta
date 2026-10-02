import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
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
      appBar: AppTopBar(
        title: 'KPSTA',
        showBackButton: false,
        leading: Padding(
          padding: const EdgeInsets.only(left: 12),
          child: CircleAvatar(
            backgroundColor: AppColors.white.withOpacity(0.2),
            child: const Icon(Icons.school, color: AppColors.white, size: 20),
          ),
        ),
        actions: [
          if (authState.status == AuthStatus.authenticated)
            IconButton(
              icon: const Icon(Icons.dashboard_outlined),
              tooltip: 'Go to Dashboard',
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
              icon: const Icon(Icons.login_outlined),
              tooltip: 'Login',
              onPressed: () => context.push('/login'),
            ),
        ],
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

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(homeDataProvider),
            child: ListView(
              padding: const EdgeInsets.only(bottom: 24),
              children: [
                // Flash News Marquee / Banner
                if (flashNews.isNotEmpty)
                  Container(
                    color: AppColors.orange,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 8,
                    ),
                    child: Row(
                      children: [
                        const Icon(
                          Icons.campaign,
                          color: AppColors.white,
                          size: 20,
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            flashNews.first['title']?.toString() ?? '',
                            style: const TextStyle(
                              color: AppColors.white,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ),

                // Hero Slider Carousel
                if (sliders.isNotEmpty)
                  SizedBox(
                    height: 190,
                    child: PageView.builder(
                      itemCount: sliders.length,
                      itemBuilder: (ctx, idx) {
                        final slider = sliders[idx];
                        final imgUrl = slider['image_url']?.toString() ?? '';
                        final title = slider['title']?.toString() ?? '';

                        return Container(
                          margin: const EdgeInsets.fromLTRB(16, 12, 16, 4),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.08),
                                blurRadius: 8,
                                offset: const Offset(0, 3),
                              ),
                            ],
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: Stack(
                              fit: StackFit.expand,
                              children: [
                                if (imgUrl.isNotEmpty)
                                  CachedNetworkImage(
                                    imageUrl: imgUrl,
                                    fit: BoxFit.cover,
                                    placeholder: (ctx, url) => Container(
                                      color: AppColors.primaryLight
                                          .withOpacity(0.2),
                                    ),
                                    errorWidget: (ctx, url, err) =>
                                        Container(
                                      color: AppColors.primaryDark,
                                      child: const Icon(
                                        Icons.image_outlined,
                                        size: 40,
                                        color: Colors.white54,
                                      ),
                                    ),
                                  )
                                else
                                  Container(color: AppColors.primaryDark),
                                Container(
                                  decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                      begin: Alignment.topCenter,
                                      end: Alignment.bottomCenter,
                                      colors: [
                                        Colors.transparent,
                                        Colors.black.withOpacity(0.7),
                                      ],
                                    ),
                                  ),
                                ),
                                if (title.isNotEmpty)
                                  Positioned(
                                    bottom: 12,
                                    left: 14,
                                    right: 14,
                                    child: Text(
                                      title,
                                      style: const TextStyle(
                                        color: AppColors.white,
                                        fontWeight: FontWeight.w700,
                                        fontSize: 15,
                                      ),
                                      maxLines: 2,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                const SizedBox(height: 12),

                // Quick Navigation Grid
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Row(
                    children: [
                      _buildQuickAction(
                        context,
                        icon: Icons.newspaper,
                        title: 'News',
                        color: AppColors.primary,
                        onTap: () => context.go('/news'),
                      ),
                      const SizedBox(width: 10),
                      _buildQuickAction(
                        context,
                        icon: Icons.description,
                        title: 'Circulars',
                        color: AppColors.indigo,
                        onTap: () => context.go('/circulars'),
                      ),
                      const SizedBox(width: 10),
                      _buildQuickAction(
                        context,
                        icon: Icons.file_download,
                        title: 'Downloads',
                        color: AppColors.orange,
                        onTap: () => context.go('/downloads'),
                      ),
                      const SizedBox(width: 10),
                      _buildQuickAction(
                        context,
                        icon: Icons.volunteer_activism,
                        title: 'Donation',
                        color: AppColors.green,
                        onTap: () => context.push('/donation'),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // Section: Latest News
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Latest News & Updates',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textDark,
                        ),
                      ),
                      TextButton(
                        onPressed: () => context.go('/news'),
                        child: const Text('View All'),
                      ),
                    ],
                  ),
                ),

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
                    final date = item['date']?.toString() ?? '';
                    final id = item['id']?.toString() ?? '';
                    final imageUrl = item['image_url']?.toString();

                    return Card(
                      margin: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 6,
                      ),
                      child: ListTile(
                        leading: ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: imageUrl != null && imageUrl.isNotEmpty
                              ? CachedNetworkImage(
                                  imageUrl: imageUrl,
                                  width: 50,
                                  height: 50,
                                  fit: BoxFit.cover,
                                  placeholder: (ctx, u) => Container(
                                    color: AppColors.bgLight,
                                  ),
                                  errorWidget: (ctx, u, e) => Container(
                                    color: AppColors.bgLight,
                                    child: const Icon(
                                      Icons.article_outlined,
                                      color: AppColors.primary,
                                    ),
                                  ),
                                )
                              : Container(
                                  width: 50,
                                  height: 50,
                                  color: AppColors.primary.withOpacity(0.1),
                                  child: const Icon(
                                    Icons.article_outlined,
                                    color: AppColors.primary,
                                  ),
                                ),
                        ),
                        title: Text(
                          title,
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textDark,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        subtitle: date.isNotEmpty
                            ? Text(
                                date,
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppColors.textMuted,
                                ),
                              )
                            : null,
                        trailing: const Icon(
                          Icons.arrow_forward_ios,
                          size: 14,
                          color: AppColors.textLight,
                        ),
                        onTap: () => context.push('/news/$id'),
                      ),
                    );
                  }),

                const SizedBox(height: 16),

                // Section: Recent Order Circulars
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Recent Circulars & Orders',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textDark,
                        ),
                      ),
                      TextButton(
                        onPressed: () => context.go('/circulars'),
                        child: const Text('View All'),
                      ),
                    ],
                  ),
                ),

                if (circulars.isEmpty)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 16),
                    child: EmptyState(
                      title: 'No circulars found',
                      icon: Icons.description_outlined,
                    ),
                  )
                else
                  ...circulars.take(4).map((circ) {
                    return DocumentRow(
                      title: circ['title']?.toString() ?? '',
                      date: circ['created_date']?.toString(),
                      category: circ['category']?.toString(),
                      fileUrl: circ['file_url']?.toString(),
                      onTap: () {
                        final id = circ['id']?.toString() ?? '';
                        context.push('/circulars/$id');
                      },
                    );
                  }),

                const SizedBox(height: 20),

                // Section: State Leadership Highlight
                if (officeBearers.isNotEmpty) ...[
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'State Leadership',
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textDark,
                          ),
                        ),
                        TextButton(
                          onPressed: () => context.push('/organization'),
                          child: const Text('View All'),
                        ),
                      ],
                    ),
                  ),
                  SizedBox(
                    height: 140,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      itemCount: officeBearers.length,
                      itemBuilder: (ctx, idx) {
                        final ob = officeBearers[idx];
                        final name = ob['name']?.toString() ?? '';
                        final desig = ob['designation']?.toString() ?? '';
                        final photo = ob['photo_url']?.toString();

                        return Container(
                          width: 110,
                          margin: const EdgeInsets.only(right: 12),
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: AppColors.white,
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: AppColors.border),
                          ),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(25),
                                child: photo != null && photo.isNotEmpty
                                    ? CachedNetworkImage(
                                        imageUrl: photo,
                                        width: 50,
                                        height: 50,
                                        fit: BoxFit.cover,
                                        placeholder: (c, u) => Container(
                                          color: AppColors.bgLight,
                                        ),
                                        errorWidget: (c, u, e) =>
                                            const Icon(Icons.person, size: 40),
                                      )
                                    : const Icon(
                                        Icons.person,
                                        size: 40,
                                        color: AppColors.primary,
                                      ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                name,
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textDark,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              Text(
                                desig,
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                  fontSize: 10,
                                  color: AppColors.primary,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildQuickAction(
    BuildContext context, {
    required IconData icon,
    required String title,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(10),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 14),
          decoration: BoxDecoration(
            color: color.withOpacity(0.08),
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: color.withOpacity(0.2)),
          ),
          child: Column(
            children: [
              Icon(icon, color: color, size: 24),
              const SizedBox(height: 6),
              Text(
                title,
                style: TextStyle(
                  color: color,
                  fontWeight: FontWeight.w600,
                  fontSize: 12,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
