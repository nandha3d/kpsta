import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/person_card.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';

enum LeaderSectionType { state, former, district }

extension LeaderSectionExt on LeaderSectionType {
  String get apiKey {
    switch (this) {
      case LeaderSectionType.state:
        return 'state';
      case LeaderSectionType.former:
        return 'former';
      case LeaderSectionType.district:
        return 'district';
    }
  }

  String get title {
    switch (this) {
      case LeaderSectionType.state:
        return 'State Leaders';
      case LeaderSectionType.former:
        return 'Former State Leaders';
      case LeaderSectionType.district:
        return 'District Leaders';
    }
  }

  String get addButtonLabel {
    switch (this) {
      case LeaderSectionType.state:
        return 'Add State Leader';
      case LeaderSectionType.former:
        return 'Add Former Leader';
      case LeaderSectionType.district:
        return 'Add District Leader';
    }
  }

  List<Map<String, dynamic>> get fallbackRoles {
    switch (this) {
      case LeaderSectionType.state:
        return [
          {'slug': 'president', 'label': 'President', 'designation_id': 1, 'position': 1},
          {'slug': 'general-secretary', 'label': 'General Secretary', 'designation_id': 2, 'position': 2},
          {'slug': 'treasurer', 'label': 'Treasurer', 'designation_id': 3, 'position': 3},
          {'slug': 'senior-vice-president', 'label': 'Senior Vice President', 'designation_id': 4, 'position': 4},
          {'slug': 'associate-general-secretary', 'label': 'Associate General Secretary', 'designation_id': 5, 'position': 5},
          {'slug': 'vice-president', 'label': 'Vice President', 'designation_id': 6, 'position': 6},
          {'slug': 'secretary', 'label': 'Secretary', 'designation_id': 7, 'position': 7},
          {'slug': 'secretariate-members', 'label': 'Secretariate Members', 'designation_id': 9, 'position': 8},
        ];
      case LeaderSectionType.former:
        return [
          {'slug': 'president', 'label': 'President', 'designation_id': 1, 'position': 1},
          {'slug': 'general-secretary', 'label': 'General Secretary', 'designation_id': 2, 'position': 2},
          {'slug': 'treasurer', 'label': 'Treasurer', 'designation_id': 3, 'position': 3},
        ];
      case LeaderSectionType.district:
        return [
          {'slug': 'president', 'label': 'President', 'designation_id': 1, 'position': 1},
          {'slug': 'secretary', 'label': 'Secretary', 'designation_id': 2, 'position': 2},
          {'slug': 'treasurer', 'label': 'Treasurer', 'designation_id': 3, 'position': 3},
        ];
    }
  }
}

class AdminBearerQuery {
  final LeaderSectionType section;
  final String? roleSlug;
  final String? district;
  final String search;

  const AdminBearerQuery({
    required this.section,
    this.roleSlug,
    this.district,
    this.search = '',
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is AdminBearerQuery &&
          runtimeType == other.runtimeType &&
          section == other.section &&
          roleSlug == other.roleSlug &&
          district == other.district &&
          search == other.search;

  @override
  int get hashCode =>
      section.hashCode ^
      roleSlug.hashCode ^
      district.hashCode ^
      search.hashCode;
}

final adminBearerMetadataProvider =
    FutureProvider<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminOfficeBearerMetadata);
  return response['data'] as Map<String, dynamic>? ?? {};
});

