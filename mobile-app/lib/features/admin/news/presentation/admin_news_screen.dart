import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../../core/widgets/status_badge.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminNewsListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminNews);
  return response['data'] as List<dynamic>? ?? [];
});

class AdminNewsScreen extends ConsumerStatefulWidget {
  const AdminNewsScreen({super.key});

  @override
  ConsumerState<AdminNewsScreen> createState() => _AdminNewsScreenState();
}

class _AdminNewsScreenState extends ConsumerState<AdminNewsScreen> {
  String _searchQuery = '';

  Future<void> _deleteNews(String id, String title) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: 'Delete Article',
      message:
          'Are you sure you want to permanently delete "$title"? This action cannot be undone.',
      confirmLabel: 'Delete',
      isDestructive: true,
    );

    if (confirmed) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminNews}/$id');
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Article deleted successfully'),
              backgroundColor: AppColors.success,
            ),
          );
          ref.refresh(adminNewsListProvider);
        }
      } catch (e) {
        if (mounted) {
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
  Widget build(BuildContext context) {
    final newsAsync = ref.watch(adminNewsListProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage News'),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/admin/news/new'),
        backgroundColor: AppColors.primary,
        foregroundColor: AppColors.white,
        icon: const Icon(Icons.add),
        label: const Text('Add Article'),
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Filter articles...',
            onChanged: (q) => setState(() => _searchQuery = q.toLowerCase()),
          ),
          Expanded(
            child: newsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(adminNewsListProvider),
              ),
              data: (items) {
                final filtered = items.where((it) {
                  final title = it['title']?.toString().toLowerCase() ?? '';
                  return title.contains(_searchQuery);
                }).toList();

                if (filtered.isEmpty) {
                  return const EmptyState(
                    title: 'No news articles found',
                    icon: Icons.newspaper_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async => ref.refresh(adminNewsListProvider),
                  child: ListView.builder(
                    padding: const EdgeInsets.only(bottom: 80),
                    itemCount: filtered.length,
                    itemBuilder: (ctx, idx) {
                      final item = filtered[idx];
                      final id = item['id']?.toString() ?? '';
                      final title = item['title']?.toString() ?? '';
                      final date = item['created_date']?.toString() ??
                          item['date']?.toString();
                      final isPublished = item['status'] == 1 ||
                          item['status'] == '1' ||
                          item['is_published'] == true;

                      return Card(
                        margin: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 6,
                        ),
                        child: ListTile(
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
                          subtitle: Row(
                            children: [
                              if (date != null) ...[
                                Text(
                                  date,
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: AppColors.textMuted,
                                  ),
                                ),
                                const SizedBox(width: 8),
                              ],
                              StatusBadge(isPublished: isPublished),
                            ],
                          ),
                          trailing: PopupMenuButton<String>(
                            icon: const Icon(Icons.more_vert),
                            onSelected: (val) {
                              if (val == 'edit') {
                                context.push('/admin/news/edit/$id');
                              } else if (val == 'delete') {
                                _deleteNews(id, title);
                              }
                            },
                            itemBuilder: (c) => [
                              const PopupMenuItem(
                                value: 'edit',
                                child: Row(
                                  children: [
                                    Icon(Icons.edit_outlined, size: 18),
                                    SizedBox(width: 8),
                                    Text('Edit'),
                                  ],
                                ),
                              ),
                              const PopupMenuItem(
                                value: 'delete',
                                child: Row(
                                  children: [
                                    Icon(Icons.delete_outline,
                                        size: 18, color: AppColors.error),
                                    SizedBox(width: 8),
                                    Text('Delete',
                                        style:
                                            TextStyle(color: AppColors.error)),
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
          ),
        ],
      ),
    );
  }
}
