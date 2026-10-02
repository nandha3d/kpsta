import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/statistic_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final membershipDashboardProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.membershipDashboard);
  return response['data'] as Map<String, dynamic>;
});

class MembershipDashboardScreen extends ConsumerWidget {
  const MembershipDashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dashAsync = ref.watch(membershipDashboardProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: 'Membership Portal',
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined),
            tooltip: "What's New",
            onPressed: () => context.push('/membership/whats-new'),
          ),
          IconButton(
            icon: const Icon(Icons.home_outlined),
            tooltip: 'Public Site',
            onPressed: () => context.go('/home'),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/membership/teachers/new'),
        backgroundColor: AppColors.green,
        foregroundColor: AppColors.white,
        icon: const Icon(Icons.person_add),
        label: const Text('New Member'),
      ),
      body: dashAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(membershipDashboardProvider),
        ),
        data: (data) {
          final metrics = data['metrics'] as Map<String, dynamic>?;
          final total = (data['total_teachers'] ?? metrics?['total_teachers'])?.toString() ?? '0';
          final confirmed = (data['confirmed_count'] ?? metrics?['confirmed_count'] ?? metrics?['confirmed'])?.toString() ?? '0';
          final verified = (data['verified_count'] ?? metrics?['verified_count'] ?? metrics?['verified'])?.toString() ?? '0';
          final approved = (data['approved_count'] ?? metrics?['approved_count'] ?? metrics?['approved'])?.toString() ?? '0';

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(membershipDashboardProvider),
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                // Info Banner
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.badge, color: AppColors.white, size: 36),
                      SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Teacher Registration & Verification',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                                color: AppColors.white,
                              ),
                            ),
                            SizedBox(height: 2),
                            Text(
                              'Manage member roll, school confirmation, and district approvals.',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.white70,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                const Text(
                  'Membership Verification Status',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 12),

                // Statistics Grid (Data direct from API)
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  childAspectRatio: 1.15,
                  children: [
                    StatisticCard(
                      title: 'Total Enrolled',
                      value: total,
                      icon: Icons.groups,
                      color: AppColors.primary,
                      onTap: () => context.push('/membership/teachers?status=all'),
                    ),
                    StatisticCard(
                      title: 'Confirmed by School',
                      value: confirmed,
                      icon: Icons.domain,
                      color: AppColors.indigo,
                      onTap: () =>
                          context.push('/membership/teachers?status=confirmed'),
                    ),
                    StatisticCard(
                      title: 'Verified by Sub-district',
                      value: verified,
                      icon: Icons.verified_user,
                      color: AppColors.orange,
                      onTap: () =>
                          context.push('/membership/teachers?status=verified'),
                    ),
                    StatisticCard(
                      title: 'Approved by State',
                      value: approved,
                      icon: Icons.check_circle,
                      color: AppColors.green,
                      onTap: () =>
                          context.push('/membership/teachers?status=approved'),
                    ),
                  ],
                ),

                const SizedBox(height: 24),

                const Text(
                  'Membership Portals & Analytics',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 10),

                Card(
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.analytics_outlined,
                          color: AppColors.primary),
                    ),
                    title: const Text(
                      'Live Membership Tallies (Counts)',
                      style:
                          TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                    ),
                    subtitle: const Text(
                      'State, District, Sub-district real-time aggregation',
                      style: TextStyle(fontSize: 12),
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                    onTap: () => context.push('/membership/counts'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.indigo.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.table_chart_outlined,
                          color: AppColors.indigo),
                    ),
                    title: const Text(
                      'Consolidated Member Reports',
                      style:
                          TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                    ),
                    subtitle: const Text(
                      'Govt vs Aided member breakdown by District & Sub-district',
                      style: TextStyle(fontSize: 12),
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                    onTap: () => context.push('/membership/reports'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.green.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.people_alt_outlined,
                          color: AppColors.green),
                    ),
                    title: const Text(
                      'Browse Teacher Directory',
                      style:
                          TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                    ),
                    subtitle: const Text(
                      'Search by name, mobile, PEN or school name',
                      style: TextStyle(fontSize: 12),
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                    onTap: () => context.push('/membership/teachers'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.orange.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.campaign_outlined,
                          color: AppColors.orange),
                    ),
                    title: const Text(
                      "Internal Member Notices (What's New)",
                      style:
                          TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                    ),
                    subtitle: const Text(
                      'Circulars for branch, district, and state members',
                      style: TextStyle(fontSize: 12),
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                    onTap: () => context.push('/membership/whats-new'),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
