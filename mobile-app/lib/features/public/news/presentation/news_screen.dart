import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';

final newsListFamilyProvider =
    FutureProvider.family.autoDispose<List<dynamic>, String>((ref, query) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.news,
    queryParameters: {
      if (query.isNotEmpty) 'q': query,
      'limit': 50,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

class NewsScreen extends ConsumerStatefulWidget {
  const NewsScreen({super.key});

  @override
  ConsumerState<NewsScreen> createState() => _NewsScreenState();
}

class _NewsScreenState extends ConsumerState<NewsScreen> {
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final newsAsync = ref.watch(newsListFamilyProvider(_searchQuery));

    return Scaffold(
      appBar: const AppTopBar(
        title: 'News & Announcements',
        showBackButton: false,
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search news headlines...',
            onChanged: (q) {
              setState(() => _searchQuery = q.trim());
            },
          ),
          Expanded(
            child: newsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(newsListFamilyProvider(_searchQuery)),
              ),
              data: (newsItems) {
                if (newsItems.isEmpty) {
                  return const EmptyState(
                    title: 'No news found',
                    message: 'Try adjusting your search criteria.',
                    icon: Icons.newspaper_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(newsListFamilyProvider(_searchQuery)),
                  child: ListView.builder(
                    padding: const EdgeInsets.only(bottom: 20),
                    itemCount: newsItems.length,
                    itemBuilder: (ctx, idx) {
                      final item = newsItems[idx];
                      final id = item['id']?.toString() ?? '';
                      final title = item['title']?.toString() ?? '';
                      final date =
                          (item['date'] ?? item['published_at'])?.toString() ??
                              '';
                      final imageUrl =
                          (item['image_url'] ?? item['photo_url'])?.toString();

                      return Card(
                        margin: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 6,
                        ),
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
                                    width: 80,
                                    height: 80,
                                    fit: BoxFit.cover,
                                    placeholder: Container(
                                      width: 80,
                                      height: 80,
                                      color: AppColors.bgLight,
                                    ),
                                    errorWidget: Container(
                                      width: 80,
                                      height: 80,
                                      color: AppColors.primary.withValues(alpha: 0.08),
                                      child: const Icon(
                                        Icons.newspaper,
                                        color: AppColors.primary,
                                        size: 32,
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 14),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        title,
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w600,
                                          color: AppColors.textDark,
                                          height: 1.3,
                                        ),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                      const SizedBox(height: 8),
                                      if (date.isNotEmpty)
                                        Row(
                                          children: [
                                            const Icon(
                                              Icons.calendar_today_outlined,
                                              size: 13,
                                              color: AppColors.textMuted,
                                            ),
                                            const SizedBox(width: 4),
                                            Text(
                                              date,
                                              style: const TextStyle(
                                                fontSize: 12,
                                                color: AppColors.textMuted,
                                              ),
                                            ),
                                          ],
                                        ),
                                    ],
                                  ),
                                ),
                                const Icon(
                                  Icons.chevron_right,
                                  color: AppColors.textLight,
                                ),
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
