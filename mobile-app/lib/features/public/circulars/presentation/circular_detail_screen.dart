import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final circularDetailProvider =
    FutureProvider.family.autoDispose<Map<String, dynamic>, String>(
        (ref, id) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get('${ApiConstants.circulars}/$id');
  return response['data'] as Map<String, dynamic>;
});

class CircularDetailScreen extends ConsumerWidget {
  final String id;

  const CircularDetailScreen({super.key, required this.id});

  Future<void> _openPdf(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detailAsync = ref.watch(circularDetailProvider(id));

    return Scaffold(
      appBar: const AppTopBar(title: 'Order Details'),
      body: detailAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(circularDetailProvider(id)),
        ),
        data: (item) {
          final title = item['title']?.toString() ?? '';
          final date = item['created_date']?.toString() ?? '';
          final category = item['category']?.toString();
          final orderNo = item['order_no']?.toString() ?? item['order_number']?.toString();
          final description = item['description']?.toString() ?? '';
          final fileUrl = item['file_url']?.toString();

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (category != null)
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      category,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                const SizedBox(height: 12),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                    height: 1.3,
                  ),
                ),
                const SizedBox(height: 12),
                if (orderNo != null && orderNo.isNotEmpty)
                  Text(
                    'Order No: $orderNo',
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      color: AppColors.textMuted,
                    ),
                  ),
                if (date.isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    'Date: $date',
                    style: const TextStyle(
                      fontSize: 13,
                      color: AppColors.textMuted,
                    ),
                  ),
                ],
                const Divider(height: 32, color: AppColors.border),
                if (description.isNotEmpty) ...[
                  Text(
                    description.replaceAll(RegExp(r'<[^>]*>'), '').trim(),
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppColors.text,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 24),
                ],
                if (fileUrl != null && fileUrl.isNotEmpty) ...[
                  ElevatedButton.icon(
                    onPressed: () => _openPdf(fileUrl),
                    icon: const Icon(Icons.picture_as_pdf_outlined),
                    label: const Text('Download / View Attached Order (PDF)'),
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }
}
