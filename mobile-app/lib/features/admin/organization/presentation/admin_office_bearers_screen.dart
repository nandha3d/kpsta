import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/person_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminBearersProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminOfficeBearers);
  return response['data'] as List<dynamic>? ?? [];
});

class AdminOfficeBearersScreen extends ConsumerWidget {
  const AdminOfficeBearersScreen({super.key});

  Future<void> _deleteBearer(
    BuildContext context,
    WidgetRef ref,
    String id,
    String name,
  ) async {
    final confirmed = await ConfirmationDialog.show(
      context,
      title: 'Remove Office Bearer',
      message: 'Are you sure you want to remove "$name" from leadership list?',
      confirmLabel: 'Remove',
      isDestructive: true,
    );

    if (confirmed) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminOfficeBearers}/$id');
        ref.refresh(adminBearersProvider);
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Office bearer removed'),
              backgroundColor: AppColors.success,
            ),
          );
        }
      } catch (e) {
        if (context.mounted) {
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

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final bearersAsync = ref.watch(adminBearersProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Manage State Leaders'),
      body: bearersAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminBearersProvider),
        ),
        data: (bearers) {
          if (bearers.isEmpty) {
            return const EmptyState(
              title: 'No office bearers found',
              icon: Icons.people_outline,
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminBearersProvider),
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: 12),
              itemCount: bearers.length,
              itemBuilder: (ctx, idx) {
                final b = bearers[idx];
                final id = b['id']?.toString() ?? '';
                final name = b['name']?.toString() ?? '';

                return Stack(
                  children: [
                    PersonCard(
                      name: name,
                      designation: b['designation']?.toString() ?? '',
                      photoUrl: b['photo_url']?.toString(),
                      mobile: b['mobile']?.toString(),
                      email: b['email']?.toString(),
                      district: b['district']?.toString(),
                    ),
                    Positioned(
                      top: 10,
                      right: 24,
                      child: IconButton(
                        icon: const Icon(Icons.delete_outline,
                            color: AppColors.error, size: 20),
                        tooltip: 'Delete',
                        onPressed: () =>
                            _deleteBearer(context, ref, id, name),
                      ),
                    ),
                  ],
                );
              },
            ),
          );
        },
      ),
    );
  }
}
