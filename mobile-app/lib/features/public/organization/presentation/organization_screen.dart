import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import '../../../../core/widgets/modern_pill_tab_bar.dart';
import '../../../../core/widgets/ribbon_header.dart';
import '../../../../core/widgets/search_field.dart';
import '../../../auth/presentation/auth_providers.dart';
import '../../downloads/presentation/downloads_screen.dart';
import 'districts_screen.dart';
import 'former_leaders_screen.dart';

final officeBearersProvider =
    FutureProvider<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.officeBearers, cacheDuration: const Duration(minutes: 5));
  return response['data'] as Map<String, dynamic>;
});

/// OrganizationScreen with 3-in-a-line responsive office bearers and swipable submenus
/// (State Bearers, Districts, Former Leaders, Representations, Notices & Posters).
class OrganizationScreen extends ConsumerStatefulWidget {
  final String? initialTab;

  const OrganizationScreen({super.key, this.initialTab});

  @override
  ConsumerState<OrganizationScreen> createState() => _OrganizationScreenState();
}

class _OrganizationScreenState extends ConsumerState<OrganizationScreen>
    with SingleTickerProviderStateMixin {
  late final TabController _tabController;
  String _memoSearchQuery = '';
  String _noticeSearchQuery = '';

  static const List<String> _tabIds = [
    'bearers',
    'districts',
    'former_leaders',
    'memorandums',
    'notice_poster',
  ];

  int _resolveTabIndex(String? tab) {
    if (tab == null || tab.isEmpty) return 0;
    final idx = _tabIds.indexOf(tab);
    return idx >= 0 ? idx : 0;
  }

  @override
  void initState() {
    super.initState();
    _tabController = TabController(
      length: _tabIds.length,
      initialIndex: _resolveTabIndex(widget.initialTab),
      vsync: this,
    );
  }

  @override
  void didUpdateWidget(covariant OrganizationScreen oldWidget) {
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
        title: 'KPSTA Organization',
        showBackButton: false,
        bottom: ModernPillTabBar(
          controller: _tabController,
          tabs: const [
            ModernPillTab(label: 'State Bearers', icon: Icons.groups_rounded),
            ModernPillTab(label: 'Districts', icon: Icons.location_city_rounded),
            ModernPillTab(label: 'Former Leaders', icon: Icons.history_edu_rounded),
            ModernPillTab(label: 'Representations', icon: Icons.assignment_outlined),
            ModernPillTab(label: 'Notices & Posters', icon: Icons.campaign_outlined),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        physics: const BouncingScrollPhysics(),
        children: [
          // Submenu 1: State Office Bearers (3 images per line responsive)
          _buildStateBearersTab(context),

          // Submenu 2: District Committees (Swipable)
          const DistrictsView(),

          // Submenu 3: Former Leaders (Swipable)
          const FormerLeadersView(),

          // Submenu 4: Representations / Memorandums
          Column(
            children: [
              SearchField(
                hintText: 'Search representations & memorandums...',
                onChanged: (q) => setState(() => _memoSearchQuery = q.trim()),
              ),
              Expanded(
                child: DownloadCategoryListView(
                  type: 'memorandums',
                  searchQuery: _memoSearchQuery,
                ),
              ),
            ],
          ),

          // Submenu 5: Notices & Posters
          Column(
            children: [
              SearchField(
                hintText: 'Search notices & posters...',
                onChanged: (q) => setState(() => _noticeSearchQuery = q.trim()),
              ),
              Expanded(
                child: DownloadCategoryListView(
                  type: 'notice_poster',
                  searchQuery: _noticeSearchQuery,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStateBearersTab(BuildContext context) {
    final bearersAsync = ref.watch(officeBearersProvider);

    return bearersAsync.when(
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
          child: LayoutBuilder(
            builder: (context, constraints) {
              // 3 images per line by default on phone screens, responsive for tablet & desktop
              final crossAxisCount = constraints.maxWidth < 600
                  ? 3
                  : (constraints.maxWidth < 900 ? 4 : 6);

              return ListView(
                padding: const EdgeInsets.only(bottom: 130), // accommodates floating nav bar
                children: [
                  // Compact Modern Header Banner
                  Container(
                    margin: const EdgeInsets.fromLTRB(14, 8, 14, 6),
                    padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 14),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF0F2C33), Color(0xFF01555E)],
                      ),
                      borderRadius: BorderRadius.circular(14),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.08),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: const Center(
                      child: Text(
                        'STATE OFFICE BEARERS',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w800,
                          fontSize: 14,
                          letterSpacing: 0.8,
                        ),
                      ),
                    ),
                  ),

                  // Render Sections with Pill-shaped Title Bar & Zero Dead Space
                  ...sections.entries.map((entry) {
                    final sectionTitle = entry.key;
                    final members = entry.value as List<dynamic>? ?? [];

                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          child: Center(
                            child: RibbonHeader(
                              title: sectionTitle,
                              color: const Color(0xFF016D77),
                              margin: const EdgeInsets.only(top: 8, bottom: 4),
                            ),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 8),
                          child: Column(
                            children: [
                              for (var i = 0; i < members.length; i += crossAxisCount)
                                Padding(
                                  padding: const EdgeInsets.only(bottom: 6),
                                  child: Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: List.generate(crossAxisCount, (colIdx) {
                                      final itemIdx = i + colIdx;
                                      if (itemIdx < members.length) {
                                        final m = members[itemIdx];
                                        return Expanded(
                                          child: Padding(
                                            padding: const EdgeInsets.symmetric(horizontal: 3),
                                            child: BearerCard(
                                              name: m['name']?.toString() ?? '',
                                              designation: m['designation']?.toString() ?? '',
                                              photoUrl: m['photo_url']?.toString(),
                                              phone: m['phone']?.toString() ?? m['mobile']?.toString(),
                                              year: m['year']?.toString(),
                                              aspectRatio: 1.0,
                                            ),
                                          ),
                                        );
                                      } else {
                                        return const Expanded(child: SizedBox());
                                      }
                                    }),
                                  ),
                                ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 6), // Tight, balanced section spacing
                      ],
                    );
                  }),

                  const SizedBox(height: 20),
                  const KpstaFooter(),
                ],
              );
            },
          ),
        );
      },
    );
  }
}
