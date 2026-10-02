import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final servicesListProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.services);
  return response['data'] as List<dynamic>? ?? [];
});

class ServicesScreen extends ConsumerWidget {
  const ServicesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final servicesAsync = ref.watch(servicesListProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: 'Service Corner',
        actions: [
          IconButton(
            icon: const Icon(Icons.menu_book_outlined),
            tooltip: 'Adayapaka Sabham',
            onPressed: () => context.push('/services/adayapaka'),
          ),
        ],
      ),
      body: servicesAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(servicesListProvider),
        ),
        data: (services) {
          if (services.isEmpty) {
            return const EmptyState(
              title: 'No services found',
              icon: Icons.design_services_outlined,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(servicesListProvider),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: services.length,
              itemBuilder: (ctx, idx) {
                final s = services[idx];
                final id = s['id']?.toString() ?? '';
                final title = s['title']?.toString() ?? '';
                final desc = s['description']?.toString() ?? '';

                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: InkWell(
                    onTap: () => context.push('/services/$id'),
                    borderRadius: BorderRadius.circular(12),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: AppColors.primary.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(
                                  Icons.assignment_outlined,
                                  color: AppColors.primary,
                                  size: 22,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Text(
                                  title,
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w700,
                                    color: AppColors.textDark,
                                  ),
                                ),
                              ),
                              const Icon(
                                Icons.arrow_forward_ios,
                                size: 14,
                                color: AppColors.textLight,
                              ),
                            ],
                          ),
                          if (desc.isNotEmpty) ...[
                            const SizedBox(height: 10),
                            Text(
                              desc.replaceAll(RegExp(r'<[^>]*>'), '').trim(),
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.textMuted,
                                height: 1.4,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
