import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:kpsta_app/features/public/home/presentation/home_screen.dart';

void main() {
  testWidgets('HomeScreen hit test', (WidgetTester tester) async {
    tester.view.physicalSize = const Size(800, 1200);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final mockData = {
      'sliders': [
        {'id': 1, 'image_url': '', 'title': 'Test Slider'}
      ],
      'flash_news': [
        {'id': 1, 'title': 'Test Flash News'}
      ],
      'latest_news': [
        {'id': 1, 'title': 'News 1', 'date': '2026-10-02'}
      ],
      'circulars': [
        {'id': 1, 'title': 'Circular 1', 'category_name': 'General'}
      ],
      'office_bearers': [
        {'id': 1, 'name': 'Leader 1', 'designation': 'President'}
      ],
      'reaction_gallery': [],
      'gallery_highlights': [],
    };

    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          homeDataProvider.overrideWith((ref) async => mockData),
        ],
        child: const MaterialApp(
          home: HomeScreen(),
        ),
      ),
    );

    await tester.pump();
    await tester.pump(const Duration(milliseconds: 100));

    // Simulate pointer hover / hit test
    final gesture = await tester.createGesture(kind: PointerDeviceKind.mouse);
    await gesture.addPointer(location: Offset.zero);
    for (double y = 10; y < 800; y += 20) {
      await gesture.moveTo(Offset(200, y));
    }
  });
}
