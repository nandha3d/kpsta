import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/document_row.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/modern_pill_tab_bar.dart';
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
    FutureProvider.family<List<dynamic>, DownloadQueryParam>(
        (ref, param) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(
    ApiConstants.downloads,
    cacheDuration: const Duration(minutes: 5),
    queryParameters: {
      if (param.type != 'all') 'type': param.type,
      if (param.search.isNotEmpty) 'q': param.search,
      'limit': 50,
    },
  );
  return response['data'] as List<dynamic>? ?? [];
});

/// DownloadsScreen with swipable submenus across all resource categories
class DownloadsScreen extends ConsumerStatefulWidget {
  final String? initialTab;

  const DownloadsScreen({super.key, this.initialTab});

  @override
  ConsumerState<DownloadsScreen> createState() => _DownloadsScreenState();
}

class _DownloadsScreenState extends ConsumerState<DownloadsScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabController;
  String _searchQuery = '';

  static const List<Map<String, dynamic>> downloadTypes = [
    {'id': 'forms', 'label': 'Forms', 'icon': Icons.file_copy_outlined},
    {'id': 'softwares', 'label': 'Software Tools', 'icon': Icons.terminal_rounded},
    {'id': 'melakal', 'label': 'Melakal', 'icon': Icons.emoji_events_outlined},
  ];

  int _resolveTabIndex(String? tab) {
    if (tab == null || tab.isEmpty) return 0;
    final idx = downloadTypes.indexWhere((t) => t['id'] == tab);
    return idx >= 0 ? idx : 0;
  }

  @override
  void initState() {
    super.initState();
    _tabController = TabController(
      length: downloadTypes.length,
      initialIndex: _resolveTabIndex(widget.initialTab),
      vsync: this,
    );
  }

  @override
  void didUpdateWidget(covariant DownloadsScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.initialTab != oldWidget.initialTab && widget.initialTab != null) {
      final idx = _resolveTabIndex(widget.initialTab);
      if (idx != _tabController.index) {
        _tabController.animateTo(idx);
      }
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: AppTopBar(
        title: 'Downloads & Resources',
        showBackButton: false,
        bottom: ModernPillTabBar(
          controller: _tabController,
          tabs: downloadTypes
              .map(
                (t) => ModernPillTab(
                  label: t['label'] as String,
                  icon: t['icon'] as IconData?,
                ),
              )
              .toList(),
        ),
      ),
      body: Column(
        children: [
          SearchField(
            hintText: 'Search forms, software tools, melakal...',
            onChanged: (q) {
              setState(() => _searchQuery = q.trim());
            },
          ),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              physics: const BouncingScrollPhysics(),
              children: downloadTypes.map((t) {
                return DownloadCategoryListView(
                  type: t['id'] as String,
                  searchQuery: _searchQuery,
                );
              }).toList(),
            ),
          ),
        ],
      ),
    );
  }
}

class DownloadCategoryListView extends ConsumerWidget {
  final String type;
  final String searchQuery;

  const DownloadCategoryListView({
    super.key,
    required this.type,
    required this.searchQuery,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final param = DownloadQueryParam(type: type, search: searchQuery);
    final downloadsAsync = ref.watch(downloadListFamilyProvider(param));

    return downloadsAsync.when(
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
            message: 'No files uploaded under this category yet.',
            icon: Icons.file_download_off_outlined,
          );
        }

        return RefreshIndicator(
          color: AppColors.primary,
          onRefresh: () async => ref.refresh(downloadListFamilyProvider(param)),
          child: ListView.builder(
            padding: const EdgeInsets.only(top: 4, bottom: 120),
            itemCount: items.length,
            itemBuilder: (ctx, idx) {
              final item = items[idx];
              return DocumentRow(
                title: item['title']?.toString() ?? '',
                date: (item['date'] ?? item['created_date'])?.toString(),
                category: (item['category_name'] ??
                        item['category'] ??
                        item['type'])
                    ?.toString(),
                fileUrl: item['file_url']?.toString(),
              );
            },
          ),
        );
      },
    );
  }
}
