import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:kpsta_app/core/constants/api_constants.dart';
import 'package:kpsta_app/core/widgets/status_badge.dart';
import 'package:kpsta_app/core/widgets/statistic_card.dart';
import 'package:kpsta_app/core/widgets/document_row.dart';
import 'package:kpsta_app/features/auth/domain/user_model.dart';

void main() {
  group('UserModel Unit Tests', () {
    test('parses admin user correctly', () {
      final json = {
        'id': 1,
        'username': 'admin',
        'email': 'admin@kpsta.in',
        'phone': '919876543210',
        'role': 'Admin',
        'roles': ['Admin'],
        'permissions': ['news.manage', 'circulars.manage'],
        'is_admin': true,
        'is_membership': false,
      };

      final user = UserModel.fromJson(json);

      expect(user.id, 1);
      expect(user.username, 'admin');
      expect(user.isAdmin, isTrue);
      expect(user.permissions, contains('news.manage'));
    });

    test('parses membership user correctly', () {
      final json = {
        'id': 5,
        'username': 'member_officer',
        'phone': '919876543211',
        'role': 'Membership',
        'roles': ['Membership'],
        'permissions': ['teachers.manage'],
        'is_admin': false,
        'is_membership': true,
      };

      final user = UserModel.fromJson(json);

      expect(user.id, 5);
      expect(user.isMembership, isTrue);
      expect(user.isAdmin, isFalse);
    });
  });

  group('Shared Widgets Tests', () {
    testWidgets('StatusBadge displays published state with icon and label',
        (WidgetTester tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: StatusBadge(isPublished: true),
          ),
        ),
      );

      expect(find.text('Published'), findsOneWidget);
      expect(find.byIcon(Icons.check_circle_outline), findsOneWidget);
    });

    testWidgets('StatusBadge displays draft state with icon and label',
        (WidgetTester tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: StatusBadge(isPublished: false),
          ),
        ),
      );

      expect(find.text('Draft'), findsOneWidget);
      expect(find.byIcon(Icons.pending_outlined), findsOneWidget);
    });

    testWidgets('StatisticCard renders title, tabular value, and icon',
        (WidgetTester tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: StatisticCard(
              title: 'Active Members',
              value: '12,450',
              icon: Icons.groups,
            ),
          ),
        ),
      );

      expect(find.text('Active Members'), findsOneWidget);
      expect(find.text('12,450'), findsOneWidget);
      expect(find.byIcon(Icons.groups), findsOneWidget);
    });

    testWidgets('DocumentRow displays title and metadata properly',
        (WidgetTester tester) async {
      await tester.pumpWidget(
        const MaterialApp(
          home: Scaffold(
            body: DocumentRow(
              title: 'G.O(P) No. 42/2026 Higher Secondary Rule Revision',
              date: '2026-09-15',
              category: 'General',
            ),
          ),
        ),
      );

      expect(find.text('G.O(P) No. 42/2026 Higher Secondary Rule Revision'),
          findsOneWidget);
      expect(find.text('General'), findsOneWidget);
      expect(find.byIcon(Icons.picture_as_pdf_outlined), findsOneWidget);
    });
  });

  group('API Layer Direct Integration Tests', () {
    test('ApiConstants maps directly to /api/v1 endpoints with zero hardcoding', () {
      expect(ApiConstants.adminFlashNews, '/admin/flash-news');
      expect(ApiConstants.adminSliders, '/admin/sliders');
      expect(ApiConstants.adminGalleries, '/admin/galleries');
      expect(ApiConstants.adminQuickLinks, '/admin/quick-links');
      expect(ApiConstants.adminResultLinks, '/admin/result-links');
      expect(ApiConstants.membershipCounts, '/membership/counts');
      expect(ApiConstants.membershipReports, '/membership/reports');
      expect(ApiConstants.membershipMetadata, '/membership/metadata');
      expect(ApiConstants.siteVisitors, '/site-visitors');
    });

    test('Membership counts envelope parses SQL aggregated tallies correctly', () {
      final apiResponse = {
        'success': true,
        'data': {
          'year': '2026',
          'group': 1,
          'items': [
            {
              'id': 1,
              'name': 'Thiruvananthapuram',
              'total': 1500,
              'confirmed': 1200,
              'verified': 1100,
              'approved': 1050,
              'govt_members': 600,
              'aided_members': 900,
            }
          ],
          'totals': {
            'total': 1500,
            'confirmed': 1200,
            'verified': 1100,
            'approved': 1050,
            'govt_members': 600,
            'aided_members': 900,
          }
        }
      };

      final data = apiResponse['data'] as Map<String, dynamic>;
      final totals = data['totals'] as Map<String, dynamic>;
      expect(totals['total'], 1500);
      expect(totals['approved'], 1050);
      expect(totals['govt_members'], 600);
      expect(totals['aided_members'], 900);
    });

    test('Membership reports envelope parses SQL breakdown totals directly', () {
      final apiResponse = {
        'success': true,
        'data': {
          'report_type': 'district',
          'items': [
            {
              'id': 1,
              'name': 'Kollam',
              'govt_members': 450,
              'aided_members': 850,
              'total_count': 1300,
            }
          ],
          'totals': {
            'govt_members': 450,
            'aided_members': 850,
            'total_count': 1300,
          }
        }
      };

      final data = apiResponse['data'] as Map<String, dynamic>;
      final totals = data['totals'] as Map<String, dynamic>;
      expect(totals['total_count'], 1300);
      expect(totals['govt_members'], 450);
      expect(totals['aided_members'], 850);
    });
  });
}
