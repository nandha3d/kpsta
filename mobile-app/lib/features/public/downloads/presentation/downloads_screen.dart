import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/filter_chips.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';

class DownloadQueryParam {
  final String type;
  final String search;
  const DownloadQueryParam({required this.type, required this.search});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is DownloadQueryParam &&
          runtimeType == other.runtimeType &&
          type == other.type &&
          search == other.search;

  @override
  int get hashCode => type.hashCode ^ search.hashCode;
}

final downloadListFamilyProvider =
    FutureProvider.family.autoDispose<List<dynamic>, DownloadQueryParam>(
        (ref, param) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.downloads,
    queryParameters: {
      if (param.type != 'all') 'type': param.type,
      if (param.search.isNotEmpty) 'q': param.search,
      'limit': 50,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

class DownloadsScreen extends ConsumerStatefulWidget {
  const DownloadsScreen({super.key});

  @override
  ConsumerState<DownloadsScreen> createState() => _DownloadsScreenState();
}

class _DownloadsScreenState extends ConsumerState<DownloadsScreen> {
  String _selectedType = 'all';
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final param = DownloadQueryParam(type: _selectedType, search: _searchQuery);
    final downloadsAsync = ref.watch(downloadListFamilyProvider(param));

    final downloadTypes = [
      {'id': 'all', 'label': 'All Downloads'},
      {'id': 'forms', 'label': 'Forms'},
      {'id': 'act_rules', 'label': 'Acts & Rules'},
      {'id': 'software', 'label': 'Software'},
      {'id': 'fonts', 'label': 'Fonts'},
      {'id': 'notice_posters', 'label': 'Notice Posters'},
      {'id': 'melakal', 'label': 'Melakal'},
      {'id': 'academic_corner', 'label': 'Academic Corner'},
    ];

    return Scaffold(
      appBar: const AppTopBar(
        title: 'Downloads & Resources',
        showBackButton: false,
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search forms, software, acts...',
            onChanged: (q) {
              setState(() => _searchQuery = q.trim());
            },
          ),
          FilterChipsBar<Map<String, String>>(
            items: downloadTypes.sublist(1),
            selectedItem: downloadTypes.firstWhere(
              (t) => t['id'] == _selectedType,
              orElse: () => downloadTypes.first,
            ),
            allLabel: 'All Downloads',
            labelBuilder: (t) => t['label']!,
            onSelected: (t) {
              setState(() {
                _selectedType = t != null ? t['id']! : 'all';
              });
            },
          ),
          const Divider(height: 1, color: AppColors.border),
          Expanded(
            child: downloadsAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppColors.primary),
              ),
              error: (err, stack) => ErrorState(
                message: err.toString().replaceAll('ApiException: ', ''),
                onRetry: () => ref.refresh(downloadListFamilyProvider(param)),
              ),
              data: (items) {
                if (items.isEmpty) {
                  return const EmptyState(
                    title: 'No documents found',
                    message: 'Try selecting a different resource section.',
                    icon: Icons.file_download_off_outlined,
                  );
                }

                return RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: () async =>
                      ref.refresh(downloadListFamilyProvider(param)),
                  child: ListView.builder(
                    itemCount: items.length,
                    itemBuilder: (ctx, idx) {
                      final item = items[idx];
                      return DocumentRow(
                        title: item['title']?.toString() ?? '',
                        date: item['created_date']?.toString(),
                        category: item['category']?.toString() ??
                            item['type']?.toString().toUpperCase(),
                        fileUrl: item['file_url']?.toString(),
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
