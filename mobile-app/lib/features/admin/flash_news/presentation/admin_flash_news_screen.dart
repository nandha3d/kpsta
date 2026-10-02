import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/status_badge.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminFlashNewsProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminFlashNews);
  return (response['data'] as List? ?? []);
});

class AdminFlashNewsScreen extends ConsumerWidget {
  const AdminFlashNewsScreen({super.key});

  Future<void> _togglePublish(
      BuildContext context, WidgetRef ref, int id, bool currentStatus) async {
    try {
      final client = ref.read(apiClientProvider);
      await client.post(
        '${ApiConstants.adminFlashNews}/$id/publish',
        data: {'is_publish': !currentStatus ? 1 : 0},
      );
      ref.refresh(adminFlashNewsProvider);
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

  Future<void> _deleteItem(BuildContext context, WidgetRef ref, int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Flash News'),
        content: const Text('Are you sure you want to delete this alert?'),
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
        await client.delete('${ApiConstants.adminFlashNews}/$id');
        ref.refresh(adminFlashNewsProvider);
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
    final textController = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Create Flash News'),
        content: TextField(
          controller: textController,
          maxLines: 3,
          decoration: const InputDecoration(
            labelText: 'Alert Text / Headline *',
            hintText: 'e.g. State conference registration extended to 15th',
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              final text = textController.text.trim();
              if (text.isEmpty) return;
              Navigator.pop(ctx);
              try {
                final client = ref.read(apiClientProvider);
                await client.post(
                  ApiConstants.adminFlashNews,
                  data: {
                    'description': text,
                    'is_publish': 1,
                  },
                );
                ref.refresh(adminFlashNewsProvider);
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
            child: const Text('Publish Alert'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final flashAsync = ref.watch(adminFlashNewsProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage Flash News'),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showAddDialog(context, ref),
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: AppColors.white),
      ),
      body: flashAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, _) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminFlashNewsProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.flash_on_outlined,
              title: 'No Flash News Found',
              subtitle: 'Tap the + button to create a new urgent notification ticker.',
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminFlashNewsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (context, idx) {
                final item = items[idx] as Map<String, dynamic>;
                final id = item['id'] as int;
                final desc = item['description']?.toString() ?? '';
                final isPublished = item['is_publish'] == true;

                return Card(
                  child: Padding(
                    padding: const EdgeInsets.all(12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            StatusBadge(
                              label: isPublished ? 'Live Ticker' : 'Unpublished',
                              isSuccess: isPublished,
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
                              tooltip: isPublished ? 'Unpublish' : 'Publish',
                              onPressed: () =>
                                  _togglePublish(context, ref, id, isPublished),
                            ),
                            IconButton(
                              icon: const Icon(Icons.delete_outline,
                                  color: AppColors.error),
                              tooltip: 'Delete',
                              onPressed: () => _deleteItem(context, ref, id),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
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
                );
              },
            ),
          );
        },
      ),
    );
  }
}
