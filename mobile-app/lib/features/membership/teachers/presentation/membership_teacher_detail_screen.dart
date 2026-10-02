import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/status_badge.dart';
import '../../../auth/presentation/auth_providers.dart';

final teacherDetailProvider =
    FutureProvider.family.autoDispose<Map<String, dynamic>, String>(
        (ref, id) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get('${ApiConstants.membershipTeachers}/$id');
  return response['data'] as Map<String, dynamic>;
});

class MembershipTeacherDetailScreen extends ConsumerStatefulWidget {
  final String id;

  const MembershipTeacherDetailScreen({super.key, required this.id});

  @override
  ConsumerState<MembershipTeacherDetailScreen> createState() =>
      _MembershipTeacherDetailScreenState();
}

class _MembershipTeacherDetailScreenState
    extends ConsumerState<MembershipTeacherDetailScreen> {
  bool _isUpdatingStatus = false;

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  Future<void> _updateStatus(String action, String label) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: '$label Member',
      message: 'Are you sure you want to $label this teacher record?',
      confirmLabel: label,
    );

    if (confirmed) {
      setState(() => _isUpdatingStatus = true);
      try {
        final client = ref.read(apiClientProvider);
        await client.post(
          '${ApiConstants.membershipTeachers}/${widget.id}/status',
          data: {'action': action},
        );

        ref.refresh(teacherDetailProvider(widget.id));

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Member status updated: $label complete'),
              backgroundColor: AppColors.success,
            ),
          );
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(e.toString().replaceAll('ApiException: ', '')),
              backgroundColor: AppColors.error,
            ),
          );
        }
      } finally {
        if (mounted) setState(() => _isUpdatingStatus = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final detailAsync = ref.watch(teacherDetailProvider(widget.id));

    return Scaffold(
      appBar: const AppTopBar(title: 'Teacher Profile'),
      body: detailAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(teacherDetailProvider(widget.id)),
        ),
        data: (t) {
          final name = t['name']?.toString() ?? 'Teacher';
          final mobile = t['mobile']?.toString() ?? '';
          final teacherType = t['teacher_type']?.toString() ?? 'General';
          final isSubscriber =
              t['adhyapaka_sabdham_subscriber'] == 1 ||
                  t['adhyapaka_sabdham_subscriber'] == '1';

          final isConfirmed =
              t['is_confirmed'] == 1 || t['is_confirmed'] == '1';
          final isVerified =
              t['is_verified'] == 1 || t['is_verified'] == '1';
          final isApproved =
              t['is_approved'] == 1 || t['is_approved'] == '1';

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Header Card
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 30,
                        backgroundColor: AppColors.primary.withOpacity(0.12),
                        child: Text(
                          name.isNotEmpty ? name[0].toUpperCase() : 'T',
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w700,
                            color: AppColors.primary,
                          ),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              name,
                              style: const TextStyle(
                                fontSize: 17,
                                fontWeight: FontWeight.w700,
                                color: AppColors.textDark,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Type: $teacherType',
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.primary,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              mobile,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.textMuted,
                              ),
                            ),
                          ],
                        ),
                      ),
                      if (mobile.isNotEmpty)
                        IconButton(
                          icon: const Icon(
                            Icons.phone_in_talk,
                            color: AppColors.green,
                            size: 26,
                          ),
                          tooltip: 'Call Teacher',
                          onPressed: () => _makeCall(mobile),
                        ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // Multi-tier Verification Stepper / Card
                const Text(
                  'Membership Verification Process',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 10),

                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        _buildStatusRow(
                          title: '1. School Level Confirmation',
                          subtitle:
                              'Verified by local unit / school convenor',
                          isDone: isConfirmed,
                        ),
                        const Divider(height: 24),
                        _buildStatusRow(
                          title: '2. Sub-district Committee Verification',
                          subtitle: 'Verified by educational sub-district',
                          isDone: isVerified,
                        ),
                        const Divider(height: 24),
                        _buildStatusRow(
                          title: '3. State Executive Approval',
                          subtitle:
                              'Enrolled in permanent KPSTA member registry',
                          isDone: isApproved,
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 20),

                // Additional Teacher Details
                const Text(
                  'Subscription & Details',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 10),

                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        _buildDetailRow(
                          'Adayapaka Sabham Subscriber',
                          isSubscriber ? 'Yes (Active)' : 'No',
                        ),
                        const Divider(height: 16),
                        _buildDetailRow(
                          'Teacher Category',
                          teacherType,
                        ),
                        const Divider(height: 16),
                        _buildDetailRow(
                          'Record ID',
                          '#${widget.id}',
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 28),

                // Status Action Buttons
                if (!isApproved) ...[
                  if (!isConfirmed)
                    ElevatedButton.icon(
                      onPressed: _isUpdatingStatus
                          ? null
                          : () => _updateStatus('CONFIRM', 'Confirm School'),
                      icon: const Icon(Icons.check),
                      label: const Text('Confirm School Level'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.indigo,
                      ),
                    )
                  else if (!isVerified)
                    ElevatedButton.icon(
                      onPressed: _isUpdatingStatus
                          ? null
                          : () =>
                              _updateStatus('VERIFY', 'Verify Sub-district'),
                      icon: const Icon(Icons.verified),
                      label: const Text('Verify Sub-district Level'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.orange,
                      ),
                    )
                  else
                    ElevatedButton.icon(
                      onPressed: _isUpdatingStatus
                          ? null
                          : () => _updateStatus('APPROVE', 'State Approve'),
                      icon: const Icon(Icons.verified_user),
                      label: const Text('Approve for State Registry'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.green,
                      ),
                    ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildStatusRow({
    required String title,
    required String subtitle,
    required bool isDone,
  }) {
    return Row(
      children: [
        Icon(
          isDone ? Icons.check_circle : Icons.radio_button_unchecked,
          color: isDone ? AppColors.green : AppColors.textLight,
          size: 24,
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: isDone ? AppColors.textDark : AppColors.textMuted,
                ),
              ),
              Text(
                subtitle,
                style: const TextStyle(fontSize: 11, color: AppColors.textMuted),
              ),
            ],
          ),
        ),
        StatusBadge(
          isPublished: isDone,
          customLabel: isDone ? 'Verified' : 'Pending',
        ),
      ],
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label,
            style: const TextStyle(fontSize: 13, color: AppColors.textMuted)),
        Text(value,
            style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: AppColors.textDark)),
      ],
    );
  }
}
