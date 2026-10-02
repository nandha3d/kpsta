import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../auth/presentation/auth_providers.dart';

final membershipMetadataProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.membershipMetadata);
  return response['data'] as Map<String, dynamic>;
});

class MembershipTeacherFormScreen extends ConsumerStatefulWidget {
  const MembershipTeacherFormScreen({super.key});

  @override
  ConsumerState<MembershipTeacherFormScreen> createState() =>
      _MembershipTeacherFormScreenState();
}

class _MembershipTeacherFormScreenState
    extends ConsumerState<MembershipTeacherFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _mobileController = TextEditingController();
  int? _selectedDesignationId;
  int? _selectedSchoolId;
  int? _selectedDistrictId;
  bool _isSubscriber = true;
  bool _isSubmitting = false;

  @override
  void dispose() {
    _nameController.dispose();
    _mobileController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSubmitting = true);

    try {
      final client = ref.read(apiClientProvider);
      await client.post(
        ApiConstants.membershipTeachers,
        data: {
          'name': _nameController.text.trim(),
          'mobile': _mobileController.text.trim(),
          'designation_id': _selectedDesignationId,
          'school_id': _selectedSchoolId,
          'district_id': _selectedDistrictId,
          'adhyapaka_sabdham_subscriber': _isSubscriber ? 1 : 0,
        },
      );

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Teacher enrolled successfully in membership roll'),
            backgroundColor: AppColors.success,
          ),
        );
        context.pop();
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
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final metaAsync = ref.watch(membershipMetadataProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Enroll New Member'),
      body: metaAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, _) => Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Failed to load form options: $err',
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.error),
              ),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: () => ref.refresh(membershipMetadataProvider),
                child: const Text('Retry'),
              ),
            ],
          ),
        ),
        data: (metadata) {
          final designations = (metadata['designations'] as List? ?? []);
          final districts = (metadata['districts'] as List? ?? []);
          final schools = (metadata['schools'] as List? ?? []);

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  TextFormField(
                    controller: _nameController,
                    decoration: const InputDecoration(
                      labelText: 'Teacher Full Name *',
                      hintText: 'e.g. Suresh Kumar K',
                    ),
                    validator: (val) => val == null || val.trim().isEmpty
                        ? 'Name is required'
                        : null,
                  ),
                  const SizedBox(height: 16),
                  TextFormField(
                    controller: _mobileController,
                    keyboardType: TextInputType.phone,
                    decoration: const InputDecoration(
                      labelText: 'Mobile Number *',
                      hintText: '10-digit mobile number',
                    ),
                    validator: (val) => val == null || val.trim().length < 10
                        ? 'Valid mobile number required'
                        : null,
                  ),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<int>(
                    value: _selectedDesignationId,
                    decoration: const InputDecoration(
                      labelText: 'Designation (from API) *',
                    ),
                    items: designations.map((d) {
                      final id = d['id'] as int;
                      final name = d['name']?.toString() ?? 'Designation $id';
                      return DropdownMenuItem<int>(
                        value: id,
                        child: Text(name),
                      );
                    }).toList(),
                    validator: (val) =>
                        val == null ? 'Please select a designation' : null,
                    onChanged: (val) =>
                        setState(() => _selectedDesignationId = val),
                  ),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<int>(
                    value: _selectedDistrictId,
                    decoration: const InputDecoration(
                      labelText: 'District (from API)',
                    ),
                    items: districts.map((dist) {
                      final id = dist['id'] as int;
                      final name = dist['name']?.toString() ?? 'District $id';
                      return DropdownMenuItem<int>(
                        value: id,
                        child: Text(name),
                      );
                    }).toList(),
                    onChanged: (val) =>
                        setState(() => _selectedDistrictId = val),
                  ),
                  const SizedBox(height: 16),
                  if (schools.isNotEmpty)
                    DropdownButtonFormField<int>(
                      value: _selectedSchoolId,
                      decoration: const InputDecoration(
                        labelText: 'Assigned School (from API)',
                      ),
                      isExpanded: true,
                      items: schools.map((s) {
                        final id = s['id'] as int;
                        final name = s['name']?.toString() ?? 'School $id';
                        return DropdownMenuItem<int>(
                          value: id,
                          child: Text(
                            name,
                            overflow: TextOverflow.ellipsis,
                          ),
                        );
                      }).toList(),
                      onChanged: (val) => setState(() => _selectedSchoolId = val),
                    ),
                  const SizedBox(height: 16),
                  Card(
                    child: SwitchListTile(
                      title: const Text(
                        'Adayapaka Sabham Subscriber',
                        style: TextStyle(
                            fontSize: 14, fontWeight: FontWeight.w600),
                      ),
                      subtitle: const Text(
                        'Annual subscription to the association publication',
                        style: TextStyle(fontSize: 12),
                      ),
                      value: _isSubscriber,
                      activeColor: AppColors.primary,
                      onChanged: (val) => setState(() => _isSubscriber = val),
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: _isSubmitting ? null : _submit,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.green,
                    ),
                    child: _isSubmitting
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: Colors.white,
                            ),
                          )
                        : const Text('Save & Enroll Member'),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
