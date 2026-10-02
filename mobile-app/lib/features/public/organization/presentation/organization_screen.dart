import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/person_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final officeBearersProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.officeBearers);
  return response['data'] as Map<String, dynamic>;
});

class OrganizationScreen extends ConsumerWidget {
  const OrganizationScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final bearersAsync = ref.watch(officeBearersProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: 'State Office Bearers',
        actions: [
          IconButton(
            icon: const Icon(Icons.history_edu),
            tooltip: 'Former Leaders',
            onPressed: () => context.push('/organization/former-leaders'),
          ),
          IconButton(
            icon: const Icon(Icons.map_outlined),
            tooltip: 'Districts',
            onPressed: () => context.push('/organization/districts'),
          ),
        ],
      ),
      body: bearersAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(officeBearersProvider),
        ),
        data: (data) {
          final sections = data['sections'] as Map<String, dynamic>? ?? {};

          if (sections.isEmpty) {
            return const EmptyState(
              title: 'No office bearers found',
              icon: Icons.people_outline,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(officeBearersProvider),
            child: ListView(
              padding: const EdgeInsets.symmetric(vertical: 12),
              children: sections.entries.map((entry) {
                final sectionTitle = entry.key;
                final members = entry.value as List<dynamic>? ?? [];

                return Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Padding(
                      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                      child: Text(
                        sectionTitle,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primaryDark,
                        ),
                      ),
                    ),
                    ...members.map((m) {
                      return PersonCard(
                        name: m['name']?.toString() ?? '',
                        designation: m['designation']?.toString() ?? '',
                        photoUrl: m['photo_url']?.toString(),
                        mobile: m['phone']?.toString() ??
                            m['mobile']?.toString(),
                        email: m['email']?.toString(),
                        district: m['district']?.toString(),
                      );
                    }),
                  ],
                );
              }).toList(),
            ),
          );
        },
      ),
    );
  }
}
