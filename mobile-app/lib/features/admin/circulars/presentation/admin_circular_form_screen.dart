import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../auth/presentation/auth_providers.dart';
import 'admin_circulars_screen.dart';

final circularCategoriesProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.circularCategories,
    queryParameters: {'all': 1},
  );
  return response['data'] as List<dynamic>? ?? [];
});

class AdminCircularFormScreen extends ConsumerStatefulWidget {
  final String? editId;

  const AdminCircularFormScreen({super.key, this.editId});

  @override
  ConsumerState<AdminCircularFormScreen> createState() =>
      _AdminCircularFormScreenState();
}

class _AdminCircularFormScreenState
    extends ConsumerState<AdminCircularFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _orderNoController = TextEditingController();
  final _descController = TextEditingController();
  String _selectedType = 'general';
  int? _selectedCategoryId;
  bool _isPublished = true;
  bool _isLoading = false;
  bool _isFetching = false;

  @override
  void initState() {
    super.initState();
    if (widget.editId != null) {
      _loadExisting();
    }
  }

  @override
  void dispose() {
    _titleController.dispose();
    _orderNoController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _loadExisting() async {
    setState(() => _isFetching = true);
    try {
      final client = ref.read(apiClientProvider);
      final response = await client.get('${ApiConstants.adminCirculars}/${widget.editId}');
      final data = response['data'] as Map<String, dynamic>;
      _titleController.text = data['title']?.toString() ?? '';
      _descController.text = data['description']?.toString() ?? '';
      _selectedType = data['type']?.toString() ?? 'general';
      if (data['category_id'] != null && data['category_id'] != 0) {
        _selectedCategoryId = int.tryParse(data['category_id'].toString());
      }
      _isPublished = data['is_publish'] == true ||
          data['status'] == 1 ||
          data['status'] == '1';
    } catch (_) {
      // Gracefully continue with defaults if initial fetch fails
    } finally {
      if (mounted) setState(() => _isFetching = false);
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    try {
      final client = ref.read(apiClientProvider);
      final payload = {
        'title': _titleController.text.trim(),
        'order_number': _orderNoController.text.trim(),
        'description': _descController.text.trim(),
        'type': _selectedType,
        'category_id': _selectedCategoryId ?? 0,
        'is_publish': _isPublished ? 1 : 0,
        'status': _isPublished ? 1 : 0,
      };

      if (widget.editId != null) {
        await client.put(
          '${ApiConstants.adminCirculars}/${widget.editId}',
          data: payload,
        );
      } else {
        await client.post(
          ApiConstants.adminCirculars,
          data: payload,
        );
      }

      ref.refresh(adminCircularsListProvider);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              widget.editId != null
                  ? 'Order updated successfully'
                  : 'Order published successfully',
            ),
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
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEdit = widget.editId != null;
    final categoriesAsync = ref.watch(circularCategoriesProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: isEdit ? 'Edit Circular' : 'New Order Circular',
      ),
      body: _isFetching
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    TextFormField(
                      controller: _titleController,
                      decoration: const InputDecoration(
                        labelText: 'Subject / Title *',
                        hintText: 'Enter order title or summary',
                      ),
                      validator: (val) => val == null || val.trim().isEmpty
                          ? 'Subject is required'
                          : null,
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      controller: _orderNoController,
                      decoration: const InputDecoration(
                        labelText: 'Order Number (e.g. G.O(P) No. 42/2026/GEDN)',
                      ),
                    ),
                    const SizedBox(height: 14),
                    DropdownButtonFormField<String>(
                      value: _selectedType,
                      decoration: const InputDecoration(
                        labelText: 'Education Stream *',
                      ),
                      items: const [
                        DropdownMenuItem(
                          value: 'general',
                          child: Text('General Education'),
                        ),
                        DropdownMenuItem(
                          value: 'hse',
                          child: Text('Higher Secondary (HSE)'),
                        ),
                        DropdownMenuItem(
                          value: 'vhse',
                          child: Text('Vocational Higher Secondary (VHSE)'),
                        ),
                      ],
                      onChanged: (val) {
                        if (val != null) setState(() => _selectedType = val);
                      },
                    ),
                    const SizedBox(height: 14),
                    categoriesAsync.when(
                      loading: () => const LinearProgressIndicator(
                        color: AppColors.primary,
                      ),
                      error: (_, __) => const SizedBox.shrink(),
                      data: (categories) {
                        if (categories.isEmpty) return const SizedBox.shrink();
                        return DropdownButtonFormField<int>(
                          value: _selectedCategoryId,
                          decoration: const InputDecoration(
                            labelText: 'Circular Category',
                            hintText: 'Select category from database',
                          ),
                          items: categories.map((cat) {
                            final catId = (cat['id'] as num).toInt();
                            final catName = cat['name']?.toString() ?? 'Category $catId';
                            return DropdownMenuItem<int>(
                              value: catId,
                              child: Text(catName),
                            );
                          }).toList(),
                          onChanged: (val) {
                            setState(() => _selectedCategoryId = val);
                          },
                        );
                      },
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      controller: _descController,
                      maxLines: 4,
                      decoration: const InputDecoration(
                        labelText: 'Remarks / Summary Notes',
                        alignLabelWithHint: true,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Card(
                      child: SwitchListTile(
                        title: const Text(
                          'Active / Published',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        value: _isPublished,
                        activeColor: AppColors.primary,
                        onChanged: (val) => setState(() => _isPublished = val),
                      ),
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: _isLoading ? null : _save,
                      child: _isLoading
                          ? const SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : Text(isEdit ? 'Update Order' : 'Publish Order'),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
