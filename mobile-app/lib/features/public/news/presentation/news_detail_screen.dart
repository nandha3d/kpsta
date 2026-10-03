import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final newsDetailProvider =
    FutureProvider.family.autoDispose<Map<String, dynamic>, String>(
        (ref, id) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get('${ApiConstants.news}/$id');
  return response['data'] as Map<String, dynamic>;
});

class NewsDetailScreen extends ConsumerWidget {
  final String id;

  const NewsDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detailAsync = ref.watch(newsDetailProvider(id));

    return Scaffold(
      appBar: const AppTopBar(title: 'News Article'),
      body: detailAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(newsDetailProvider(id)),
        ),
        data: (item) {
          final title = item['title']?.toString() ?? '';
          final date = item['date']?.toString() ?? '';
          final content = item['description']?.toString() ??
              item['content']?.toString() ??
              '';
          final imageUrl = item['image_url']?.toString();

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (imageUrl != null && imageUrl.isNotEmpty) ...[
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: AppNetworkImage(
                      imageUrl: imageUrl,
                      width: double.infinity,
                      height: 220,
                      fit: BoxFit.cover,
                      placeholder: Container(
                        color: AppColors.bgLight,
                        height: 220,
                      ),
                      errorWidget: const SizedBox.shrink(),
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                    height: 1.3,
                  ),
                ),
                if (date.isNotEmpty) ...[
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      const Icon(
                        Icons.calendar_today_outlined,
                        size: 14,
                        color: AppColors.textMuted,
                      ),
                      const SizedBox(width: 6),
                      Text(
                        date,
                        style: const TextStyle(
                          fontSize: 13,
                          color: AppColors.textMuted,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
                const Divider(height: 30, color: AppColors.border),
                Text(
                  // Strip basic HTML tags if present for clean mobile presentation
                  content.replaceAll(RegExp(r'<[^>]*>'), '').trim(),
                  style: const TextStyle(
                    fontSize: 15,
                    color: AppColors.text,
                    height: 1.6,
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
