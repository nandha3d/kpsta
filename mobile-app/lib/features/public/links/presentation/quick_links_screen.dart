import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final quickLinksProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.quickLinks);
  return response['data'] as List<dynamic>? ?? [];
});

class QuickLinksScreen extends ConsumerWidget {
  const QuickLinksScreen({super.key});

  Future<void> _openLink(String url) async {
    final uri = Uri.parse(url.startsWith('http') ? url : 'https://$url');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final linksAsync = ref.watch(quickLinksProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Government & Educational Links'),
      body: linksAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(quickLinksProvider),
        ),
        data: (links) {
          if (links.isEmpty) {
            return const EmptyState(
              title: 'No quick links available',
              icon: Icons.link_off_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(quickLinksProvider),
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: 12),
              itemCount: links.length,
              itemBuilder: (ctx, idx) {
                final link = links[idx];
                final title = link['title']?.toString() ?? 'Portal';
                final url = link['url']?.toString() ?? '';

                return Card(
                  margin:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.indigo.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.open_in_new,
                        color: AppColors.indigo,
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
                    onTap: () => _openLink(url),
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
