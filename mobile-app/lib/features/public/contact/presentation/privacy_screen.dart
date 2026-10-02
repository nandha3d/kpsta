import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final privacyPolicyProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.privacy);
  return response['data'] as Map<String, dynamic>;
});

class PrivacyScreen extends ConsumerWidget {
  const PrivacyScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final policyAsync = ref.watch(privacyPolicyProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Privacy & Terms'),
      body: policyAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(privacyPolicyProvider),
        ),
        data: (data) {
          final title =
              data['title']?.toString() ?? 'KPSTA Privacy & Data Policy';
          final content = data['content']?.toString() ??
              data['policy']?.toString() ??
              'Kerala Pradesh School Teachers’ Association (KPSTA) is committed to protecting member confidentiality and member privacy in accordance with applicable IT legislation.';

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const Divider(height: 24, color: AppColors.border),
                Text(
                  content.replaceAll(RegExp(r'<[^>]*>'), '').trim(),
                  style: const TextStyle(
                    fontSize: 14,
                    color: AppColors.text,
                    height: 1.6,
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
