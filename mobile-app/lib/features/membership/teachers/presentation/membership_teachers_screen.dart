import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/filter_chips.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../../core/widgets/status_badge.dart';
import '../../../auth/presentation/auth_providers.dart';

class TeacherQueryParam {
  final String status;
  final String search;
  const TeacherQueryParam({required this.status, required this.search});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is TeacherQueryParam &&
          runtimeType == other.runtimeType &&
          status == other.status &&
          search == other.search;

  @override
  int get hashCode => status.hashCode ^ search.hashCode;
}

final teachersListFamilyProvider =
    FutureProvider.family.autoDispose<List<dynamic>, TeacherQueryParam>(
        (ref, param) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.membershipTeachers,
    queryParameters: {
      if (param.status != 'all') 'status': param.status,
      if (param.search.isNotEmpty) 'q': param.search,
      'limit': 50,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

class MembershipTeachersScreen extends ConsumerStatefulWidget {
  final String? initialStatus;

  const MembershipTeachersScreen({super.key, this.initialStatus});

  @override
  ConsumerState<MembershipTeachersScreen> createState() =>
      _MembershipTeachersScreenState();
}

class _MembershipTeachersScreenState
    extends ConsumerState<MembershipTeachersScreen> {
  late String _currentStatus;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _currentStatus = widget.initialStatus ?? 'all';
  }

  @override
  Widget build(BuildContext context) {
    final param =
        TeacherQueryParam(status: _currentStatus, search: _searchQuery);
    final teachersAsync = ref.watch(teachersListFamilyProvider(param));

    final statusList = [
      {'id': 'all', 'label': 'All Members'},
      {'id': 'confirmed', 'label': 'School Confirmed'},
      {'id': 'verified', 'label': 'Sub-district Verified'},
      {'id': 'approved', 'label': 'State Approved'},
    ];

    return Scaffold(
      appBar: const AppTopBar(title: 'Teacher Directory'),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/membership/teachers/new'),
        backgroundColor: AppColors.green,
        foregroundColor: AppColors.white,
        icon: const Icon(Icons.person_add),
        label: const Text('Add Teacher'),
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search by name or mobile...',
            onChanged: (q) {
              setState(() => _searchQuery = q.trim());
            },
          ),
          FilterChipsBar<Map<String, String>>(
            items: statusList.sublist(1),
            selectedItem: statusList.firstWhere(
              (s) => s['id'] == _currentStatus,
              orElse: () => statusList.first,
            ),
            allLabel: 'All Members',
            labelBuilder: (s) => s['label']!,
            onSelected: (s) {
              setState(() {
                _currentStatus = s != null ? s['id']! : 'all';
              });
            },
          ),
          const Divider(height: 1, color: AppColors.border),
          Expanded(
            child: teachersAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(teachersListFamilyProvider(param)),
              ),
              data: (teachers) {
                if (teachers.isEmpty) {
                  return const EmptyState(
                    title: 'No teachers found',
                    message:
                        'Try adjusting your search or verification filter.',
                    icon: Icons.person_search_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(teachersListFamilyProvider(param)),
                  child: ListView.builder(
                    padding: const EdgeInsets.only(bottom: 80),
                    itemCount: teachers.length,
                    itemBuilder: (ctx, idx) {
                      final t = teachers[idx];
                      final id = t['id']?.toString() ?? '';
                      final name = t['name']?.toString() ?? 'Teacher';
                      final mobile = t['mobile']?.toString() ?? '';
                      final teacherType = t['teacher_type']?.toString();
                      final isApproved = t['is_approved'] == 1 ||
                          t['is_approved'] == '1';

                      return Card(
                        margin: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 5,
                        ),
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor:
                                AppColors.primary.withOpacity(0.1),
                            child: Text(
                              name.isNotEmpty ? name[0].toUpperCase() : 'T',
                              style: const TextStyle(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                          title: Text(
                            name,
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: AppColors.textDark,
                            ),
                          ),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 2),
                              Text(
                                'Mobile: $mobile',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppColors.textMuted,
                                ),
                              ),
                              if (teacherType != null &&
                                  teacherType.isNotEmpty) ...[
                                const SizedBox(height: 2),
                                Text(
                                  'Type: $teacherType',
                                  style: const TextStyle(
                                    fontSize: 11,
                                    color: AppColors.primary,
                                  ),
                                ),
                              ],
                            ],
                          ),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              StatusBadge(
                                isPublished: isApproved,
                                customLabel:
                                    isApproved ? 'Approved' : 'Pending',
                              ),
                              const SizedBox(width: 4),
                              const Icon(
                                Icons.chevron_right,
                                color: AppColors.textLight,
                              ),
                            ],
                          ),
                          onTap: () =>
                              context.push('/membership/teachers/$id'),
                        ),
                      );
                    },
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
