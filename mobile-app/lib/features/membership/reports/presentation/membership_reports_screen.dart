import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final membershipReportsProvider =
    FutureProvider.autoDispose.family<Map<String, dynamic>, String>(
        (ref, reportType) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.membershipReports,
    queryParameters: {'type': reportType},
  );
  return response['data'] as Map<String, dynamic>;
});

class MembershipReportsScreen extends ConsumerStatefulWidget {
  const MembershipReportsScreen({super.key});

  @override
  ConsumerState<MembershipReportsScreen> createState() =>
      _MembershipReportsScreenState();
}

class _MembershipReportsScreenState
    extends ConsumerState<MembershipReportsScreen> {
  String _selectedReportType = 'district';

  @override
  Widget build(BuildContext context) {
    final reportAsync = ref.watch(membershipReportsProvider(_selectedReportType));

    return Scaffold(
      appBar: const AppTopBar(title: 'Consolidated Member Reports'),
      body: Column(
        children: [
          // Report Type Selector
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            color: AppColors.backgroundSecondary,
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  ChoiceChip(
                    label: const Text('District-Wise'),
                    selected: _selectedReportType == 'district',
                    onSelected: (val) {
                      if (val) setState(() => _selectedReportType = 'district');
                    },
                  ),
                  const SizedBox(width: 8),
                  ChoiceChip(
                    label: const Text('Sub-District-Wise'),
                    selected: _selectedReportType == 'sub_district',
                    onSelected: (val) {
                      if (val) setState(() => _selectedReportType = 'sub_district');
                    },
                  ),
                  const SizedBox(width: 8),
                  ChoiceChip(
                    label: const Text('School-Wise'),
                    selected: _selectedReportType == 'school',
                    onSelected: (val) {
                      if (val) setState(() => _selectedReportType = 'school');
                    },
                  ),
                  const SizedBox(width: 8),
                  ChoiceChip(
                    label: const Text('Designation-Wise'),
                    selected: _selectedReportType == 'designation',
                    onSelected: (val) {
                      if (val) setState(() => _selectedReportType = 'designation');
                    },
                  ),
                ],
              ),
            ),
          ),

          Expanded(
            child: reportAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, _) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () =>
                    ref.refresh(membershipReportsProvider(_selectedReportType)),
              ),
              data: (data) {
                final totals = data['totals'] as Map<String, dynamic>? ?? {};
                final items = (data['items'] as List? ?? []);

                final totalMembers = totals['total_count']?.toString() ?? '0';
                final govtMembers = totals['govt_members']?.toString() ?? '0';
                final aidedMembers = totals['aided_members']?.toString() ?? '0';

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(membershipReportsProvider(_selectedReportType)),
                  child: ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      // Summary Card directly from SQL
                      Card(
                        color: AppColors.primaryLight,
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceAround,
                            children: [
                              _buildSummaryColumn('Total Enrolled', totalMembers, AppColors.primary),
                              _buildSummaryColumn('Govt. Staff', govtMembers, AppColors.blue),
                              _buildSummaryColumn('Aided Staff', aidedMembers, AppColors.purple),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      Text(
                        'Report Table: ${_selectedReportType.toUpperCase()} CONSOLIDATION',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textDark,
                        ),
                      ),
                      const SizedBox(height: 10),

                      if (items.isEmpty)
                        const Padding(
                          padding: EdgeInsets.all(32),
                          child: Center(child: Text('No consolidation records found')),
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
                                DataColumn(label: Text('Unit Name')),
                                DataColumn(label: Text('Category / Context')),
                                DataColumn(label: Text('Govt.'), numeric: true),
                                DataColumn(label: Text('Aided'), numeric: true),
                                DataColumn(label: Text('Total'), numeric: true),
                              ],
                              rows: items.map((it) {
                                final row = it as Map<String, dynamic>;
                                return DataRow(
                                  cells: [
                                    DataCell(Text(
                                      row['name']?.toString() ?? '',
                                      style: const TextStyle(fontWeight: FontWeight.w600),
                                    )),
                                    DataCell(Text(row['extra_label']?.toString() ?? '-')),
                                    DataCell(Text(row['govt_members']?.toString() ?? '0')),
                                    DataCell(Text(row['aided_members']?.toString() ?? '0')),
                                    DataCell(Text(
                                      row['total_count']?.toString() ?? '0',
                                      style: const TextStyle(
                                        fontWeight: FontWeight.w700,
                                        color: AppColors.primary,
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

  Widget _buildSummaryColumn(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.w800,
            color: color,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(
            fontSize: 11,
            color: AppColors.textMuted,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }
}
