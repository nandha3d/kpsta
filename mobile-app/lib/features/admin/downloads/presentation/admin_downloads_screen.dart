import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminDownloadsListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminDownloads);
  return response['data'] as List<dynamic>? ?? [];
});

class AdminDownloadsScreen extends ConsumerWidget {
  const AdminDownloadsScreen({super.key});

  Future<void> _deleteDownload(
    BuildContext context,
    WidgetRef ref,
    String id,
    String title,
  ) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: 'Delete Download File',
      message: 'Are you sure you want to remove "$title"?',
      confirmLabel: 'Delete',
      isDestructive: true,
    );

    if (confirmed) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminDownloads}/$id');
        ref.refresh(adminDownloadsListProvider);
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('File removed successfully'),
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
    final downloadsAsync = ref.watch(adminDownloadsListProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage Download Resources'),
      body: downloadsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminDownloadsListProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              title: 'No downloadable files found',
              icon: Icons.folder_open_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminDownloadsListProvider),
            child: ListView.builder(
              itemCount: items.length,
              itemBuilder: (ctx, idx) {
                final item = items[idx];
                final id = item['id']?.toString() ?? '';
                final title = item['title']?.toString() ?? '';

                return DocumentRow(
                  title: title,
                  date: item['created_date']?.toString(),
                  category: item['category']?.toString(),
                  fileUrl: item['file_url']?.toString(),
                  onDelete: () => _deleteDownload(context, ref, id, title),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
