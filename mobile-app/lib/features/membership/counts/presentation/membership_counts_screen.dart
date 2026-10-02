import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/statistic_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final membershipCountsProvider =
    FutureProvider.autoDispose.family<Map<String, dynamic>, int>(
        (ref, group) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.membershipCounts,
    queryParameters: {'group': group},
  );
  return response['data'] as Map<String, dynamic>;
});

class MembershipCountsScreen extends ConsumerStatefulWidget {
  const MembershipCountsScreen({super.key});

  @override
  ConsumerState<MembershipCountsScreen> createState() =>
      _MembershipCountsScreenState();
}

class _MembershipCountsScreenState
    extends ConsumerState<MembershipCountsScreen> {
  int _selectedGroup = 1; // 1 = State level breakdown

  @override
  Widget build(BuildContext context) {
    final countsAsync = ref.watch(membershipCountsProvider(_selectedGroup));

    return Scaffold(
      appBar: const AppTopBar(title: 'Membership Live Tallies'),
      body: Column(
        children: [
          // Filter Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            color: AppColors.backgroundSecondary,
            child: Row(
              children: [
                const Text(
                  'Hierarchy:',
                  style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                ),
                const SizedBox(width: 10),
                ChoiceChip(
                  label: const Text('District View'),
                  selected: _selectedGroup == 1,
                  onSelected: (val) {
                    if (val) setState(() => _selectedGroup = 1);
                  },
                ),
                const SizedBox(width: 8),
                ChoiceChip(
                  label: const Text('Sub-District View'),
                  selected: _selectedGroup == 2,
                  onSelected: (val) {
                    if (val) setState(() => _selectedGroup = 2);
                  },
                ),
              ],
            ),
          ),

          Expanded(
            child: countsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, _) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () =>
                    ref.refresh(membershipCountsProvider(_selectedGroup)),
              ),
              data: (data) {
                final totals = data['totals'] as Map<String, dynamic>? ?? {};
                final items = (data['items'] as List? ?? []);

                final totalAll = totals['total']?.toString() ?? '0';
                final confirmed = totals['confirmed']?.toString() ?? '0';
                final verified = totals['verified']?.toString() ?? '0';
                final approved = totals['approved']?.toString() ?? '0';
                final govt = totals['govt_members']?.toString() ?? '0';
                final aided = totals['aided_members']?.toString() ?? '0';

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(membershipCountsProvider(_selectedGroup)),
                  child: ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      // Header Card
                      Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: AppColors.primary,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          'SQL Database Verified Aggregation (Zero Estimates) — Year ${data['year'] ?? ''}',
                          style: const TextStyle(
                            color: AppColors.white,
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Totals Grid
                      GridView.count(
                        crossAxisCount: 3,
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        crossAxisSpacing: 8,
                        mainAxisSpacing: 8,
                        childAspectRatio: 1.1,
                        children: [
                          StatisticCard(
                            title: 'Total Enrolled',
                            value: totalAll,
                            color: AppColors.primary,
                          ),
                          StatisticCard(
                            title: 'Confirmed',
                            value: confirmed,
                            color: AppColors.indigo,
                          ),
                          StatisticCard(
                            title: 'Verified',
                            value: verified,
                            color: AppColors.orange,
                          ),
                          StatisticCard(
                            title: 'Approved',
                            value: approved,
                            color: AppColors.green,
                          ),
                          StatisticCard(
                            title: 'Govt. Members',
                            value: govt,
                            color: AppColors.blue,
                          ),
                          StatisticCard(
                            title: 'Aided Members',
                            value: aided,
                            color: AppColors.purple,
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),

                      const Text(
                        'District Breakdown',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textDark,
                        ),
                      ),
                      const SizedBox(height: 10),

                      if (items.isEmpty)
                        const Padding(
                          padding: EdgeInsets.all(24.0),
                          child: Center(
                            child: Text('No breakdown records available from database'),
                          ),
                        )
                      else
                        Card(
                          clipBehavior: Clip.antiAlias,
                          child: SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            child: DataTable(
                              headingRowColor: WidgetStateProperty.all(
                                AppColors.backgroundSecondary,
                              ),
                              columns: const [
                                DataColumn(label: Text('Unit / District')),
                                DataColumn(label: Text('Total'), numeric: true),
                                DataColumn(label: Text('Govt'), numeric: true),
                                DataColumn(label: Text('Aided'), numeric: true),
                                DataColumn(label: Text('Approved'), numeric: true),
                              ],
                              rows: items.map((it) {
                                final row = it as Map<String, dynamic>;
                                return DataRow(
                                  cells: [
                                    DataCell(Text(
                                      row['name']?.toString() ?? '',
                                      style: const TextStyle(fontWeight: FontWeight.w600),
                                    )),
                                    DataCell(Text(row['total']?.toString() ?? '0')),
                                    DataCell(Text(row['govt_members']?.toString() ?? '0')),
                                    DataCell(Text(row['aided_members']?.toString() ?? '0')),
                                    DataCell(Text(
                                      row['approved']?.toString() ?? '0',
                                      style: const TextStyle(
                                        color: AppColors.green,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    )),
                                  ],
                                );
                              }).toList(),
                            ),
                          ),
                        ),
                    ],
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
