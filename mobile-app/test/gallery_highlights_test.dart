import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:go_router/go_router.dart';
import 'package:kpsta_app/features/public/home/presentation/home_screen.dart';

void main() {
  testWidgets('HomeScreen gallery highlights are rendered and clickable', (WidgetTester tester) async {
    tester.view.physicalSize = const Size(800, 1600);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final mockData = {
      'sliders': [],
      'flash_news': [],
      'latest_news': [],
      'circulars': [],
      'office_bearers': [],
      'reaction_gallery': [],
      'gallery_highlights': [
        {
          'id': 101,
          'album_id': '42',
          'album_guid': 'guid-42',
          'album_name': 'Annual Conference 2026',
          'thumb_url': 'http://localhost/uploads/gallery/thumb/img1.jpg',
          'image_url': 'http://localhost/uploads/gallery/original/img1.jpg',
          'caption': 'Inaugural Session',
        },
        {
          'id': 102,
          'album_id': '43',
          'album_guid': 'guid-43',
          'album_name': 'State Meet',
          'thumb_url': 'http://localhost/uploads/gallery/thumb/img2.jpg',
          'image_url': 'http://localhost/uploads/gallery/original/img2.jpg',
          'caption': 'Cultural Event',
        },
      ],
    };

    final router = GoRouter(
      initialLocation: '/',
      routes: [
        GoRoute(
          path: '/',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/gallery',
          builder: (context, state) => const Scaffold(body: Text('Gallery List Screen')),
        ),
        GoRoute(
          path: '/gallery/:id',
          builder: (context, state) => Scaffold(body: Text('Gallery Detail ${state.pathParameters['id']}')),
        ),
      ],
    );

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          homeDataProvider.overrideWith((ref) async => mockData),
        ],
        child: MaterialApp.router(
          routerConfig: router,
        ),
      ),
    );

    await tester.pump();
    await tester.pump(const Duration(milliseconds: 100));

    // Check gallery title and View All button exist
    expect(find.text('Gallery'), findsWidgets);
    expect(find.text('View All'), findsWidgets);
    expect(find.text('Inaugural Session'), findsOneWidget);
    expect(find.text('Cultural Event'), findsOneWidget);

    // Tap first gallery card item -> navigates to /gallery/42
    await tester.tap(find.text('Inaugural Session'));
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 300));

    expect(find.text('Gallery Detail 42'), findsOneWidget);

    // Pop back to home
    router.pop();
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 300));

    // Tap View All in the gallery section -> navigates to /gallery
    final viewAllButtons = find.widgetWithText(TextButton, 'View All');
    await tester.tap(viewAllButtons.first);
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 300));

    expect(find.text('Gallery List Screen'), findsOneWidget);
  });
}

