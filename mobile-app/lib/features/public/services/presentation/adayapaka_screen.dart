import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final adayapakaListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adayapaka);
  return response['data'] as List<dynamic>? ?? [];
});

class AdayapakaScreen extends ConsumerWidget {
  const AdayapakaScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final listAsync = ref.watch(adayapakaListProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Adayapaka Sabham'),
      body: listAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adayapakaListProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              title: 'No publications found',
              message:
                  'Adayapaka Sabham editions and circulars will be displayed here.',
              icon: Icons.menu_book_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adayapakaListProvider),
            child: ListView.builder(
              itemCount: items.length,
              itemBuilder: (ctx, idx) {
                final item = items[idx];
                return DocumentRow(
                  title: item['title']?.toString() ?? '',
                  date: item['created_date']?.toString(),
                  category: 'Magazine Edition',
                  fileUrl: item['file_url']?.toString(),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
