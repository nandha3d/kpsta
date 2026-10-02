import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final serviceDetailProvider =
    FutureProvider.family.autoDispose<Map<String, dynamic>, String>(
        (ref, id) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get('${ApiConstants.services}/$id');
  return response['data'] as Map<String, dynamic>;
});

class ServiceDetailScreen extends ConsumerWidget {
  final String id;

  const ServiceDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final serviceAsync = ref.watch(serviceDetailProvider(id));

    return Scaffold(
      appBar: const AppTopBar(title: 'Service Guide'),
      body: serviceAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(serviceDetailProvider(id)),
        ),
        data: (data) {
          final service = data['service'] as Map<String, dynamic>? ?? {};
          final rules = data['rules'] as List<dynamic>? ?? [];

          final title = service['title']?.toString() ?? 'Service';
          final desc = service['description']?.toString() ?? '';

          return ListView(
            padding: const EdgeInsets.all(20),
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textDark,
                ),
              ),
              const SizedBox(height: 14),
              if (desc.isNotEmpty) ...[
                Text(
                  desc.replaceAll(RegExp(r'<[^>]*>'), '').trim(),
                  style: const TextStyle(
                    fontSize: 14,
                    color: AppColors.text,
                    height: 1.6,
                  ),
                ),
                const SizedBox(height: 24),
              ],
              if (rules.isNotEmpty) ...[
                const Text(
                  'Associated Service Rules & Guidelines',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.primaryDark,
                  ),
                ),
                const SizedBox(height: 8),
                ...rules.map((rule) {
                  return DocumentRow(
                    title: rule['title']?.toString() ?? '',
                    fileUrl: rule['file_url']?.toString(),
                  );
                }),
              ],
            ],
          );
        },
      ),
    );
  }
}
