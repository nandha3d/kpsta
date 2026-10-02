import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final whatsNewListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.membershipWhatsNew);
  return response['data'] as List<dynamic>? ?? [];
});

class MembershipWhatsNewScreen extends ConsumerWidget {
  const MembershipWhatsNewScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final whatsNewAsync = ref.watch(whatsNewListProvider);

    return Scaffold(
      appBar: const AppTopBar(title: "Internal Notices (What's New)"),
      body: whatsNewAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(whatsNewListProvider),
        ),
        data: (items) {
          if (items.isEmpty) {
            return const EmptyState(
              title: 'No internal notices found',
              message:
                  'Notices issued to your member group will be posted here.',
              icon: Icons.campaign_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(whatsNewListProvider),
            child: ListView.builder(
              itemCount: items.length,
              itemBuilder: (ctx, idx) {
                final item = items[idx];
                return DocumentRow(
                  title: item['title']?.toString() ?? '',
                  date: item['created_date']?.toString(),
                  category: 'Member Notice',
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
