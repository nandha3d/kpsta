import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/page_hero_banner.dart';
import '../../../../core/widgets/ribbon_header.dart';
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
      backgroundColor: Colors.white,
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
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                const PageHeroBanner(
                  title: 'FORMER LEADERS',
                  height: 100,
                ),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16),
                  child: RibbonHeader(
                    title: 'Former State Leadership',
                    color: Color(0xFF016D77),
                    margin: EdgeInsets.only(top: 18, bottom: 12),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  child: Wrap(
                    alignment: WrapAlignment.spaceEvenly,
                    spacing: 12,
                    runSpacing: 16,
                    children: leaders.map((l) {
                      final positions = l['positions'] as List<dynamic>? ?? [];
                      final posStr = positions.isNotEmpty
                          ? positions.map((p) => "${p['designation'] ?? ''} (${p['year'] ?? ''})").join(', ')
                          : (l['designation']?.toString() ?? 'Former Leader');

                      return BearerCard(
                        name: l['name']?.toString() ?? '',
                        designation: posStr,
                        photoUrl: l['photo_url']?.toString(),
                        phone: l['phone']?.toString(),
                        year: l['year']?.toString() ?? l['tenure']?.toString(),
                        width: 155,
                        photoHeight: 160,
                      );
                    }).toList(),
                  ),
                ),
                const SizedBox(height: 36),
                const KpstaFooter(),
              ],
            ),
          );
        },
      ),
    );
  }
}
