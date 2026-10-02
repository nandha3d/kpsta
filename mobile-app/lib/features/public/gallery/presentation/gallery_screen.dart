import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final galleryAlbumsProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.gallery);
  return response['data'] as List<dynamic>? ?? [];
});

class GalleryScreen extends ConsumerWidget {
  const GalleryScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final albumsAsync = ref.watch(galleryAlbumsProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Photo Gallery'),
      body: albumsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(galleryAlbumsProvider),
        ),
        data: (albums) {
          if (albums.isEmpty) {
            return const EmptyState(
              title: 'No gallery albums found',
              icon: Icons.photo_library_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(galleryAlbumsProvider),
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
                childAspectRatio: 0.85,
              ),
              itemCount: albums.length,
              itemBuilder: (ctx, idx) {
                final album = albums[idx];
                final id = album['id']?.toString() ?? '';
                final name = album['name']?.toString() ??
                    album['title']?.toString() ??
                    'Album';
                final coverUrl = album['cover_image']?.toString() ??
                    album['cover_url']?.toString() ??
                    album['image_url']?.toString();
                final count =
                    (album['image_count'] ?? album['photo_count'])?.toString() ??
                        '0';

                return InkWell(
                  onTap: () => context.push('/gallery/$id'),
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    decoration: BoxDecoration(
                      color: AppColors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: ClipRRect(
                            borderRadius: const BorderRadius.vertical(
                              top: Radius.circular(11),
                            ),
                            child: coverUrl != null && coverUrl.isNotEmpty
                                ? CachedNetworkImage(
                                    imageUrl: coverUrl,
                                    width: double.infinity,
                                    fit: BoxFit.cover,
                                    placeholder: (c, u) => Container(
                                      color: AppColors.bgLight,
                                    ),
                                    errorWidget: (c, u, e) => Container(
                                      color: AppColors.bgLight,
                                      child: const Icon(
                                        Icons.image_outlined,
                                        size: 32,
                                        color: AppColors.textLight,
                                      ),
                                    ),
                                  )
                                : Container(
                                    color: AppColors.primary.withOpacity(0.08),
                                    child: const Center(
                                      child: Icon(
                                        Icons.photo_library,
                                        color: AppColors.primary,
                                        size: 32,
                                      ),
                                    ),
                                  ),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.all(10),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                name,
                                style: const TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.textDark,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '$count Photos',
                                style: const TextStyle(
                                  fontSize: 11,
                                  color: AppColors.textMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
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
