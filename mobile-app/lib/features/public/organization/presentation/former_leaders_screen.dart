import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/person_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final formerLeadersProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.formerLeaders);
  return response['data'] as List<dynamic>? ?? [];
});

class FormerLeadersScreen extends ConsumerWidget {
  const FormerLeadersScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final leadersAsync = ref.watch(formerLeadersProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Former Association Leaders'),
      body: leadersAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(formerLeadersProvider),
        ),
        data: (leaders) {
          if (leaders.isEmpty) {
            return const EmptyState(
              title: 'No historical leader records found',
              icon: Icons.history_edu_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(formerLeadersProvider),
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: 12),
              itemCount: leaders.length,
              itemBuilder: (ctx, idx) {
                final l = leaders[idx];
                return PersonCard(
                  name: l['name']?.toString() ?? '',
                  designation: l['designation']?.toString() ?? 'Former Leader',
                  photoUrl: l['photo_url']?.toString(),
                  tenure: l['tenure']?.toString() ?? l['year']?.toString(),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
