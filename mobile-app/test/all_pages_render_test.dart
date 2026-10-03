import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:kpsta_app/features/public/home/presentation/home_screen.dart';
import 'package:kpsta_app/features/public/home/presentation/more_menu_screen.dart';
import 'package:kpsta_app/features/public/news/presentation/news_screen.dart';
import 'package:kpsta_app/features/public/news/presentation/news_detail_screen.dart';
import 'package:kpsta_app/features/public/circulars/presentation/circulars_screen.dart';
import 'package:kpsta_app/features/public/circulars/presentation/circular_detail_screen.dart';
import 'package:kpsta_app/features/public/downloads/presentation/downloads_screen.dart';
import 'package:kpsta_app/features/public/gallery/presentation/gallery_screen.dart';
import 'package:kpsta_app/features/public/gallery/presentation/gallery_detail_screen.dart';
import 'package:kpsta_app/features/public/organization/presentation/organization_screen.dart';
import 'package:kpsta_app/features/public/organization/presentation/districts_screen.dart';
import 'package:kpsta_app/features/public/organization/presentation/former_leaders_screen.dart';
import 'package:kpsta_app/features/public/services/presentation/services_screen.dart';
import 'package:kpsta_app/features/public/services/presentation/service_detail_screen.dart';
import 'package:kpsta_app/features/public/services/presentation/adayapaka_screen.dart';
import 'package:kpsta_app/features/public/links/presentation/quick_links_screen.dart';
import 'package:kpsta_app/features/public/links/presentation/results_screen.dart';
import 'package:kpsta_app/features/public/contact/presentation/contact_screen.dart';
import 'package:kpsta_app/features/public/contact/presentation/privacy_screen.dart';
import 'package:kpsta_app/features/public/donation/presentation/donation_screen.dart';
import 'package:kpsta_app/features/auth/presentation/login_screen.dart';
import 'package:kpsta_app/features/admin/dashboard/presentation/admin_dashboard_screen.dart';
import 'package:kpsta_app/features/membership/dashboard/presentation/membership_dashboard_screen.dart';

