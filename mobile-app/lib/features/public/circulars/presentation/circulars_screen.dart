import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';

class CircularQueryParam {
  final String search;
  final int? year;
  final int? month;
  final String? categoryId;

  const CircularQueryParam({
    this.search = '',
    this.year,
    this.month,
    this.categoryId,
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is CircularQueryParam &&
          runtimeType == other.runtimeType &&
          search == other.search &&
          year == other.year &&
          month == other.month &&
          categoryId == other.categoryId;

  @override
  int get hashCode =>
      search.hashCode ^ year.hashCode ^ month.hashCode ^ categoryId.hashCode;
}

class CircularListResult {
  final List<dynamic> items;
  final List<int> years;

  const CircularListResult({required this.items, required this.years});
}

final circularCategoriesProvider =
    FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.circularCategories,
    cacheDuration: const Duration(minutes: 10),
    queryParameters: {'all': 1},
  );
  final raw = response['data'] as List<dynamic>? ?? [];
  return raw
      .whereType<Map>()
      .map((e) => Map<String, dynamic>.from(e))
      .toList();
});

final circularListFamilyProvider =
    FutureProvider.family<CircularListResult, CircularQueryParam>(
        (ref, param) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.circulars,
    cacheDuration: const Duration(minutes: 5),
    queryParameters: {
      'type': 'all',
      if (param.search.isNotEmpty) 'search': param.search,
      if (param.search.isNotEmpty) 'q': param.search,
      if (param.year != null) 'year': param.year,
      if (param.month != null) 'month': param.month,
      if (param.categoryId != null && param.categoryId!.isNotEmpty)
        'category': param.categoryId,
      'per_page': 50,
      'limit': 50,
    },
  );

  final items = response['data'] as List<dynamic>? ?? [];
  final meta = response['meta'] as Map<String, dynamic>? ?? {};
  final rawYears = meta['years'] as List<dynamic>? ?? [];
  final years = rawYears
      .map((y) => int.tryParse(y.toString()))
      .whereType<int>()
      .toList();

  return CircularListResult(items: items, years: years);
});

/// Unified Orders & Circulars screen with Year, Month, and searchable Label filters
/// matching the main KPSTA website.
class CircularsScreen extends ConsumerStatefulWidget {
  const CircularsScreen({super.key});

  @override
  ConsumerState<CircularsScreen> createState() => _CircularsScreenState();
}

class _CircularsScreenState extends ConsumerState<CircularsScreen> {
  String _searchQuery = '';
  int? _selectedYear;
  int? _selectedMonth;
  String? _selectedCategoryId;
  String? _selectedCategoryName;
  List<int> _cachedYears = [];

  static const List<Map<String, dynamic>> _months = [
    {'id': 1, 'name': 'January', 'short': 'Jan'},
    {'id': 2, 'name': 'February', 'short': 'Feb'},
    {'id': 3, 'name': 'March', 'short': 'Mar'},
    {'id': 4, 'name': 'April', 'short': 'Apr'},
    {'id': 5, 'name': 'May', 'short': 'May'},
    {'id': 6, 'name': 'June', 'short': 'Jun'},
    {'id': 7, 'name': 'July', 'short': 'Jul'},
    {'id': 8, 'name': 'August', 'short': 'Aug'},
    {'id': 9, 'name': 'September', 'short': 'Sep'},
    {'id': 10, 'name': 'October', 'short': 'Oct'},
    {'id': 11, 'name': 'November', 'short': 'Nov'},
    {'id': 12, 'name': 'December', 'short': 'Dec'},
  ];

  bool get _hasActiveFilters =>
      _selectedYear != null ||
      _selectedMonth != null ||
      (_selectedCategoryId != null && _selectedCategoryId!.isNotEmpty);

  void _clearFilters() {
    setState(() {
      _selectedYear = null;
      _selectedMonth = null;
      _selectedCategoryId = null;
      _selectedCategoryName = null;
    });
  }

  List<int> _fallbackYears() {
    final nowYear = DateTime.now().year;
    final count = (nowYear - 1988) + 1;
    return List.generate(count > 0 ? count : 12, (i) => nowYear - i);
  }

  String _selectedMonthLabel() {
    if (_selectedMonth == null) return 'Month';
    for (final m in _months) {
      if (m['id'] == _selectedMonth) {
        return m['short'] as String;
      }
    }
    return 'Month';
  }

