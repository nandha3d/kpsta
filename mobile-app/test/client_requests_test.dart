import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:kpsta_app/features/public/home/presentation/home_screen.dart';
import 'package:kpsta_app/features/public/home/presentation/widgets/quick_menu_widget.dart';
import 'package:kpsta_app/features/public/home/presentation/widgets/reaction_gallery_popup_dialog.dart';
import 'package:kpsta_app/features/public/organization/presentation/chronicle_screen.dart';
import 'package:kpsta_app/features/public/organization/presentation/former_leaders_screen.dart';
import 'package:kpsta_app/features/public/organization/presentation/organization_screen.dart';
import 'package:kpsta_app/features/public/circulars/presentation/circulars_screen.dart';
import 'package:kpsta_app/features/public/downloads/presentation/downloads_screen.dart';
import 'package:kpsta_app/core/widgets/app_top_bar.dart';
import 'package:kpsta_app/core/widgets/flag_background_wrapper.dart';
import 'package:kpsta_app/core/widgets/modern_pill_tab_bar.dart';
import 'package:kpsta_app/core/widgets/search_field.dart';
import 'package:kpsta_app/core/widgets/news_ticker_bar.dart';
import 'package:kpsta_app/features/public/home/presentation/more_menu_screen.dart';

void main() {
  testWidgets('HomeScreen displays Chronicle of KPSTA instead of Office Bearers and shows KPSTA Services & Portals', (tester) async {
    tester.view.physicalSize = const Size(800, 1600);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final mockHome = {
      'sliders': [
        {'id': 1, 'image_url': '', 'title': 'KPSTA Slider'}
      ],
      'flash_news': [
        {'id': 1, 'title': 'Flash Announcement'}
      ],
      'latest_news': [
        {'id': 1, 'title': 'Latest News Title', 'published_at': '2026-11-13'}
      ],
      'circulars': [
        {'id': 1, 'title': 'Circular 1', 'category': 'General'}
      ],
      'office_bearers': [
        {'id': 1, 'name': 'PK Aravindan', 'designation': 'PRESIDENT'}
      ],
      'reaction_gallery': [],
      'gallery_highlights': [],
      'services': [],
      'reaction_popup': {'enabled': false},
    };

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          homeDataProvider.overrideWith((ref) async => mockHome),
        ],
        child: const MaterialApp(
          home: HomeScreen(),
        ),
      ),
    );

    await tester.pump();
    await tester.pump(const Duration(milliseconds: 150));

    expect(find.text('Chronicle of KPSTA'), findsOneWidget);
    expect(find.text('Office Bearers'), findsNothing);
    expect(find.text('PK Aravindan'), findsOneWidget);
    expect(find.text('More Office Bearers'), findsOneWidget);
    expect(find.text('Website Directory'), findsOneWidget);
    expect(find.text('10 Services'), findsNothing);
    expect(find.byIcon(Icons.person_rounded), findsNothing);
  });

  testWidgets('QuickMenuWidget expands and displays all 10 client-requested services', (tester) async {
    tester.view.physicalSize = const Size(800, 1600);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: SingleChildScrollView(
            child: QuickMenuWidget(isAuthenticated: false),
          ),
        ),
      ),
    );

    expect(find.text('Website Directory'), findsOneWidget);

    await tester.tap(find.text('Website Directory'));
    await tester.pumpAndSettle();

    const expectedMenus = [
      'Membership & Adyapaka Sabdham',
      'Representation (Memorandum)',
      'Notice & Posters',
      'Forms',
      'Software Tools',
      'Melakal',
      'Service Corner',
      'Academic Corner',
      'Online Links',
      'Contact Us',
    ];

    for (final label in expectedMenus) {
      expect(find.text(label), findsOneWidget, reason: 'Expected service menu item: $label');
    }
  });

  testWidgets('ChronicleScreen renders organizational heritage and pillars', (tester) async {
    tester.view.physicalSize = const Size(800, 1600);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          formerLeadersProvider.overrideWith((ref) async => []),
        ],
        child: const MaterialApp(
          home: ChronicleScreen(),
        ),
      ),
    );

    await tester.pump();
    await tester.pump(const Duration(milliseconds: 100));

    expect(find.text('CHRONICLE OF KPSTA'), findsOneWidget);
    expect(find.text('National & International Affiliations'), findsOneWidget);
    expect(find.textContaining('KPSTA Bhavan'), findsWidgets);
  });

  testWidgets('ReactionGalleryPopupDialog switches between Reaction Poster and News Window', (tester) async {
    tester.view.physicalSize = const Size(800, 1400);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final popupItems = [
      {
        'type': 'reaction',
        'id': 1,
        'title': 'State Conference Poster',
        'image_url': '',
        'route': '/reaction-gallery',
      },
      {
        'type': 'news',
        'id': 42,
        'title': 'Important Transfer Circular News',
        'description': 'Official statement regarding transfer guidelines.',
        'image_url': '',
        'route': '/news/42',
        'published_at': '2026-11-13T10:00:00+05:30',
      },
    ];

    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: ReactionGalleryPopupDialog(items: popupItems),
        ),
      ),
    );

    expect(find.text('KPSTA Reaction Poster'), findsOneWidget);
    expect(find.text('Reaction Gallery →'), findsOneWidget);

    await tester.tap(find.text('News Window'));
    await tester.pumpAndSettle();

    expect(find.text('KPSTA News Bulletin'), findsOneWidget);
    expect(find.text('Important Transfer Circular News'), findsOneWidget);
    expect(find.text('Read News →'), findsOneWidget);
  });

  testWidgets('DownloadsScreen renders ModernPillTabBar and switches categories smoothly', (tester) async {
    tester.view.physicalSize = const Size(420, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          downloadListFamilyProvider(
            const DownloadQueryParam(type: 'all', search: ''),
          ).overrideWith((ref) async => []),
          downloadListFamilyProvider(
            const DownloadQueryParam(type: 'melakal', search: ''),
          ).overrideWith((ref) async => [
                {
                  'id': 11,
                  'title': 'State Kalolsavam Manual',
                  'category': 'Melakal',
                  'date': '13-11-2026',
                }
              ]),
        ],
        child: const MaterialApp(
          home: DownloadsScreen(initialTab: 'melakal'),
        ),
      ),
    );

    await tester.pumpAndSettle();

    expect(find.byType(ModernPillTabBar), findsOneWidget);
    expect(find.text('Forms'), findsOneWidget);
    expect(find.text('Software Tools'), findsOneWidget);
    expect(find.text('Melakal'), findsWidgets);
    expect(find.text('Memorandums'), findsNothing);
    expect(find.text('State Kalolsavam Manual'), findsOneWidget);
  });

  testWidgets('CircularsScreen unifies orders & circulars with Year, Month, and Label filters', (tester) async {
    tester.view.physicalSize = const Size(480, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          circularCategoriesProvider.overrideWith((ref) async => [
                {'id': 1, 'name': 'Census'},
                {'id': 2, 'name': 'Transfer'},
              ]),
          circularListFamilyProvider(const CircularQueryParam()).overrideWith(
            (ref) async => const CircularListResult(
              items: [
                {
                  'id': 1,
                  'title': 'Unified Order Circular',
                  'category_name': 'Census',
                  'date': '2026-06-30',
                }
              ],
              years: [2026, 2025],
            ),
          ),
        ],
        child: const MaterialApp(home: CircularsScreen()),
      ),
    );

    await tester.pumpAndSettle();

    expect(find.text('Orders & Circulars'), findsOneWidget);
    expect(find.text('General Education'), findsNothing);
    expect(find.text('Year'), findsOneWidget);
    expect(find.text('Month'), findsOneWidget);
    expect(find.text('Label'), findsOneWidget);
    expect(find.text('Unified Order Circular'), findsOneWidget);
  });

  testWidgets('OrganizationScreen includes Representations and Notices & Posters tabs', (tester) async {
    tester.view.physicalSize = const Size(800, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          officeBearersProvider.overrideWith((ref) async => {'sections': <String, dynamic>{}}),
        ],
        child: const MaterialApp(home: OrganizationScreen()),
      ),
    );

    await tester.pumpAndSettle();

    expect(find.text('State Bearers'), findsOneWidget);
    expect(find.text('Districts'), findsOneWidget);
    expect(find.text('Former Leaders'), findsOneWidget);
    expect(find.text('Representations'), findsOneWidget);
    expect(find.text('Notices & Posters'), findsOneWidget);
  });

  testWidgets('AppTopBar title and ModernPillTabBar are centered, SearchField clears query, and FlagBackgroundWrapper wraps cleanly', (tester) async {
    tester.view.physicalSize = const Size(760, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    String latestQuery = '';
    final tabController = TabController(length: 3, vsync: const TestVSync());
    addTearDown(tabController.dispose);

    await tester.pumpWidget(
      MaterialApp(
        home: FlagBackgroundWrapper(
          child: Scaffold(
            backgroundColor: Colors.transparent,
            appBar: AppTopBar(
              title: 'KPSTA Organization',
              showBackButton: false,
              bottom: ModernPillTabBar(
                controller: tabController,
                tabs: const [
                  ModernPillTab(label: 'State Bearers', icon: Icons.groups_rounded),
                  ModernPillTab(label: 'Districts', icon: Icons.location_city_rounded),
                  ModernPillTab(label: 'Former Leaders', icon: Icons.history_edu_rounded),
                ],
              ),
            ),
            body: Column(
              children: [
                SearchField(
                  hintText: 'Search order number or subject...',
                  onChanged: (q) => latestQuery = q,
                ),
              ],
            ),
          ),
        ),
      ),
    );

    await tester.pumpAndSettle();

    // AppTopBar title should be horizontally centered around x = 380
    final titleCenterX = tester.getCenter(find.text('KPSTA Organization')).dx;
    expect((titleCenterX - 380.0).abs(), lessThan(2.0));

    // Pill group outer bounds (from first AnimatedContainer pill to last) should be centered around x = 380
    final firstPill = find.ancestor(
      of: find.text('State Bearers'),
      matching: find.byType(AnimatedContainer),
    );
    final lastPill = find.ancestor(
      of: find.text('Former Leaders'),
      matching: find.byType(AnimatedContainer),
    );
    final firstLeft = tester.getTopLeft(firstPill).dx;
    final lastRight = tester.getTopRight(lastPill).dx;
    final pillsGroupCenter = (firstLeft + lastRight) / 2;
    expect((pillsGroupCenter - 380.0).abs(), lessThan(2.0));

    // SearchField input & clear button behavior
    await tester.enterText(find.byType(TextField), 'Transfer');
    await tester.pumpAndSettle();
    expect(latestQuery, 'Transfer');
    expect(find.byIcon(Icons.close_rounded), findsOneWidget);

    await tester.tap(find.byIcon(Icons.close_rounded));
    await tester.pumpAndSettle();
    expect(latestQuery, '');
  });

  testWidgets('MoreMenuScreen and NewsTickerBar match updated specifications', (tester) async {
    tester.view.physicalSize = const Size(800, 3000);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    // 1. Verify NewsTickerBar renders without errors with speed property
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: NewsTickerBar(
            items: ['Headline 1', 'Headline 2'],
            speed: 80.0,
          ),
        ),
      ),
    );
    await tester.pump();
    expect(find.text('KPSTA NEWS'), findsOneWidget);

    // 2. Verify MoreMenuScreen items
    await tester.pumpWidget(
      const ProviderScope(
        child: MaterialApp(
          home: MoreMenuScreen(),
        ),
      ),
    );
    await tester.pumpAndSettle();

    expect(find.text('OFFICIAL PORTALS'), findsOneWidget);
    expect(find.text('Quick Department Links'), findsOneWidget);
    expect(find.text('Official Exam Results'), findsNothing);
    expect(find.text('KPSTA Teachers Welfare & Donation'), findsNothing);
    expect(find.text('Contact Us'), findsOneWidget);
    expect(find.text('Admin Login'), findsAtLeastNWidgets(1));
  });
}