void main() {
  setUp(() {
    TestWidgetsFlutterBinding.ensureInitialized();
  });

  group('All Mobile Pages Render & No Broken Reference Tests', () {
    testWidgets('HomeScreen renders with zero overflow and active tickers', (tester) async {
      tester.view.physicalSize = const Size(800, 1600);
      tester.view.devicePixelRatio = 1.0;
      addTearDown(tester.view.resetPhysicalSize);

      final mockHome = {
        'sliders': [
          {'id': 1, 'image_url': '', 'title': 'Slider 1', 'description': 'KPSTA March'}
        ],
        'flash_news': [
          {'id': 1, 'title': 'KPSTA Oath Ceremony 2026', 'link': ''}
        ],
        'latest_news': [
          {'id': 1, 'title': 'K-TET February 2026', 'published_at': '2026-02-17'}
        ],
        'circulars': [
          {'id': 1, 'title': 'Census Duty 2026', 'category': 'Census', 'circular_date': '2026-06-30'}
        ],
        'office_bearers': [
          {'id': 1, 'name': 'PK Aravindan', 'designation': 'PRESIDENT', 'phone': '9495409460'}
        ],
        'reaction_gallery': [],
        'gallery_highlights': [],
        'services': [
          {'id': 2, 'title': 'Leave Rules & Eligibility', 'description': 'KSR leave rules', 'icon': 'event_available'}
        ],
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
      await tester.pump(const Duration(milliseconds: 100));

      expect(find.text('KERALA PRADESH'), findsOneWidget);
      expect(find.text('Welcome To KPSTA'), findsOneWidget);
      expect(find.text('KPSTA NEWS'), findsOneWidget);
      expect(find.text('Office Bearers'), findsOneWidget);
      expect(find.text('Membership & Magazine'), findsOneWidget);
      expect(find.text('Recent Orders & Circulars'), findsOneWidget);

      await tester.drag(find.byType(SingleChildScrollView).first, const Offset(0, -600));
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('News Window'), findsOneWidget);
    });

    testWidgets('NewsScreen renders list of news items', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            newsListFamilyProvider('').overrideWith((ref) async => [
              {
                'id': 81,
                'title': 'K-TET February 2026 Exam Announcement',
                'description': 'Details regarding teacher eligibility test.',
                'published_at': '2026-02-17',
              }
            ]),
          ],
          child: const MaterialApp(home: NewsScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('News & Announcements'), findsOneWidget);
      expect(find.text('K-TET February 2026 Exam Announcement'), findsOneWidget);
    });

    testWidgets('NewsDetailScreen renders single article', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            newsDetailProvider('81').overrideWith((ref) async => {
              'id': 81,
              'title': 'K-TET February 2026 Exam Announcement',
              'description': 'Full text of the announcement.',
              'published_at': '2026-02-17',
            }),
          ],
          child: const MaterialApp(home: NewsDetailScreen(id: '81')),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('K-TET February 2026 Exam Announcement'), findsOneWidget);
    });

    testWidgets('CircularsScreen renders orders and circulars list', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            circularListFamilyProvider(const CircularQueryParam(type: 'all', search: '')).overrideWith((ref) async => [
              {
                'id': 4018,
                'title': 'Census 2027 Facilities and Concessions',
                'category_name': 'Census',
                'date': '2026-07-01',
              }
            ]),
          ],
          child: const MaterialApp(home: CircularsScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Orders & Circulars'), findsOneWidget);
    });

    testWidgets('CircularDetailScreen renders single circular details', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            circularDetailProvider('4018').overrideWith((ref) async => {
              'id': 4018,
              'title': 'Census 2027 Facilities and Concessions',
              'category_name': 'Census',
              'date': '2026-07-01',
            }),
          ],
          child: const MaterialApp(home: CircularDetailScreen(id: '4018')),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Census 2027 Facilities and Concessions'), findsOneWidget);
    });

    testWidgets('DownloadsScreen renders downloads categories and items', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            downloadListFamilyProvider(const DownloadQueryParam(type: 'all', search: '')).overrideWith((ref) async => [
              {
                'id': 1,
                'title': 'Leave Application Form',
                'category': 'Forms',
                'file_url': 'http://example.com/form.pdf',
              }
            ]),
          ],
          child: const MaterialApp(home: DownloadsScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Downloads & Resources'), findsOneWidget);
    });

    testWidgets('OrganizationScreen renders state office bearers', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            officeBearersProvider.overrideWith((ref) async => {
              'sections': {
                'State Leaders': [
                  {
                    'id': 58,
                    'name': 'PK Aravindan',
                    'designation': 'PRESIDENT',
                    'phone': '9495409460',
                  }
                ]
              }
            }),
          ],
          child: const MaterialApp(home: OrganizationScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('PK Aravindan'), findsOneWidget);
    });

    testWidgets('DistrictsScreen renders district committees', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            districtsProvider.overrideWith((ref) async => [
              {'id': 1, 'name': 'Thiruvananthapuram', 'code': 'TVM'}
            ]),
          ],
          child: const MaterialApp(home: DistrictsScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Districts of Kerala'), findsOneWidget);
    });

    testWidgets('FormerLeadersScreen renders former leaders directory', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            formerLeadersProvider.overrideWith((ref) async => [
              {'id': 1, 'name': 'Comrade Leader', 'designation': 'Former President'}
            ]),
          ],
          child: const MaterialApp(home: FormerLeadersScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Former Association Leaders'), findsOneWidget);
      expect(find.text('Comrade Leader'), findsOneWidget);
    });

    testWidgets('GalleryScreen renders albums', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            galleryAlbumsProvider.overrideWith((ref) async => [
              {
                'id': 12,
                'album_name': 'State Conference 2026',
                'photo_count': 15,
                'cover_image': '',
              }
            ]),
          ],
          child: const MaterialApp(home: GalleryScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Photo Gallery'), findsOneWidget);
    });

    testWidgets('GalleryDetailScreen renders album photo grid', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            albumDetailProvider('12').overrideWith((ref) async => {
              'album_name': 'State Conference 2026',
              'photos': [
                {'id': 101, 'image_url': '', 'caption': 'Inauguration'}
              ]
            }),
          ],
          child: const MaterialApp(home: GalleryDetailScreen(id: '12')),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Album Photos'), findsOneWidget);
    });

    testWidgets('ServicesScreen renders service items', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            servicesListProvider.overrideWith((ref) async => [
              {
                'id': 2,
                'title': 'Leave Rules & Eligibility',
                'description': 'Comprehensive KSR leave rules',
                'icon': 'event_available',
              }
            ]),
          ],
          child: const MaterialApp(home: ServicesScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Service Corner'), findsOneWidget);
    });

    testWidgets('ServiceDetailScreen renders single service document', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            serviceDetailProvider('2').overrideWith((ref) async => {
              'id': 2,
              'title': 'Leave Rules & Eligibility',
              'content': 'Earned Leave, Half Pay Leave details.',
            }),
          ],
          child: const MaterialApp(home: ServiceDetailScreen(id: '2')),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Service Guide'), findsOneWidget);
    });

    testWidgets('AdayapakaScreen renders magazine subscriptions and issues', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(home: AdayapakaScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Adayapaka Sabham'), findsOneWidget);
    });

    testWidgets('QuickLinksScreen renders official portal links', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            quickLinksProvider.overrideWith((ref) async => [
              {'id': 94, 'title': 'Victers Channel APP', 'url': 'https://kite.kerala.gov.in'}
            ]),
          ],
          child: const MaterialApp(home: QuickLinksScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Government & Educational Links'), findsOneWidget);
    });

    testWidgets('ResultsScreen renders exam results', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            examResultsProvider.overrideWith((ref) async => [
              {'id': 1, 'title': 'SSLC Examination Results', 'url': 'http://keralaresults.nic.in'}
            ]),
          ],
          child: const MaterialApp(home: ResultsScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Exam Results Portals'), findsOneWidget);
    });

    testWidgets('ContactScreen renders contact details and address', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            contactInfoProvider.overrideWith((ref) async => {
              'phone': '0471-2575797',
              'email': 'kpsta.in@gmail.com',
              'address': 'KPSTA Bhavan, Thiruvananthapuram',
            }),
          ],
          child: const MaterialApp(home: ContactScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Contact KPSTA'), findsOneWidget);
    });

    testWidgets('PrivacyScreen renders privacy policy', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(home: PrivacyScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Privacy & Terms'), findsOneWidget);
    });

    testWidgets('DonationScreen renders contributions and bank information', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(home: DonationScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Support KPSTA / Donate'), findsOneWidget);
    });

    testWidgets('MoreMenuScreen renders all menu categories', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(home: MoreMenuScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('More & Information'), findsOneWidget);
      expect(find.text('Staff & Member Portal'), findsOneWidget);
    });

    testWidgets('LoginScreen renders authentication inputs', (tester) async {
      await tester.pumpWidget(
        const ProviderScope(
          child: MaterialApp(home: LoginScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('KPSTA Portal Authentication'), findsOneWidget);
    });

    testWidgets('AdminDashboardScreen renders dashboard widgets', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            adminDashboardProvider.overrideWith((ref) async => {
              'news_count': 12,
              'circulars_count': 45,
              'visitors_count': 15200,
            }),
          ],
          child: const MaterialApp(home: AdminDashboardScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Administrator Dashboard'), findsOneWidget);
    });

    testWidgets('MembershipDashboardScreen renders membership portal', (tester) async {
      await tester.pumpWidget(
        ProviderScope(
          overrides: [
            membershipDashboardProvider.overrideWith((ref) async => {
              'total_members': 14200,
              'approved_count': 13800,
              'pending_count': 400,
            }),
          ],
          child: const MaterialApp(home: MembershipDashboardScreen()),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 100));
      expect(find.text('Membership Portal'), findsOneWidget);
    });
  });
}
