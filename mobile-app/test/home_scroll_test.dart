import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:kpsta_app/features/public/home/presentation/home_screen.dart';

void main() {
  testWidgets('HomeScreen scroll to bottom test', (WidgetTester tester) async {
    tester.view.physicalSize = const Size(800, 1200);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.resetPhysicalSize);

    final mockData = {
      "sliders": [
        {
          "id": 40,
          "title": "KPSTA DGE Office March - 2025 June 21",
          "description": "KPSTA DGE Office March - 2025 June 21",
          "image_url": "http://127.0.0.1:8090/uploads/slider/16a0dfaaf62f28d69e5903a284152357.jpg",
        },
        {
          "id": 38,
          "title": "KPSTA Niyamasabha March",
          "description": "KPSTA Niyamasabha March",
          "image_url": "http://127.0.0.1:8090/uploads/slider/8616999ad0ef3e2fb7837118d96563fe.jpg",
        }
      ],
      "flash_news": [
        {"id": 1, "title": "Flash 1"},
        {"id": 2, "title": "Flash 2"}
      ],
      "latest_news": [
        {
          "id": 81,
          "title": "അധ്യാപക-അനധ്യാപക ജീവനക്കാർക്കുള്ള കേരള ടീച്ചർ എലിജിബിലിറ്റി ടെസ്റ്റ് - ഫെബ്രുവരി 2026",
          "description": "",
          "image_url": null,
          "published_at": null
        },
        {
          "id": 80,
          "title": "CM Kids Scholarship LP & UP (LSS, USS) ഹാൾടിക്കറ്റ് പ്രസിദ്ധീകരിച്ചു.",
          "description": "",
          "image_url": null,
          "published_at": "2026-02-17T22:24:36+05:30"
        }
      ],
      "circulars": [
        {
          "id": 4019,
          "title": "Test Order 123",
          "type": "General",
          "category": ".",
          "circular_date": "2027-01-01T00:00:00+05:30",
          "file_url": "http://127.0.0.1:8090/uploads/order_circular/http://example.com"
        },
        {
          "id": 4018,
          "title": "Census 2027 - Facilities and concessions to the staff engaged for the census work - Orders issued General Administration department",
          "type": "General",
          "category": "Census",
          "circular_date": "2026-07-01T00:00:00+05:30",
          "file_url": "http://127.0.0.1:8090/uploads/order_circular/GO_Ms_69_2026_GAD.pdf"
        },
        {
          "id": 4016,
          "title": "SSLC സർട്ടിഫിക്കറ്റിലെ പേര് തിരുത്തി നൽകുന്നതിനുള്ള നിലവിലെ ഫീസ്  വർദ്ധിപ്പിച്ചു.. - Order 24.06.2026",
          "type": "General",
          "category": "Correction",
          "circular_date": "2026-06-24T00:00:00+05:30",
          "file_url": "http://127.0.0.1:8090/uploads/order_circular/sslc.pdf"
        }
      ],
      "office_bearers": [
        {
          "id": 58,
          "name": "PK Aravindan",
          "designation": "PRESIDENT",
          "phone": "9495409460",
          "photo_url": "http://127.0.0.1:8090/uploads/office_bearer/b92f63395992daac23c1c4a1fcef9471.jpg"
        },
        {
          "id": 55,
          "name": "Abdul Majeed K",
          "designation": "GENERAL SECRETARY",
          "phone": "994656575",
          "photo_url": "http://127.0.0.1:8090/uploads/office_bearer/ead388e6263d4602fb53a634cdc58981.jpg"
        }
      ],
      "reaction_gallery": [
        {
          "id": 52,
          "title": ".",
          "image_url": "http://127.0.0.1:8090/uploads/reaction_gallery/6ebb86882358f2c41cdb2bd2810f7721.jpg"
        }
      ],
      "gallery_highlights": [
        {
          "id": 5,
          "caption": "Conference",
          "image_url": "http://127.0.0.1:8090/uploads/gallery/ed0d71c131327fd4449179c18aa182cd.jpeg"
        }
      ]
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

    // Scroll down to circulars
    final scrollFinder = find.byType(SingleChildScrollView).first;
    await tester.drag(scrollFinder, const Offset(0, -600));
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 200));

    // Advance timer by 5 seconds (hero slider ticks)
    await tester.pump(const Duration(seconds: 5));
    await tester.pump(const Duration(milliseconds: 600));

    // Scroll further down
    await tester.drag(scrollFinder, const Offset(0, -600));
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 200));

    // Hit test mouse movements
    final gesture = await tester.createGesture(kind: PointerDeviceKind.mouse);
    await gesture.addPointer(location: Offset.zero);
    for (double y = 10; y < 800; y += 20) {
      await gesture.moveTo(Offset(200, y));
    }
  });
}
