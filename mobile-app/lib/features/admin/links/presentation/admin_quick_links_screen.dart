import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminQuickLinksProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminQuickLinks);
  return (response['data'] as List? ?? []);
});

class AdminQuickLinksScreen extends ConsumerWidget {
  const AdminQuickLinksScreen({super.key});

  Future<void> _deleteLink(BuildContext context, WidgetRef ref, int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Quick Link'),
        content: const Text('Are you sure you want to delete this link?'),
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
        await client.delete('${ApiConstants.adminQuickLinks}/$id');
        ref.refresh(adminQuickLinksProvider);
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

  void _showAddDialog(BuildContext context, WidgetRef ref) {
    final titleController = TextEditingController();
    final urlController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Add Quick Link'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: titleController,
              decoration: const InputDecoration(labelText: 'Link Title *'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: urlController,
              decoration: const InputDecoration(
                labelText: 'URL / Target *',
                hintText: 'https://...',
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              final title = titleController.text.trim();
              final url = urlController.text.trim();
              if (title.isEmpty || url.isEmpty) return;
              Navigator.pop(ctx);
              try {
                final client = ref.read(apiClientProvider);
                await client.post(
                  ApiConstants.adminQuickLinks,
                  data: {
                    'title': title,
                    'url': url,
                    'is_publish': 1,
                  },
                );
                ref.refresh(adminQuickLinksProvider);
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
            },
            child: const Text('Add Link'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final linksAsync = ref.watch(adminQuickLinksProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage Quick Links'),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showAddDialog(context, ref),
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: AppColors.white),
      ),
      body: linksAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, _) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminQuickLinksProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.link_off,
              title: 'No Quick Links Found',
              subtitle: 'Add department portals and online services.',
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminQuickLinksProvider),
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (context, idx) {
                final item = items[idx] as Map<String, dynamic>;
                final id = item['id'] as int;
                final title = item['title']?.toString() ?? '';
                final url = item['url']?.toString() ?? '';

                return Card(
                  child: ListTile(
                    leading: const CircleAvatar(
                      backgroundColor: AppColors.primaryLight,
                      child: Icon(Icons.link, color: AppColors.primary, size: 20),
                    ),
                    title: Text(
                      title,
                      style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                    ),
                    subtitle: Text(
                      url,
                      style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    trailing: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.open_in_new, size: 18),
                          onPressed: () {
                            final uri = Uri.tryParse(url);
                            if (uri != null) launchUrl(uri);
                          },
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete_outline,
                              color: AppColors.error, size: 20),
                          onPressed: () => _deleteLink(context, ref, id),
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
