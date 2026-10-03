import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/page_hero_banner.dart';
import '../../../../core/widgets/ribbon_header.dart';
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
      backgroundColor: Colors.white,
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

          final bearers = (selectedDistrict?['office_bearers'] ??
                  selectedDistrict?['bearers']) as List<dynamic>? ??
              [];

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(districtsProvider),
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                const PageHeroBanner(
                  title: 'DISTRICT COMMITTEES',
                  height: 100,
                ),

                // District Chips Horizontal Bar
                Container(
                  color: const Color(0xFFF1F5F9),
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  child: SizedBox(
                    height: 44,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16),
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
                            selectedColor: const Color(0xFF016D77),
                            labelStyle: TextStyle(
                              color: isSelected ? Colors.white : const Color(0xFF0F172A),
                              fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                              fontSize: 12.5,
                            ),
                            backgroundColor: Colors.white,
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
                ),

                // Selected District View or All Districts Grid
                if (selectedDistrict == null) ...[
                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 16),
                    child: RibbonHeader(
                      title: 'All 14 Revenue Districts',
                      color: Color(0xFF016D77),
                      margin: EdgeInsets.only(top: 18, bottom: 12),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        crossAxisSpacing: 10,
                        mainAxisSpacing: 10,
                        childAspectRatio: 2.2,
                      ),
                      itemCount: districts.length,
                      itemBuilder: (ctx, idx) {
                        final d = districts[idx];
                        final name = d['name']?.toString() ?? '';
                        final count = (d['office_bearers'] as List<dynamic>?)?.length ??
                            (d['bearers'] as List<dynamic>?)?.length ?? 0;

                        return InkWell(
                          onTap: () {
                            setState(() {
                              _selectedDistrictIndex = idx;
                            });
                          },
                          borderRadius: BorderRadius.circular(10),
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF8FAFC),
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: Row(
                              children: [
                                const CircleAvatar(
                                  radius: 18,
                                  backgroundColor: Color(0xFF016D77),
                                  child: Icon(Icons.location_on, color: Colors.white, size: 18),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Text(
                                        name,
                                        style: const TextStyle(
                                          fontWeight: FontWeight.w700,
                                          fontSize: 13,
                                          color: Color(0xFF0F1E24),
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                      Text(
                                        '$count Bearers',
                                        style: const TextStyle(
                                          fontSize: 11,
                                          color: Color(0xFF64748B),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ] else ...[
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: RibbonHeader(
                      title: "${selectedDistrict['name']} District Committee",
                      color: const Color(0xFF016D77),
                      margin: const EdgeInsets.only(top: 18, bottom: 14),
                    ),
                  ),
                  if (bearers.isEmpty)
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 24),
                      child: EmptyState(
                        title: 'No committee bearers listed',
                        message: 'Committee information for this district will be updated soon.',
                        icon: Icons.person_off_outlined,
                      ),
                    )
                  else
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: Wrap(
                        alignment: WrapAlignment.spaceEvenly,
                        spacing: 12,
                        runSpacing: 16,
                        children: bearers.map((b) {
                          return BearerCard(
                            name: b['name']?.toString() ?? '',
                            designation: b['designation']?.toString() ?? '',
                            photoUrl: b['photo_url']?.toString(),
                            phone: b['phone']?.toString() ?? b['mobile']?.toString(),
                            width: 155,
                            photoHeight: 160,
                          );
                        }).toList(),
                      ),
                    ),
                ],

                const SizedBox(height: 36),
                const KpstaFooter(),
              ],
            ),
          );
        },
      ),
    );
  }
}
