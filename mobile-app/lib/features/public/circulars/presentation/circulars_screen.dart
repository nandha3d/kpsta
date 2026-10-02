import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/filter_chips.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';

class CircularQueryParam {
  final String type;
  final String search;
  const CircularQueryParam({required this.type, required this.search});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is CircularQueryParam &&
          runtimeType == other.runtimeType &&
          type == other.type &&
          search == other.search;

  @override
  int get hashCode => type.hashCode ^ search.hashCode;
}

final circularListFamilyProvider =
    FutureProvider.family.autoDispose<List<dynamic>, CircularQueryParam>(
        (ref, param) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.circulars,
    queryParameters: {
      if (param.type != 'all') 'type': param.type,
      if (param.search.isNotEmpty) 'q': param.search,
      'limit': 50,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

class CircularsScreen extends ConsumerStatefulWidget {
  const CircularsScreen({super.key});

  @override
  ConsumerState<CircularsScreen> createState() => _CircularsScreenState();
}

class _CircularsScreenState extends ConsumerState<CircularsScreen> {
  String _selectedType = 'all';
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final param = CircularQueryParam(type: _selectedType, search: _searchQuery);
    final circularsAsync = ref.watch(circularListFamilyProvider(param));

    final types = [
      {'id': 'all', 'label': 'All Orders'},
      {'id': 'general', 'label': 'General Education'},
      {'id': 'hse', 'label': 'Higher Secondary (HSE)'},
      {'id': 'vhse', 'label': 'Vocational (VHSE)'},
    ];

    return Scaffold(
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
          FilterChipsBar<Map<String, String>>(
            items: types.sublist(1),
            selectedItem: types.firstWhere(
              (t) => t['id'] == _selectedType,
              orElse: () => types.first,
            ),
            allLabel: 'All Orders',
            labelBuilder: (t) => t['label']!,
            onSelected: (t) {
              setState(() {
                _selectedType = t != null ? t['id']! : 'all';
              });
            },
          ),
          const Divider(height: 1, color: AppColors.border),
          Expanded(
            child: circularsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(circularListFamilyProvider(param)),
              ),
              data: (items) {
                if (items.isEmpty) {
                  return const EmptyState(
                    title: 'No circulars found',
                    message: 'Try changing your category or search keywords.',
                    icon: Icons.description_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(circularListFamilyProvider(param)),
                  child: ListView.builder(
                    itemCount: items.length,
                    itemBuilder: (ctx, idx) {
                      final item = items[idx];
                      final id = item['id']?.toString() ?? '';
                      return DocumentRow(
                        title: item['title']?.toString() ?? '',
                        date: item['created_date']?.toString(),
                        category: item['category']?.toString() ??
                            item['type']?.toString().toUpperCase(),
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
