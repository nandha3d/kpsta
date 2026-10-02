import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final examResultsProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.examResults);
  return response['data'] as List<dynamic>? ?? [];
});

class ResultsScreen extends ConsumerWidget {
  const ResultsScreen({super.key});

  Future<void> _openResult(String url) async {
    final uri = Uri.parse(url.startsWith('http') ? url : 'https://$url');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final resultsAsync = ref.watch(examResultsProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Exam Results Portals'),
      body: resultsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(examResultsProvider),
        ),
        data: (links) {
          if (links.isEmpty) {
            return const EmptyState(
              title: 'No exam result portals listed',
              message:
                  'Official examination portals (SSLC, Plus Two, KTET) will appear here.',
              icon: Icons.school_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(examResultsProvider),
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: 12),
              itemCount: links.length,
              itemBuilder: (ctx, idx) {
                final item = links[idx];
                final title = item['title']?.toString() ?? 'Examination';
                final url = item['url']?.toString() ?? '';

                return Card(
                  margin:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.orange.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.grade_outlined,
                        color: AppColors.orange,
                        size: 20,
                      ),
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
                      url,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.textMuted,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    trailing: const Icon(
                      Icons.arrow_forward_ios,
                      size: 14,
                      color: AppColors.textLight,
                    ),
                    onTap: () => _openResult(url),
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
