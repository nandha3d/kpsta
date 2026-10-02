import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final albumDetailProvider =
    FutureProvider.family.autoDispose<Map<String, dynamic>, String>(
        (ref, id) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get('${ApiConstants.gallery}/$id');
  return response['data'] as Map<String, dynamic>;
});

class GalleryDetailScreen extends ConsumerWidget {
  final String id;

  const GalleryDetailScreen({super.key, required this.id});

  void _showFullScreen(BuildContext context, String url, String? caption) {
    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        backgroundColor: Colors.black87,
        insetPadding: EdgeInsets.zero,
        child: Stack(
          alignment: Alignment.center,
          children: [
            InteractiveViewer(
              child: CachedNetworkImage(
                imageUrl: url,
                fit: BoxFit.contain,
              ),
            ),
            Positioned(
              top: 40,
              right: 20,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 28),
                onPressed: () => Navigator.of(ctx).pop(),
              ),
            ),
            if (caption != null && caption.isNotEmpty)
              Positioned(
                bottom: 30,
                left: 20,
                right: 20,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  color: Colors.black54,
                  child: Text(
                    caption,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.white, fontSize: 14),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final albumAsync = ref.watch(albumDetailProvider(id));

    return Scaffold(
      appBar: const AppTopBar(title: 'Album Photos'),
      body: albumAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(albumDetailProvider(id)),
        ),
        data: (data) {
          final album = (data['album'] as Map<String, dynamic>?) ?? data;
          final photos =
              (data['images'] ?? data['photos']) as List<dynamic>? ?? [];
          final albumTitle =
              (album['name'] ?? album['title'])?.toString() ?? 'Photos';

          if (photos.isEmpty) {
            return const EmptyState(
              title: 'No photos in this album',
              icon: Icons.photo_library_outlined,
            );
          }

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(16),
                child: Text(
                  albumTitle,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
              ),
              Expanded(
                child: GridView.builder(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 3,
                    crossAxisSpacing: 8,
                    mainAxisSpacing: 8,
                  ),
                  itemCount: photos.length,
                  itemBuilder: (ctx, idx) {
                    final photo = photos[idx];
                    final photoUrl = photo['image_url']?.toString() ??
                        photo['url']?.toString() ??
                        '';
                    final caption = photo['title']?.toString() ??
                        photo['caption']?.toString();

                    return InkWell(
                      onTap: () => _showFullScreen(context, photoUrl, caption),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(8),
                        child: CachedNetworkImage(
                          imageUrl: photoUrl,
                          fit: BoxFit.cover,
                          placeholder: (c, u) =>
                              Container(color: AppColors.bgLight),
                          errorWidget: (c, u, e) => Container(
                            color: AppColors.bgLight,
                            child: const Icon(Icons.broken_image),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
