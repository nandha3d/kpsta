import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/person_card.dart';
import '../../../auth/presentation/auth_providers.dart';

final districtsProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.districts);
  return response['data'] as List<dynamic>? ?? [];
});

class DistrictsScreen extends ConsumerStatefulWidget {
  const DistrictsScreen({super.key});

  @override
  ConsumerState<DistrictsScreen> createState() => _DistrictsScreenState();
}

class _DistrictsScreenState extends ConsumerState<DistrictsScreen> {
  int? _selectedDistrictIndex;

  @override
  Widget build(BuildContext context) {
    final districtsAsync = ref.watch(districtsProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Districts of Kerala'),
      body: districtsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(districtsProvider),
        ),
        data: (districts) {
          if (districts.isEmpty) {
            return const EmptyState(
              title: 'No districts found',
              icon: Icons.map_outlined,
            );
          }

          final selectedDistrict = _selectedDistrictIndex != null &&
                  _selectedDistrictIndex! < districts.length
              ? districts[_selectedDistrictIndex!]
              : null;

          final bearers =
              selectedDistrict?['bearers'] as List<dynamic>? ?? [];

          return Column(
            children: [
              // District Chips Row
              SizedBox(
                height: 52,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 8,
                  ),
                  itemCount: districts.length,
                  itemBuilder: (ctx, idx) {
                    final d = districts[idx];
                    final name = d['name']?.toString() ?? 'District';
                    final isSelected = _selectedDistrictIndex == idx;

                    return Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(name),
                        selected: isSelected,
                        selectedColor: AppColors.primary,
                        labelStyle: TextStyle(
                          color:
                              isSelected ? AppColors.white : AppColors.text,
                          fontWeight: isSelected
                              ? FontWeight.w600
                              : FontWeight.normal,
                          fontSize: 13,
                        ),
                        onSelected: (selected) {
                          setState(() {
                            _selectedDistrictIndex = selected ? idx : null;
                          });
                        },
                      ),
                    );
                  },
                ),
              ),
              const Divider(height: 1, color: AppColors.border),

              // Content Area
              Expanded(
                child: selectedDistrict == null
                    ? ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: districts.length,
                        itemBuilder: (ctx, idx) {
                          final d = districts[idx];
                          final name = d['name']?.toString() ?? '';
                          final count =
                              (d['bearers'] as List<dynamic>?)?.length ?? 0;

                          return Card(
                            margin: const EdgeInsets.only(bottom: 8),
                            child: ListTile(
                              leading: const CircleAvatar(
                                backgroundColor: AppColors.bgLight,
                                child: Icon(Icons.location_on,
                                    color: AppColors.primary, size: 20),
                              ),
                              title: Text(
                                name,
                                style: const TextStyle(
                                  fontWeight: FontWeight.w600,
                                  fontSize: 15,
                                ),
                              ),
                              subtitle: Text(
                                '$count Committee Bearers',
                                style: const TextStyle(
                                  color: AppColors.textMuted,
                                  fontSize: 13,
                                ),
                              ),
                              trailing: const Icon(
                                Icons.arrow_forward_ios,
                                size: 14,
                                color: AppColors.textLight,
                              ),
                              onTap: () {
                                setState(() {
                                  _selectedDistrictIndex = idx;
                                });
                              },
                            ),
                          );
                        },
                      )
                    : bearers.isEmpty
                        ? const EmptyState(
                            title: 'No committee bearers listed',
                            message:
                                'Information for this district committee will be updated soon.',
                            icon: Icons.person_off_outlined,
                          )
                        : ListView.builder(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            itemCount: bearers.length,
                            itemBuilder: (ctx, idx) {
                              final b = bearers[idx];
                              return PersonCard(
                                name: b['name']?.toString() ?? '',
                                designation:
                                    b['designation']?.toString() ?? '',
                                photoUrl: b['photo_url']?.toString(),
                                mobile: b['mobile']?.toString(),
                                email: b['email']?.toString(),
                                district: selectedDistrict['name']?.toString(),
                              );
                            },
                          ),
              ),
            ],
          );
        },
      ),
    );
  }
}