  Future<void> _openYearPicker(List<int> years) async {
    final sortedYears = List<int>.from(years);
    if (_selectedYear != null && !sortedYears.contains(_selectedYear)) {
      sortedYears.add(_selectedYear!);
      sortedYears.sort((a, b) => b.compareTo(a));
    }

    await showModalBottomSheet<void>(
      context: context,
      useRootNavigator: true,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        final maxH = MediaQuery.of(ctx).size.height * 0.65;
        return SafeArea(
          top: false,
          child: ConstrainedBox(
            constraints: BoxConstraints(maxHeight: maxH),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const SizedBox(height: 10),
                Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: const Color(0xFFCBD5E1),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 12, 8, 8),
                  child: Row(
                    children: [
                      const Icon(Icons.calendar_today_rounded,
                          color: AppColors.primary, size: 20),
                      const SizedBox(width: 8),
                      const Expanded(
                        child: Text(
                          'Select Year',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primaryDark,
                          ),
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close_rounded,
                            color: Color(0xFF64748B)),
                        onPressed: () => Navigator.of(ctx).pop(),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1),
                Expanded(
                  child: Scrollbar(
                    thumbVisibility: true,
                    child: ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(),
                      padding: const EdgeInsets.only(bottom: 16),
                      itemCount: sortedYears.length + 1,
                      itemBuilder: (_, idx) {
                        if (idx == 0) {
                          final isSelected = _selectedYear == null;
                          return ListTile(
                            minTileHeight: 48,
                            title: Text(
                              'All Years',
                              style: TextStyle(
                                fontWeight: isSelected
                                    ? FontWeight.w700
                                    : FontWeight.w500,
                                color: isSelected
                                    ? AppColors.primary
                                    : const Color(0xFF1E293B),
                              ),
                            ),
                            trailing: isSelected
                                ? const Icon(Icons.check_rounded,
                                    color: AppColors.primary)
                                : null,
                            onTap: () {
                              setState(() => _selectedYear = null);
                              Navigator.of(ctx).pop();
                            },
                          );
                        }
                        final yr = sortedYears[idx - 1];
                        final isSelected = _selectedYear == yr;
                        return ListTile(
                          minTileHeight: 48,
                          title: Text(
                            '$yr',
                            style: TextStyle(
                              fontWeight: isSelected
                                  ? FontWeight.w700
                                  : FontWeight.w500,
                              color: isSelected
                                  ? AppColors.primary
                                  : const Color(0xFF1E293B),
                            ),
                          ),
                          trailing: isSelected
                              ? const Icon(Icons.check_rounded,
                                  color: Color(0xFFEE6A2A))
                              : null,
                          onTap: () {
                            setState(() => _selectedYear = yr);
                            Navigator.of(ctx).pop();
                          },
                        );
                      },
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _openMonthPicker() async {
    await showModalBottomSheet<void>(
      context: context,
      useRootNavigator: true,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        final maxH = MediaQuery.of(ctx).size.height * 0.70;
        return SafeArea(
          top: false,
          child: ConstrainedBox(
            constraints: BoxConstraints(maxHeight: maxH),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const SizedBox(height: 10),
                Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: const Color(0xFFCBD5E1),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 12, 8, 8),
                  child: Row(
                    children: [
                      const Icon(Icons.date_range_rounded,
                          color: AppColors.primary, size: 20),
                      const SizedBox(width: 8),
                      const Expanded(
                        child: Text(
                          'Select Month',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: AppColors.primaryDark,
                          ),
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close_rounded,
                            color: Color(0xFF64748B)),
                        onPressed: () => Navigator.of(ctx).pop(),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1),
                Expanded(
                  child: Scrollbar(
                    thumbVisibility: true,
                    child: ListView.builder(
                      physics: const AlwaysScrollableScrollPhysics(),
                      padding: const EdgeInsets.only(bottom: 16),
                      itemCount: _months.length + 1,
                      itemBuilder: (_, idx) {
                        if (idx == 0) {
                          final isSelected = _selectedMonth == null;
                          return ListTile(
                            minTileHeight: 48,
                            title: Text(
                              'All Months',
                              style: TextStyle(
                                fontWeight: isSelected
                                    ? FontWeight.w700
                                    : FontWeight.w500,
                                color: isSelected
                                    ? AppColors.primary
                                    : const Color(0xFF1E293B),
                              ),
                            ),
                            trailing: isSelected
                                ? const Icon(Icons.check_rounded,
                                    color: AppColors.primary)
                                : null,
                            onTap: () {
                              setState(() => _selectedMonth = null);
                              Navigator.of(ctx).pop();
                            },
                          );
                        }
                        final m = _months[idx - 1];
                        final mId = m['id'] as int;
                        final mName = m['name'] as String;
                        final isSelected = _selectedMonth == mId;
                        return ListTile(
                          minTileHeight: 48,
                          title: Text(
                            mName,
                            style: TextStyle(
                              fontWeight: isSelected
                                  ? FontWeight.w700
                                  : FontWeight.w500,
                              color: isSelected
                                  ? AppColors.primary
                                  : const Color(0xFF1E293B),
                            ),
                          ),
                          trailing: isSelected
                              ? const Icon(Icons.check_rounded,
                                  color: Color(0xFFEE6A2A))
                              : null,
                          onTap: () {
                            setState(() => _selectedMonth = mId);
                            Navigator.of(ctx).pop();
                          },
                        );
                      },
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _openLabelPicker(List<Map<String, dynamic>> categories) async {
    String filterText = '';
    await showModalBottomSheet<void>(
      context: context,
      useRootNavigator: true,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setSheetState) {
            final filtered = categories.where((c) {
              final name = (c['name'] ?? '').toString().toLowerCase();
              return filterText.isEmpty || name.contains(filterText);
            }).toList();

            return SafeArea(
              top: false,
              child: DraggableScrollableSheet(
                initialChildSize: 0.65,
                minChildSize: 0.4,
                maxChildSize: 0.9,
                expand: false,
                builder: (_, scrollController) {
                  return Column(
                    children: [
                      const SizedBox(height: 10),
                      Container(
                        width: 40,
                        height: 4,
                        decoration: BoxDecoration(
                          color: const Color(0xFFCBD5E1),
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                      const Padding(
                        padding: EdgeInsets.fromLTRB(16, 14, 16, 8),
                        child: Row(
                          children: [
                            Icon(Icons.label_outline_rounded,
                                color: AppColors.primary, size: 20),
                            SizedBox(width: 8),
                            Text(
                              'Search By Label',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w800,
                                color: AppColors.primaryDark,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 6),
                        child: TextField(
                          autofocus: false,
                          decoration: InputDecoration(
                            hintText: 'Type to search labels...',
                            prefixIcon: const Icon(Icons.search, size: 20),
                            isDense: true,
                            filled: true,
                            fillColor: const Color(0xFFF8FAFC),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide:
                                  const BorderSide(color: Color(0xFFCBD5E1)),
                            ),
                            contentPadding: const EdgeInsets.symmetric(
                                horizontal: 12, vertical: 10),
                          ),
                          onChanged: (val) {
                            setSheetState(() {
                              filterText = val.trim().toLowerCase();
                            });
                          },
                        ),
                      ),
                      const Divider(height: 1),
                      Expanded(
                        child: Scrollbar(
                          controller: scrollController,
                          thumbVisibility: true,
                          child: ListView(
                            controller: scrollController,
                            physics: const AlwaysScrollableScrollPhysics(),
                            children: [
                              ListTile(
                                minTileHeight: 48,
                                title: const Text(
                                  'All Labels',
                                  style: TextStyle(fontWeight: FontWeight.w700),
                                ),
                                trailing: _selectedCategoryId == null
                                    ? const Icon(Icons.check_rounded,
                                        color: AppColors.primary)
                                    : null,
                                onTap: () {
                                  setState(() {
                                    _selectedCategoryId = null;
                                    _selectedCategoryName = null;
                                  });
                                  Navigator.of(ctx).pop();
                                },
                              ),
                              ...filtered.map((cat) {
                                final id = cat['id']?.toString() ?? '';
                                final name = cat['name']?.toString() ?? '';
                                final isSelected = _selectedCategoryId == id;
                                return ListTile(
                                  minTileHeight: 48,
                                  title: Text(
                                    name,
                                    style: TextStyle(
                                      fontWeight: isSelected
                                          ? FontWeight.w700
                                          : FontWeight.w500,
                                      color: isSelected
                                          ? AppColors.primary
                                          : const Color(0xFF1E293B),
                                    ),
                                  ),
                                  trailing: isSelected
                                      ? const Icon(Icons.check_rounded,
                                          color: Color(0xFFEE6A2A))
                                      : null,
                                  onTap: () {
                                    setState(() {
                                      _selectedCategoryId = id;
                                      _selectedCategoryName = name;
                                    });
                                    Navigator.of(ctx).pop();
                                  },
                                );
                              }),
                            ],
                          ),
                        ),
                      ),
                    ],
                  );
                },
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final param = CircularQueryParam(
      search: _searchQuery,
      year: _selectedYear,
      month: _selectedMonth,
      categoryId: _selectedCategoryId,
    );
    final circularsAsync = ref.watch(circularListFamilyProvider(param));
    final categoriesAsync = ref.watch(circularCategoriesProvider);

    final loadedYears = circularsAsync.maybeWhen(
      data: (res) => res.years,
      orElse: () => const <int>[],
    );
    if (loadedYears.isNotEmpty) {
      _cachedYears = loadedYears;
    }
    final availableYears =
        _cachedYears.isNotEmpty ? _cachedYears : _fallbackYears();
    final categories = categoriesAsync.maybeWhen(
      data: (cats) => cats,
      orElse: () => <Map<String, dynamic>>[],
    );

    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: const AppTopBar(
        title: 'Orders & Circulars',
        showBackButton: false,
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search order number or subject...',
            onChanged: (q) {
              setState(() => _searchQuery = q.trim());
            },
          ),
          // Year, Month & Label Filter Toolbar (matching main website)
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
            child: Row(
              children: [
                // Year Picker
                Expanded(
                  flex: 2,
                  child: _FilterPickerButton(
                    label: _selectedYear != null ? '$_selectedYear' : 'Year',
                    icon: Icons.calendar_today_rounded,
                    isSelected: _selectedYear != null,
                    onTap: () => _openYearPicker(availableYears),
                  ),
                ),
                const SizedBox(width: 8),
                // Month Picker
                Expanded(
                  flex: 2,
                  child: _FilterPickerButton(
                    label: _selectedMonthLabel(),
                    icon: Icons.date_range_rounded,
                    isSelected: _selectedMonth != null,
                    onTap: _openMonthPicker,
                  ),
                ),
                const SizedBox(width: 8),
                // Label Searchable Picker Trigger
                Expanded(
                  flex: 3,
                  child: _FilterPickerButton(
                    label: _selectedCategoryName ?? 'Label',
                    icon: Icons.label_outline_rounded,
                    isSelected: _selectedCategoryId != null,
                    onTap: () => _openLabelPicker(categories),
                  ),
                ),
              ],
            ),
          ),
          if (_hasActiveFilters)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 6),
              child: Align(
                alignment: Alignment.centerRight,
                child: InkWell(
                  onTap: _clearFilters,
                  borderRadius: BorderRadius.circular(16),
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFFF1F2),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFFFE4E6)),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.close_rounded,
                            size: 14, color: Color(0xFFE11D48)),
                        SizedBox(width: 4),
                        Text(
                          'Clear Filters',
                          style: TextStyle(
                            fontSize: 11.5,
                            fontWeight: FontWeight.w700,
                            color: Color(0xFFE11D48),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          Expanded(
            child: circularsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(circularListFamilyProvider(param)),
              ),
              data: (res) {
                final items = res.items;
                if (items.isEmpty) {
                  return const EmptyState(
                    title: 'No circulars found',
                    message:
                        'No official orders matching your search or filter.',
                    icon: Icons.description_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(circularListFamilyProvider(param)),
                  child: ListView.builder(
                    padding: const EdgeInsets.only(top: 4, bottom: 120),
                    itemCount: items.length,
                    itemBuilder: (ctx, idx) {
                      final item = items[idx];
                      final id = item['id']?.toString() ?? '';
                      return DocumentRow(
                        title: item['order_no'] != null &&
                                item['order_no'].toString().isNotEmpty
                            ? "${item['order_no']} - ${item['title'] ?? ''}"
                            : (item['title']?.toString() ?? ''),
                        date:
                            (item['date'] ?? item['created_date'])?.toString(),
                        category: (item['category_name'] ??
                                item['category'] ??
                                item['type'])
                            ?.toString(),
                        fileUrl: item['file_url']?.toString(),
                        onTap: () => context.push('/circulars/$id'),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _FilterPickerButton extends StatelessWidget {
  final String label;
  final IconData icon;
  final bool isSelected;
  final VoidCallback onTap;

  const _FilterPickerButton({
    required this.label,
    required this.icon,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTap,
        child: Container(
          height: 40,
          padding: const EdgeInsets.symmetric(horizontal: 10),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: isSelected ? AppColors.primary : const Color(0xFFCBD5E1),
              width: isSelected ? 1.5 : 1.1,
            ),
          ),
          child: Row(
            children: [
              Icon(
                icon,
                size: 15,
                color: isSelected ? AppColors.primary : const Color(0xFF64748B),
              ),
              const SizedBox(width: 5),
              Expanded(
                child: Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w600,
                    color: isSelected
                        ? AppColors.primaryDark
                        : const Color(0xFF475569),
                  ),
                ),
              ),
              const Icon(
                Icons.arrow_drop_down_rounded,
                size: 20,
                color: Color(0xFF64748B),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
