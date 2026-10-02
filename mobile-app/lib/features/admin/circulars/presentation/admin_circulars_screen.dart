import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminCircularsListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminCirculars);
  return response['data'] as List<dynamic>? ?? [];
});

class AdminCircularsScreen extends ConsumerWidget {
  const AdminCircularsScreen({super.key});

  Future<void> _deleteCircular(
    BuildContext context,
    WidgetRef ref,
    String id,
    String title,
  ) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: 'Delete Circular',
      message:
          'Are you sure you want to delete "$title"? This removes the document from active listings.',
      confirmLabel: 'Delete',
      isDestructive: true,
    );

    if (confirmed) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminCirculars}/$id');
        ref.refresh(adminCircularsListProvider);
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Circular deleted successfully'),
              backgroundColor: AppColors.success,
            ),
          );
        }
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
    final circularsAsync = ref.watch(adminCircularsListProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage Order Circulars'),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/admin/circulars/new'),
        backgroundColor: AppColors.primary,
        foregroundColor: AppColors.white,
        icon: const Icon(Icons.add),
        label: const Text('Add Circular'),
      ),
      body: circularsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminCircularsListProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              title: 'No circulars found',
              icon: Icons.description_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminCircularsListProvider),
            child: ListView.builder(
              padding: const EdgeInsets.only(bottom: 80),
              itemCount: items.length,
              itemBuilder: (ctx, idx) {
                final item = items[idx];
                final id = item['id']?.toString() ?? '';
                final title = item['title']?.toString() ?? '';
                final isPublished = item['status'] == 1 ||
                    item['status'] == '1' ||
                    item['is_published'] == true;

                return DocumentRow(
                  title: title,
                  date: item['created_date']?.toString(),
                  category: item['category']?.toString(),
                  fileUrl: item['file_url']?.toString(),
                  isPublished: isPublished,
                  onEdit: () => context.push('/admin/circulars/edit/$id'),
                  onDelete: () => _deleteCircular(context, ref, id, title),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
