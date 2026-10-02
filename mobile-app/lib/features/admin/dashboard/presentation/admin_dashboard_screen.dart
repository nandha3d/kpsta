import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/statistic_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminDashboardProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminDashboard);
  return response['data'] as Map<String, dynamic>;
});

class AdminDashboardScreen extends ConsumerWidget {
  const AdminDashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dashboardAsync = ref.watch(adminDashboardProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: 'Administrator Dashboard',
        actions: [
          IconButton(
            icon: const Icon(Icons.home_outlined),
            tooltip: 'Public Site',
            onPressed: () => context.go('/home'),
          ),
        ],
      ),
      body: dashboardAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminDashboardProvider),
        ),
        data: (counts) {
          final newsCount = (counts['news_count'] ?? counts['total_news'])?.toString() ?? '0';
          final circularsCount = (counts['circulars_count'] ?? counts['total_circulars'])?.toString() ?? '0';
          final downloadsCount = (counts['downloads_count'] ?? counts['total_downloads'])?.toString() ?? '0';
          final bearersCount = (counts['office_bearers_count'] ?? counts['total_office_bearers'])?.toString() ?? '0';
          final albumsCount = (counts['albums_count'] ?? counts['total_gallery_albums'])?.toString() ?? '0';
          final flashCount = (counts['flash_news_count'] ?? counts['total_flash_news'])?.toString() ?? '0';
          final sliderCount = (counts['sliders_count'] ?? counts['total_sliders'])?.toString() ?? '0';

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminDashboardProvider),
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                // Welcome Card
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: AppColors.primaryDark,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    children: [
                      Icon(
                        Icons.shield_outlined,
                        color: AppColors.white,
                        size: 32,
                      ),
                      SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Content Management System',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                                color: AppColors.white,
                              ),
                            ),
                            SizedBox(height: 2),
                            Text(
                              'Authorized changes take effect immediately across web & mobile.',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.white70,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Metrics Grid (Data direct from API)
                const Text(
                  'Overview & Live Counts',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 12),

                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  childAspectRatio: 1.15,
                  children: [
                    StatisticCard(
                      title: 'News Articles',
                      value: newsCount,
                      icon: Icons.newspaper,
                      color: AppColors.primary,
                      onTap: () => context.push('/admin/news'),
                    ),
                    StatisticCard(
                      title: 'Order Circulars',
                      value: circularsCount,
                      icon: Icons.description,
                      color: AppColors.indigo,
                      onTap: () => context.push('/admin/circulars'),
                    ),
                    StatisticCard(
                      title: 'Downloads',
                      value: downloadsCount,
                      icon: Icons.download,
                      color: AppColors.orange,
                      onTap: () => context.push('/admin/downloads'),
                    ),
                    StatisticCard(
                      title: 'Office Bearers',
                      value: bearersCount,
                      icon: Icons.people,
                      color: AppColors.green,
                      onTap: () => context.push('/admin/office-bearers'),
                    ),
                    StatisticCard(
                      title: 'Photo Albums',
                      value: albumsCount,
                      icon: Icons.photo_library,
                      color: AppColors.indigoHover,
                      onTap: () => context.push('/admin/gallery'),
                    ),
                    StatisticCard(
                      title: 'Flash News',
                      value: flashCount,
                      icon: Icons.flash_on,
                      color: AppColors.gold,
                      onTap: () => context.push('/admin/flash-news'),
                    ),
                    StatisticCard(
                      title: 'Home Sliders',
                      value: sliderCount,
                      icon: Icons.view_carousel,
                      color: AppColors.purple,
                      onTap: () => context.push('/admin/sliders'),
                    ),
                  ],
                ),

                const SizedBox(height: 24),

                // Management Sections
                const Text(
                  'Manage Content & Modules',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 10),

                _buildAdminActionTile(
                  icon: Icons.article_outlined,
                  title: 'Publish / Manage News',
                  subtitle: 'Create articles, upload photos, set publish status',
                  onTap: () => context.push('/admin/news'),
                ),
                _buildAdminActionTile(
                  icon: Icons.assignment_outlined,
                  title: 'Manage Order Circulars',
                  subtitle: 'General, HSE, VHSE government orders and PDF attachments',
                  onTap: () => context.push('/admin/circulars'),
                ),
                _buildAdminActionTile(
                  icon: Icons.flash_on_outlined,
                  title: 'Flash News Ticker',
                  subtitle: 'Manage scrolling alerts and priority announcements',
                  onTap: () => context.push('/admin/flash-news'),
                ),
                _buildAdminActionTile(
                  icon: Icons.view_carousel_outlined,
                  title: 'Hero Sliders & Banners',
                  subtitle: 'Upload homepage carousels and header backgrounds',
                  onTap: () => context.push('/admin/sliders'),
                ),
                _buildAdminActionTile(
                  icon: Icons.photo_library_outlined,
                  title: 'Photo Gallery Albums',
                  subtitle: 'Manage events, albums, and multi-photo uploads',
                  onTap: () => context.push('/admin/gallery'),
                ),
                _buildAdminActionTile(
                  icon: Icons.folder_shared_outlined,
                  title: 'Manage Downloads & Forms',
                  subtitle: 'Software, Acts & Rules, Application forms',
                  onTap: () => context.push('/admin/downloads'),
                ),
                _buildAdminActionTile(
                  icon: Icons.group_outlined,
                  title: 'Manage Office Bearers',
                  subtitle: 'Add state officials, assign designations & districts',
                  onTap: () => context.push('/admin/office-bearers'),
                ),
                _buildAdminActionTile(
                  icon: Icons.link_outlined,
                  title: 'Quick Links & Portals',
                  subtitle: 'Department portals, circular shortcuts, government links',
                  onTap: () => context.push('/admin/quick-links'),
                ),
                _buildAdminActionTile(
                  icon: Icons.grade_outlined,
                  title: 'Result & Exam Links',
                  subtitle: 'SSLC, HSE, VHSE and scholarship result links',
                  onTap: () => context.push('/admin/result-links'),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildAdminActionTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.08),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: AppColors.primary, size: 22),
        ),
        title: Text(
          title,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.textDark,
          ),
        ),
        subtitle: Text(
          subtitle,
          style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
        ),
        trailing: const Icon(
          Icons.arrow_forward_ios,
          size: 14,
          color: AppColors.textLight,
        ),
        onTap: onTap,
      ),
    );
  }
}
