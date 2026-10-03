import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
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

final officeBearersProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.officeBearers);
  return response['data'] as Map<String, dynamic>;
});

class OrganizationScreen extends ConsumerWidget {
  const OrganizationScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final bearersAsync = ref.watch(officeBearersProvider);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppTopBar(
        title: 'State Office Bearers',
        actions: [
          IconButton(
            icon: const Icon(Icons.history_edu),
            tooltip: 'Former Leaders',
            onPressed: () => context.push('/organization/former-leaders'),
          ),
          IconButton(
            icon: const Icon(Icons.map_outlined),
            tooltip: 'Districts',
            onPressed: () => context.push('/organization/districts'),
          ),
        ],
      ),
      body: bearersAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(officeBearersProvider),
        ),
        data: (data) {
          final sections = data['sections'] as Map<String, dynamic>? ?? {};

          if (sections.isEmpty) {
            return const EmptyState(
              title: 'No office bearers found',
              icon: Icons.people_outline,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(officeBearersProvider),
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                // Interior Hero Banner
                const PageHeroBanner(
                  title: 'STATE OFFICE BEARERS',
                  height: 100,
                ),

                const SizedBox(height: 12),

                // Sections with Ribbon Header and Asymmetric Bearer Cards
                ...sections.entries.map((entry) {
                  final sectionTitle = entry.key;
                  final members = entry.value as List<dynamic>? ?? [];

                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: RibbonHeader(
                          title: sectionTitle,
                          color: const Color(0xFF016D77),
                          margin: const EdgeInsets.only(top: 18, bottom: 12),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        child: Wrap(
                          alignment: WrapAlignment.spaceEvenly,
                          spacing: 12,
                          runSpacing: 16,
                          children: members.map((m) {
                            return BearerCard(
                              name: m['name']?.toString() ?? '',
                              designation: m['designation']?.toString() ?? '',
                              photoUrl: m['photo_url']?.toString(),
                              phone: m['phone']?.toString() ?? m['mobile']?.toString(),
                              year: m['year']?.toString(),
                              width: 155,
                              photoHeight: 160,
                            );
                          }).toList(),
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],
                  );
                }),

                const SizedBox(height: 32),
                const KpstaFooter(),
              ],
            ),
          );
        },
      ),
    );
  }
}
