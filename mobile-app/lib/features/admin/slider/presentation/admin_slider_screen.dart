import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/status_badge.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminSlidersProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminSliders);
  return (response['data'] as List? ?? []);
});

class AdminSliderScreen extends ConsumerWidget {
  const AdminSliderScreen({super.key});

  Future<void> _togglePublish(
      BuildContext context, WidgetRef ref, int id, bool currentStatus) async {
    try {
      final client = ref.read(apiClientProvider);
      await client.post(
        '${ApiConstants.adminSliders}/$id/publish',
        data: {'is_publish': !currentStatus ? 1 : 0},
      );
      ref.refresh(adminSlidersProvider);
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceAll('ApiException: ', '')),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  Future<void> _deleteSlider(BuildContext context, WidgetRef ref, int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Slider'),
        content: const Text('Are you sure you want to remove this slider banner?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Delete'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminSliders}/$id');
        ref.refresh(adminSlidersProvider);
      } catch (e) {
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(e.toString().replaceAll('ApiException: ', '')),
              backgroundColor: AppColors.error,
            ),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sliderAsync = ref.watch(adminSlidersProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Hero Sliders & Banners'),
      body: sliderAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, _) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminSlidersProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.view_carousel_outlined,
              title: 'No Sliders Configured',
              subtitle: 'Upload homepage carousels to showcase state initiatives.',
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminSlidersProvider),
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: 12),
              itemBuilder: (context, idx) {
                final item = items[idx] as Map<String, dynamic>;
                final id = item['id'] as int;
                final desc = item['description']?.toString() ?? 'Home Slider Banner';
                final imgUrl = item['image_url']?.toString();
                final isPublished = item['is_publish'] == true;
                final showOnHome = item['show_on_home'] == true;

                return Card(
                  clipBehavior: Clip.antiAlias,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      AppNetworkImage(
                        imageUrl: imgUrl,
                        height: 160,
                        width: double.infinity,
                        fit: BoxFit.cover,
                        placeholder: Container(
                          height: 160,
                          color: AppColors.backgroundSecondary,
                          child: const Center(
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                        ),
                        errorWidget: Container(
                          height: 160,
                          color: AppColors.backgroundSecondary,
                          child: const Icon(Icons.broken_image, size: 40),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                StatusBadge(
                                  label: isPublished ? 'Published' : 'Hidden',
                                  isPublished: isPublished,
                                ),
                                const SizedBox(width: 8),
                                if (showOnHome)
                                  const StatusBadge(
                                    label: 'Home Active',
                                    isPublished: true,
                                  ),
                                const Spacer(),
                                IconButton(
                                  icon: Icon(
                                    isPublished
                                        ? Icons.visibility_outlined
                                        : Icons.visibility_off_outlined,
                                    color: isPublished
                                        ? AppColors.green
                                        : AppColors.textMuted,
                                  ),
                                  onPressed: () =>
                                      _togglePublish(context, ref, id, isPublished),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline,
                                      color: AppColors.error),
                                  onPressed: () => _deleteSlider(context, ref, id),
                                ),
                              ],
                            ),
                            const SizedBox(height: 6),
                            Text(
                              desc,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textDark,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
