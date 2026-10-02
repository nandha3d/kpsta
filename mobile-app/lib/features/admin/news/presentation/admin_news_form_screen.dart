import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../auth/presentation/auth_providers.dart';
import 'admin_news_screen.dart';

class AdminNewsFormScreen extends ConsumerStatefulWidget {
  final String? editId;

  const AdminNewsFormScreen({super.key, this.editId});

  @override
  ConsumerState<AdminNewsFormScreen> createState() =>
      _AdminNewsFormScreenState();
}

class _AdminNewsFormScreenState extends ConsumerState<AdminNewsFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descController = TextEditingController();
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
    _descController.dispose();
    super.dispose();
  }

  Future<void> _loadExisting() async {
    setState(() => _isFetching = true);
    try {
      final client = ref.read(apiClientProvider);
      final response = await client.get('${ApiConstants.news}/${widget.editId}');
      final data = response['data'] as Map<String, dynamic>;
      _titleController.text = data['title']?.toString() ?? '';
      _descController.text = data['description']?.toString() ??
          data['content']?.toString() ??
          '';
      _isPublished = data['status'] == 1 || data['status'] == '1';
    } catch (_) {
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
        'description': _descController.text.trim(),
        'status': _isPublished ? 1 : 0,
      };

      if (widget.editId != null) {
        await client.put(
          '${ApiConstants.adminNews}/${widget.editId}',
          data: payload,
        );
      } else {
        await client.post(
          ApiConstants.adminNews,
          data: payload,
        );
      }

      ref.refresh(adminNewsListProvider);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              widget.editId != null
                  ? 'Article updated successfully'
                  : 'Article published successfully',
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

    return Scaffold(
      appBar: AppTopBar(
        title: isEdit ? 'Edit Article' : 'New Article',
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
                        labelText: 'Article Title *',
                        hintText: 'Enter clear, descriptive headline',
                      ),
                      validator: (val) => val == null || val.trim().isEmpty
                          ? 'Title is required'
                          : null,
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _descController,
                      maxLines: 8,
                      decoration: const InputDecoration(
                        labelText: 'Article Body / Content *',
                        hintText: 'Enter full news text or press statement',
                        alignLabelWithHint: true,
                      ),
                      validator: (val) => val == null || val.trim().isEmpty
                          ? 'Content is required'
                          : null,
                    ),
                    const SizedBox(height: 16),
                    Card(
                      child: SwitchListTile(
                        title: const Text(
                          'Publish Immediately',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        subtitle: Text(
                          _isPublished
                              ? 'Visible to public on mobile and web'
                              : 'Saved as draft in admin records',
                          style: const TextStyle(fontSize: 12),
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
                          : Text(isEdit ? 'Update Article' : 'Create Article'),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