final adminBearersProvider =
    FutureProvider.autoDispose.family<List<dynamic>, AdminBearerQuery>(
        (ref, query) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.adminOfficeBearers,
    queryParameters: {
      'section': query.section.apiKey,
      if (query.roleSlug != null && query.roleSlug!.isNotEmpty)
        'role': query.roleSlug,
      if (query.section == LeaderSectionType.district &&
          query.district != null &&
          query.district!.isNotEmpty)
        'district': query.district,
      if (query.search.isNotEmpty) 'search': query.search,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

class AdminOfficeBearersScreen extends ConsumerStatefulWidget {
  final LeaderSectionType section;

  const AdminOfficeBearersScreen({
    super.key,
    this.section = LeaderSectionType.state,
  });

  @override
  ConsumerState<AdminOfficeBearersScreen> createState() =>
      _AdminOfficeBearersScreenState();
}

class _AdminOfficeBearersScreenState
    extends ConsumerState<AdminOfficeBearersScreen> {
  String? _selectedRoleSlug;
  String? _selectedDistrict;
  String _searchQuery = '';

  List<Map<String, dynamic>> _rolesForSection(Map<String, dynamic>? meta) {
    final sections = meta?['sections'] as Map<String, dynamic>?;
    final secData = sections?[widget.section.apiKey] as Map<String, dynamic>?;
    final rawRoles = secData?['roles'] as List<dynamic>?;
    if (rawRoles != null && rawRoles.isNotEmpty) {
      return rawRoles
          .whereType<Map>()
          .map((e) => Map<String, dynamic>.from(e))
          .toList();
    }
    return widget.section.fallbackRoles;
  }

  List<String> _districtsFromMeta(Map<String, dynamic>? meta) {
    final raw = meta?['districts'] as List<dynamic>? ?? [];
    final list = raw
        .whereType<Map>()
        .map((e) => e['name']?.toString() ?? '')
        .where((s) => s.isNotEmpty)
        .toList();
    if (list.isNotEmpty) return list;
    return const [
      'Thiruvananthapuram',
      'Kollam',
      'Pathanamthitta',
      'Allapuzha',
      'Kottayam',
      'Idukki',
      'Ernakulam',
      'Thrissur',
      'Palakkad',
      'Malappuram',
      'Kozhikode',
      'Wayanad',
      'Kannur',
      'Kasargod',
    ];
  }

  Future<void> _deleteBearer(String id, String name) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: 'Remove ${widget.section.title.replaceAll('s', '')}',
      message:
          'Are you sure you want to remove "$name" from ${widget.section.title}?',
      confirmLabel: 'Remove',
      isDestructive: true,
    );

    if (confirmed) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminOfficeBearers}/$id');
        ref.invalidate(adminBearersProvider);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Leader removed'),
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
      }
    }
  }

  Future<void> _openFormSheet({
    Map<String, dynamic>? existing,
    required List<Map<String, dynamic>> roles,
    required List<String> districts,
  }) async {
    final isEdit = existing != null;
    final nameCtrl =
        TextEditingController(text: existing?['name']?.toString() ?? '');
    final phoneCtrl =
        TextEditingController(text: existing?['phone']?.toString() ?? '');
    final emailCtrl =
        TextEditingController(text: existing?['email']?.toString() ?? '');
    final yearCtrl =
        TextEditingController(text: existing?['year']?.toString() ?? '');

    int? selectedDesigId = int.tryParse(existing?['designation_id']?.toString() ?? '');
    if (selectedDesigId == null || selectedDesigId == 0) {
      if (_selectedRoleSlug != null) {
        final match = roles.firstWhere(
          (r) => r['slug'] == _selectedRoleSlug,
          orElse: () => roles.first,
        );
        selectedDesigId = (match['designation_id'] as num?)?.toInt();
      } else if (roles.isNotEmpty) {
        selectedDesigId = (roles.first['designation_id'] as num?)?.toInt();
      }
    }
    if (roles.isNotEmpty &&
        !roles.any((r) => (r['designation_id'] as num?)?.toInt() == selectedDesigId)) {
      selectedDesigId = (roles.first['designation_id'] as num?)?.toInt();
    }

    String? selectedDistrict =
        existing?['district']?.toString() ??
        existing?['section_heading']?.toString() ??
        _selectedDistrict;
    if (widget.section == LeaderSectionType.district &&
        (selectedDistrict == null || !districts.contains(selectedDistrict))) {
      selectedDistrict = districts.isNotEmpty ? districts.first : null;
    }

    bool isPublished = existing != null ? (existing['is_publish'] == true) : true;
    bool isSaving = false;
    final formKey = GlobalKey<FormState>();

    await showModalBottomSheet<void>(
      context: context,
      useRootNavigator: true,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setSheetState) {
            return SafeArea(
              top: false,
              child: Padding(
                padding: EdgeInsets.fromLTRB(
                  16,
                  14,
                  16,
                  MediaQuery.of(ctx).viewInsets.bottom + 18,
                ),
                child: SingleChildScrollView(
                  child: Form(
                    key: formKey,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Center(
                          child: Container(
                            width: 40,
                            height: 4,
                            decoration: BoxDecoration(
                              color: const Color(0xFFCBD5E1),
                              borderRadius: BorderRadius.circular(2),
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                isEdit
                                    ? 'Edit ${widget.section.title}'
                                    : widget.section.addButtonLabel,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.primaryDark,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.close_rounded),
                              onPressed: () => Navigator.of(ctx).pop(),
                            ),
                          ],
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 8),
                          margin: const EdgeInsets.only(bottom: 14),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEEF2FF),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: const Color(0xFFC7D2FE)),
                          ),
                          child: Text(
                            'Section: ${widget.section.title} (isolated)',
                            style: const TextStyle(
                              fontSize: 12.5,
                              fontWeight: FontWeight.w700,
                              color: Color(0xFF3730A3),
                            ),
                          ),
                        ),
                        DropdownButtonFormField<int>(
                          value: selectedDesigId,
                          dropdownColor: Colors.white,
                          menuMaxHeight: 320,
                          decoration: const InputDecoration(
                            labelText: 'Role / Designation *',
                          ),
                          items: roles
                              .where((r) => (r['designation_id'] as num?)?.toInt() != 0)
                              .map((r) {
                            final dId = (r['designation_id'] as num).toInt();
                            return DropdownMenuItem<int>(
                              value: dId,
                              child: Text(r['label']?.toString() ?? ''),
                            );
                          }).toList(),
                          onChanged: (val) =>
                              setSheetState(() => selectedDesigId = val),
                          validator: (val) =>
                              val == null ? 'Please select a role' : null,
                        ),
                        if (widget.section == LeaderSectionType.district) ...[
                          const SizedBox(height: 12),
                          DropdownButtonFormField<String>(
                            value: selectedDistrict,
                            dropdownColor: Colors.white,
                            menuMaxHeight: 320,
                            decoration: const InputDecoration(
                              labelText: 'District *',
                            ),
                            items: districts
                                .map((d) => DropdownMenuItem<String>(
                                      value: d,
                                      child: Text(d),
                                    ))
                                .toList(),
                            onChanged: (val) =>
                                setSheetState(() => selectedDistrict = val),
                            validator: (val) => (val == null || val.isEmpty)
                                ? 'Please select a district'
                                : null,
                          ),
                        ],
                        const SizedBox(height: 12),
                        TextFormField(
                          controller: nameCtrl,
                          decoration: const InputDecoration(
                            labelText: 'Leader Name *',
                          ),
                          validator: (val) =>
                              (val == null || val.trim().isEmpty)
                                  ? 'Name is required'
                                  : null,
                        ),
                        const SizedBox(height: 12),
                        TextFormField(
                          controller: phoneCtrl,
                          keyboardType: TextInputType.phone,
                          decoration: const InputDecoration(
                            labelText: 'Phone',
                          ),
                        ),
                        const SizedBox(height: 12),
                        TextFormField(
                          controller: emailCtrl,
                          keyboardType: TextInputType.emailAddress,
                          decoration: const InputDecoration(
                            labelText: 'Email',
                          ),
                        ),
                        const SizedBox(height: 12),
                        TextFormField(
                          controller: yearCtrl,
                          decoration: InputDecoration(
                            labelText: widget.section == LeaderSectionType.former
                                ? 'Term / Period (e.g. 2018-2021)'
                                : 'Term / Year (Optional)',
                          ),
                        ),
                        const SizedBox(height: 8),
                        SwitchListTile(
                          contentPadding: EdgeInsets.zero,
                          title: const Text(
                            'Active / Published',
                            style: TextStyle(
                                fontSize: 14, fontWeight: FontWeight.w600),
                          ),
                          value: isPublished,
                          activeColor: AppColors.primary,
                          onChanged: (val) =>
                              setSheetState(() => isPublished = val),
                        ),
                        const SizedBox(height: 14),
                        ElevatedButton(
                          onPressed: isSaving
                              ? null
                              : () async {
                                  if (!formKey.currentState!.validate()) return;
                                  setSheetState(() => isSaving = true);
                                  try {
                                    final client = ref.read(apiClientProvider);
                                    final rolePos = roles.firstWhere(
                                      (r) =>
                                          (r['designation_id'] as num?)?.toInt() ==
                                          selectedDesigId,
                                      orElse: () => {'position': 25},
                                    )['position'];
                                    final payload = {
                                      'section': widget.section.apiKey,
                                      'name': nameCtrl.text.trim(),
                                      'designation_id': selectedDesigId,
                                      'phone': phoneCtrl.text.trim(),
                                      'email': emailCtrl.text.trim(),
                                      'year': yearCtrl.text.trim(),
                                      'position': rolePos ?? 25,
                                      'is_publish': isPublished,
                                      if (widget.section ==
                                          LeaderSectionType.district)
                                        'district': selectedDistrict,
                                    };
                                    if (isEdit) {
                                      final id = existing['id']?.toString() ?? '';
                                      await client.put(
                                        '${ApiConstants.adminOfficeBearers}/$id',
                                        data: payload,
                                      );
                                    } else {
                                      await client.post(
                                        ApiConstants.adminOfficeBearers,
                                        data: payload,
                                      );
                                    }
                                    ref.invalidate(adminBearersProvider);
                                    if (ctx.mounted) Navigator.of(ctx).pop();
                                    if (mounted) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(
                                          content: Text(isEdit
                                              ? 'Leader updated'
                                              : 'Leader added'),
                                          backgroundColor: AppColors.success,
                                        ),
                                      );
                                    }
                                  } catch (e) {
                                    setSheetState(() => isSaving = false);
                                    if (ctx.mounted) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(
                                          content: Text(e
                                              .toString()
                                              .replaceAll('ApiException: ', '')),
                                          backgroundColor: AppColors.error,
                                        ),
                                      );
                                    }
                                  }
                                },
                          child: isSaving
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    color: Colors.white,
                                  ),
                                )
                              : Text(isEdit ? 'Save Changes' : 'Save Leader'),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final metaAsync = ref.watch(adminBearerMetadataProvider);
    final meta = metaAsync.maybeWhen(
      data: (m) => m,
      orElse: () => null,
    );
    final roles = _rolesForSection(meta);
    final districts = _districtsFromMeta(meta);

    final query = AdminBearerQuery(
      section: widget.section,
      roleSlug: _selectedRoleSlug,
      district: _selectedDistrict,
      search: _searchQuery,
    );
    final bearersAsync = ref.watch(adminBearersProvider(query));

    return Scaffold(
      appBar: AppTopBar(title: widget.section.title),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: Text(widget.section.addButtonLabel),
        onPressed: () => _openFormSheet(roles: roles, districts: districts),
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search ${widget.section.title.toLowerCase()}...',
            onChanged: (q) => setState(() => _searchQuery = q.trim()),
          ),
          // Role Filter Chips (strictly scoped to this section)
          SizedBox(
            height: 44,
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                      label: const Text('All Roles'),
                      selected: _selectedRoleSlug == null,
                      selectedColor: AppColors.primary,
                      labelStyle: TextStyle(
                        color: _selectedRoleSlug == null
                            ? Colors.white
                            : AppColors.textDark,
                        fontWeight: FontWeight.w600,
                        fontSize: 12.5,
                      ),
                      onSelected: (_) => setState(() => _selectedRoleSlug = null),
                    ),
                  ),
                  ...roles.map((role) {
                    final slug = role['slug']?.toString() ?? '';
                    final label = role['label']?.toString() ?? '';
                    final isSelected = _selectedRoleSlug == slug;
                    return Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(label),
                        selected: isSelected,
                        selectedColor: AppColors.primary,
                        labelStyle: TextStyle(
                          color: isSelected ? Colors.white : AppColors.textDark,
                          fontWeight: FontWeight.w600,
                          fontSize: 12.5,
                        ),
                        onSelected: (selected) {
                          setState(() => _selectedRoleSlug = selected ? slug : null);
                        },
                      ),
                    );
                  }),
                ],
              ),
            ),
          ),
          if (widget.section == LeaderSectionType.district)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
              child: DropdownButtonFormField<String?>(
                value: _selectedDistrict,
                dropdownColor: Colors.white,
                menuMaxHeight: 320,
                decoration: const InputDecoration(
                  labelText: 'Filter by District',
                  isDense: true,
                  contentPadding:
                      EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                ),
                items: [
                  const DropdownMenuItem<String?>(
                    value: null,
                    child: Text('All 14 Districts'),
                  ),
                  ...districts.map((d) => DropdownMenuItem<String?>(
                        value: d,
                        child: Text(d),
                      )),
                ],
                onChanged: (val) => setState(() => _selectedDistrict = val),
              ),
            ),
          Expanded(
            child: bearersAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(adminBearersProvider(query)),
              ),
              data: (bearers) {
                if (bearers.isEmpty) {
                  return EmptyState(
                    title: 'No ${widget.section.title} found',
                    icon: Icons.people_outline,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(adminBearersProvider(query)),
                  child: ListView.builder(
                    padding: const EdgeInsets.fromLTRB(0, 8, 0, 96),
                    itemCount: bearers.length,
                    itemBuilder: (ctx, idx) {
                      final b = Map<String, dynamic>.from(bearers[idx] as Map);
                      final id = b['id']?.toString() ?? '';
                      final name = b['name']?.toString() ?? '';

                      return Stack(
                        children: [
                          PersonCard(
                            name: name,
                            designation: b['designation']?.toString() ?? '',
                            photoUrl: b['photo_url']?.toString(),
                            mobile: b['phone']?.toString() ??
                                b['mobile']?.toString(),
                            email: b['email']?.toString(),
                            district: b['district']?.toString(),
                            tenure: b['year']?.toString(),
                          ),
                          Positioned(
                            top: 8,
                            right: 20,
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                IconButton(
                                  icon: const Icon(Icons.edit_outlined,
                                      color: AppColors.primary, size: 20),
                                  tooltip: 'Edit',
                                  onPressed: () => _openFormSheet(
                                    existing: b,
                                    roles: roles,
                                    districts: districts,
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline,
                                      color: AppColors.error, size: 20),
                                  tooltip: 'Delete',
                                  onPressed: () => _deleteBearer(id, name),
                                ),
                              ],
                            ),
                          ),
                        ],
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
