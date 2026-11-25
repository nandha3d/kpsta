import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/ribbon_header.dart';
import '../../../auth/presentation/auth_providers.dart';

final formerLeadersProvider =
    FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.formerLeaders, cacheDuration: const Duration(minutes: 5));
  return response['data'] as List<dynamic>? ?? [];
});

/// Standalone Screen for Former Leaders
class FormerLeadersScreen extends StatelessWidget {
  const FormerLeadersScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: Colors.transparent,
      appBar: AppTopBar(title: 'Former Association Leaders'),
      body: FormerLeadersView(),
    );
  }
}

/// Swipable Submenu View for Former Leaders
class FormerLeadersView extends ConsumerWidget {
  const FormerLeadersView({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final leadersAsync = ref.watch(formerLeadersProvider);

    return leadersAsync.when(
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
          child: LayoutBuilder(
            builder: (context, constraints) {
              final crossAxisCount = constraints.maxWidth < 600
                  ? 3
                  : (constraints.maxWidth < 900 ? 4 : 6);

              return ListView(
                padding: const EdgeInsets.only(bottom: 90),
                children: [
                  Container(
                    margin: const EdgeInsets.fromLTRB(14, 8, 14, 6),
                    padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 14),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF0F2C33), Color(0xFF01555E)],
                      ),
                      borderRadius: BorderRadius.circular(14),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.08),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: const Center(
                      child: Text(
                        'FORMER LEADERS',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 14,
                          letterSpacing: 0.8,
                        ),
                      ),
                    ),
                  ),
                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 14),
                    child: RibbonHeader(
                      title: 'Former State Leadership',
                      color: Color(0xFF016D77),
                      margin: EdgeInsets.only(top: 8, bottom: 4),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 8),
                    child: Column(
                      children: [
                        for (var i = 0; i < leaders.length; i += crossAxisCount)
                          Padding(
                            padding: const EdgeInsets.only(bottom: 6),
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: List.generate(crossAxisCount, (colIdx) {
                                final itemIdx = i + colIdx;
                                if (itemIdx < leaders.length) {
                                  final l = leaders[itemIdx];
                                  final positions =
                                      l['positions'] as List<dynamic>? ?? [];
                                  final posStr = positions.isNotEmpty
                                      ? positions
                                          .map((p) =>
                                              "${p['designation'] ?? ''} (${p['year'] ?? ''})")
                                          .join(', ')
                                      : (l['designation']?.toString() ??
                                          'Former Leader');

                                  return Expanded(
                                    child: Padding(
                                      padding: const EdgeInsets.symmetric(horizontal: 3),
                                      child: BearerCard(
                                        name: l['name']?.toString() ?? '',
                                        designation: posStr,
                                        photoUrl: l['photo_url']?.toString(),
                                        phone: l['phone']?.toString(),
                                        year: l['year']?.toString() ??
                                            l['tenure']?.toString(),
                                        aspectRatio: 1.0,
                                      ),
                                    ),
                                  );
                                } else {
                                  return const Expanded(child: SizedBox());
                                }
                              }),
                            ),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  const KpstaFooter(),
                ],
              );
            },
          ),
        );
      },
    );
  }
}
