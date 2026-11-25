import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:kpsta_app/core/widgets/hero_carousel_slider.dart';
import 'package:kpsta_app/features/public/circulars/presentation/circulars_screen.dart';
import 'package:kpsta_app/features/admin/dashboard/presentation/admin_dashboard_screen.dart';
import 'package:kpsta_app/features/admin/organization/presentation/admin_office_bearers_screen.dart';

void main() {
  testWidgets('HeroCarouselSlider shows dark tricolour scrim & text on Slide 1, only flag on Slide 2+', (tester) async {
    tester.view.physicalSize = const Size(480, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: HeroCarouselSlider(
            sliders: [
              {'id': 1, 'image_url': ''},
              {'id': 2, 'image_url': ''},
            ],
          ),
        ),
      ),
    );

    await tester.pump();

    // Slide 0 shows KPSTA title, slogans, and flag
    expect(find.text('KPSTA'), findsOneWidget);
    expect(find.text('UNITE FOR QUALITY EDUCATION'), findsOneWidget);
    expect(find.byType(Image), findsOneWidget);

    // Swipe left to Slide 1
    await tester.drag(find.byType(PageView), const Offset(-400, 0));
    await tester.pumpAndSettle();

    // Slide 1 hides KPSTA title and slogans, keeping only the flag
    expect(find.text('KPSTA'), findsNothing);
    expect(find.text('UNITE FOR QUALITY EDUCATION'), findsNothing);
    expect(find.byType(Image), findsOneWidget);
  });

  testWidgets('CircularsScreen Year and Month pickers show all options and scroll down & up smoothly', (tester) async {
    tester.view.physicalSize = const Size(480, 900);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final allYears = [
      2026, 2025, 2024, 2023, 2022, 2021, 2020, 2019, 2018, 2017,
      2016, 2015, 2014, 2013, 2012, 2011, 2007, 2002, 1995, 1991, 1988,
    ];

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          circularCategoriesProvider.overrideWith((ref) async => [
                {'id': 1, 'name': 'Census'},
              ]),
          circularListFamilyProvider.overrideWith((ref, param) async {
            return CircularListResult(
              items: [
                {
                  'id': 1,
                  'title': 'Sample Order',
                  'category_name': 'Census',
                  'date': '2026-06-30',
                }
              ],
              years: allYears,
            );
          }),
        ],
        child: const MaterialApp(home: CircularsScreen()),
      ),
    );

    await tester.pumpAndSettle();

    // Open Year picker and scroll down to 1988, then back up to 2025
    await tester.tap(find.text('Year'));
    await tester.pumpAndSettle();

    expect(find.text('Select Year'), findsOneWidget);
    expect(find.text('All Years'), findsOneWidget);
    final yearScrollable = find.byType(Scrollable).last;
    await tester.scrollUntilVisible(find.text('1988'), 200, scrollable: yearScrollable);
    expect(find.text('1988'), findsOneWidget);

    await tester.scrollUntilVisible(find.text('2025'), -200, scrollable: yearScrollable);
    await tester.tap(find.text('2025'));
    await tester.pumpAndSettle();
    expect(find.text('2025'), findsOneWidget);

    // Open Month picker and scroll down to December, then back up to March
    await tester.tap(find.text('Month'));
    await tester.pumpAndSettle();

    expect(find.text('Select Month'), findsOneWidget);
    expect(find.text('All Months'), findsOneWidget);
    final monthScrollable = find.byType(Scrollable).last;
    await tester.scrollUntilVisible(find.text('December'), 200, scrollable: monthScrollable);
    expect(find.text('December'), findsOneWidget);

    await tester.scrollUntilVisible(find.text('March'), -200, scrollable: monthScrollable);
    await tester.tap(find.text('March'));
    await tester.pumpAndSettle();
    expect(find.text('Mar'), findsOneWidget);
  });

  testWidgets('AdminDashboardScreen displays 3 separate leader menus', (tester) async {
    tester.view.physicalSize = const Size(480, 1400);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          adminDashboardProvider.overrideWith((ref) async => {
                'news_count': 5,
                'circulars_count': 10,
                'downloads_count': 4,
                'state_leaders_count': 57,
                'former_state_leaders_count': 8,
                'district_leaders_count': 42,
                'albums_count': 3,
                'flash_news_count': 2,
                'sliders_count': 4,
              }),
        ],
        child: const MaterialApp(home: AdminDashboardScreen()),
      ),
    );

    await tester.pumpAndSettle();

    expect(find.text('State Leaders'), findsWidgets);
    expect(find.text('Former State Leaders'), findsWidgets);
    expect(find.text('District Leaders'), findsWidgets);
    expect(find.text('Manage Office Bearers'), findsNothing);
  });

  testWidgets('AdminOfficeBearersScreen isolates roles per section', (tester) async {
    tester.view.physicalSize = const Size(1800, 1000);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final mockMeta = {
      'sections': {
        'state': {
          'roles': LeaderSectionType.state.fallbackRoles,
        },
        'former': {
          'roles': LeaderSectionType.former.fallbackRoles,
        },
        'district': {
          'roles': LeaderSectionType.district.fallbackRoles,
        },
      },
      'districts': [
        {'id': 1, 'name': 'Thiruvananthapuram'},
        {'id': 2, 'name': 'Kollam'},
      ],
    };

    // 1. State Leaders section
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          adminBearerMetadataProvider.overrideWith((ref) async => mockMeta),
          adminBearersProvider.overrideWith((ref, query) async => []),
        ],
        child: const MaterialApp(
          home: AdminOfficeBearersScreen(section: LeaderSectionType.state),
        ),
      ),
    );
    await tester.pumpAndSettle();
    expect(find.text('State Leaders'), findsOneWidget);
    expect(find.text('Add State Leader'), findsOneWidget);
    expect(find.text('President'), findsOneWidget);
    expect(find.text('General Secretary'), findsOneWidget);
    expect(find.text('Secretariate Members'), findsOneWidget);
    expect(find.text('Filter by District'), findsNothing);

    // 2. Former State Leaders section
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          adminBearerMetadataProvider.overrideWith((ref) async => mockMeta),
          adminBearersProvider.overrideWith((ref, query) async => []),
        ],
        child: const MaterialApp(
          home: AdminOfficeBearersScreen(section: LeaderSectionType.former),
        ),
      ),
    );
    await tester.pumpAndSettle();
    expect(find.text('Former State Leaders'), findsOneWidget);
    expect(find.text('Add Former Leader'), findsOneWidget);
    expect(find.text('General Secretary'), findsOneWidget);
    expect(find.text('Secretariate Members'), findsNothing);

    // 3. District Leaders section
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          adminBearerMetadataProvider.overrideWith((ref) async => mockMeta),
          adminBearersProvider.overrideWith((ref, query) async => []),
        ],
        child: const MaterialApp(
          home: AdminOfficeBearersScreen(section: LeaderSectionType.district),
        ),
      ),
    );
    await tester.pumpAndSettle();
    expect(find.text('District Leaders'), findsOneWidget);
    expect(find.text('Add District Leader'), findsOneWidget);
    expect(find.text('Secretary'), findsOneWidget);
    expect(find.text('Filter by District'), findsOneWidget);
  });
}
