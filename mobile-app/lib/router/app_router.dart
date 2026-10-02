import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../core/widgets/app_shell_public.dart';
import '../features/auth/presentation/auth_providers.dart';
import '../features/auth/presentation/login_screen.dart';

// Public Screens
import '../features/public/home/presentation/home_screen.dart';
import '../features/public/home/presentation/more_menu_screen.dart';
import '../features/public/news/presentation/news_screen.dart';
import '../features/public/news/presentation/news_detail_screen.dart';
import '../features/public/circulars/presentation/circulars_screen.dart';
import '../features/public/circulars/presentation/circular_detail_screen.dart';
import '../features/public/downloads/presentation/downloads_screen.dart';
import '../features/public/gallery/presentation/gallery_screen.dart';
import '../features/public/gallery/presentation/gallery_detail_screen.dart';
import '../features/public/organization/presentation/organization_screen.dart';
import '../features/public/organization/presentation/districts_screen.dart';
import '../features/public/organization/presentation/former_leaders_screen.dart';
import '../features/public/services/presentation/services_screen.dart';
import '../features/public/services/presentation/service_detail_screen.dart';
import '../features/public/services/presentation/adayapaka_screen.dart';
import '../features/public/links/presentation/quick_links_screen.dart';
import '../features/public/links/presentation/results_screen.dart';
import '../features/public/contact/presentation/contact_screen.dart';
import '../features/public/contact/presentation/privacy_screen.dart';
import '../features/public/donation/presentation/donation_screen.dart';

// Admin Screens
import '../features/admin/dashboard/presentation/admin_dashboard_screen.dart';
import '../features/admin/news/presentation/admin_news_screen.dart';
import '../features/admin/news/presentation/admin_news_form_screen.dart';
import '../features/admin/circulars/presentation/admin_circulars_screen.dart';
import '../features/admin/circulars/presentation/admin_circular_form_screen.dart';
import '../features/admin/downloads/presentation/admin_downloads_screen.dart';
import '../features/admin/organization/presentation/admin_office_bearers_screen.dart';
import '../features/admin/flash_news/presentation/admin_flash_news_screen.dart';
import '../features/admin/slider/presentation/admin_slider_screen.dart';
import '../features/admin/gallery/presentation/admin_gallery_screen.dart';
import '../features/admin/links/presentation/admin_quick_links_screen.dart';
import '../features/admin/links/presentation/admin_result_links_screen.dart';

// Membership Screens
import '../features/membership/dashboard/presentation/membership_dashboard_screen.dart';
import '../features/membership/counts/presentation/membership_counts_screen.dart';
import '../features/membership/reports/presentation/membership_reports_screen.dart';
import '../features/membership/teachers/presentation/membership_teachers_screen.dart';
import '../features/membership/teachers/presentation/membership_teacher_detail_screen.dart';
import '../features/membership/teachers/presentation/membership_teacher_form_screen.dart';
import '../features/membership/whats_new/presentation/membership_whats_new_screen.dart';

final routerProvider = Provider<GoRouter>((ref) {
  final authNotifier = ref.watch(authNotifierProvider);

  return GoRouter(
    initialLocation: '/home',
    redirect: (context, state) {
      final loc = state.uri.toString();
      final isAuth = authNotifier.status == AuthStatus.authenticated;
      final isAdmin = authNotifier.user?.isAdmin == true;
      final isMembership = authNotifier.user?.isMembership == true || isAdmin;

      if (loc.startsWith('/admin') && (!isAuth || !isAdmin)) {
        return '/login';
      }

      if (loc.startsWith('/membership') && (!isAuth || !isMembership)) {
        return '/login';
      }

      if (loc == '/login' && isAuth) {
        if (isAdmin) return '/admin/dashboard';
        return '/membership/dashboard';
      }

      return null;
    },
    routes: [
      // Public Shell Route (Bottom Navigation)
      ShellRoute(
        builder: (context, state, child) => AppShellPublic(child: child),
        routes: [
          GoRoute(
            path: '/home',
            builder: (context, state) => const HomeScreen(),
          ),
          GoRoute(
            path: '/news',
            builder: (context, state) => const NewsScreen(),
          ),
          GoRoute(
            path: '/circulars',
            builder: (context, state) => const CircularsScreen(),
          ),
          GoRoute(
            path: '/downloads',
            builder: (context, state) => const DownloadsScreen(),
          ),
          GoRoute(
            path: '/more',
            builder: (context, state) => const MoreMenuScreen(),
          ),
        ],
      ),

      // Public Detail / Dedicated Screens
      GoRoute(
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/news/:id',
        builder: (context, state) =>
            NewsDetailScreen(id: state.pathParameters['id']!),
      ),
      GoRoute(
        path: '/circulars/:id',
        builder: (context, state) =>
            CircularDetailScreen(id: state.pathParameters['id']!),
      ),
      GoRoute(
        path: '/gallery',
        builder: (context, state) => const GalleryScreen(),
      ),
      GoRoute(
        path: '/gallery/:id',
        builder: (context, state) =>
            GalleryDetailScreen(id: state.pathParameters['id']!),
      ),
      GoRoute(
        path: '/organization',
        builder: (context, state) => const OrganizationScreen(),
      ),
      GoRoute(
        path: '/organization/districts',
        builder: (context, state) => const DistrictsScreen(),
      ),
      GoRoute(
        path: '/organization/former-leaders',
        builder: (context, state) => const FormerLeadersScreen(),
      ),
      GoRoute(
        path: '/services',
        builder: (context, state) => const ServicesScreen(),
      ),
      GoRoute(
        path: '/services/adayapaka',
        builder: (context, state) => const AdayapakaScreen(),
      ),
      GoRoute(
        path: '/services/:id',
        builder: (context, state) =>
            ServiceDetailScreen(id: state.pathParameters['id']!),
      ),
      GoRoute(
        path: '/links/quick',
        builder: (context, state) => const QuickLinksScreen(),
      ),
      GoRoute(
        path: '/links/results',
        builder: (context, state) => const ResultsScreen(),
      ),
      GoRoute(
        path: '/contact',
        builder: (context, state) => const ContactScreen(),
      ),
      GoRoute(
        path: '/privacy',
        builder: (context, state) => const PrivacyScreen(),
      ),
      GoRoute(
        path: '/donation',
        builder: (context, state) => const DonationScreen(),
      ),

      // Admin Routes
      GoRoute(
        path: '/admin/dashboard',
        builder: (context, state) => const AdminDashboardScreen(),
      ),
      GoRoute(
        path: '/admin/news',
        builder: (context, state) => const AdminNewsScreen(),
      ),
      GoRoute(
        path: '/admin/news/new',
        builder: (context, state) => const AdminNewsFormScreen(),
      ),
      GoRoute(
        path: '/admin/news/edit/:id',
        builder: (context, state) =>
            AdminNewsFormScreen(editId: state.pathParameters['id']),
      ),
      GoRoute(
        path: '/admin/circulars',
        builder: (context, state) => const AdminCircularsScreen(),
      ),
      GoRoute(
        path: '/admin/circulars/new',
        builder: (context, state) => const AdminCircularFormScreen(),
      ),
      GoRoute(
        path: '/admin/circulars/edit/:id',
        builder: (context, state) =>
            AdminCircularFormScreen(editId: state.pathParameters['id']),
      ),
      GoRoute(
        path: '/admin/downloads',
        builder: (context, state) => const AdminDownloadsScreen(),
      ),
      GoRoute(
        path: '/admin/office-bearers',
        builder: (context, state) => const AdminOfficeBearersScreen(),
      ),
      GoRoute(
        path: '/admin/flash-news',
        builder: (context, state) => const AdminFlashNewsScreen(),
      ),
      GoRoute(
        path: '/admin/sliders',
        builder: (context, state) => const AdminSliderScreen(),
      ),
      GoRoute(
        path: '/admin/gallery',
        builder: (context, state) => const AdminGalleryScreen(),
      ),
      GoRoute(
        path: '/admin/quick-links',
        builder: (context, state) => const AdminQuickLinksScreen(),
      ),
      GoRoute(
        path: '/admin/result-links',
        builder: (context, state) => const AdminResultLinksScreen(),
      ),

      // Membership Routes
      GoRoute(
        path: '/membership/dashboard',
        builder: (context, state) => const MembershipDashboardScreen(),
      ),
      GoRoute(
        path: '/membership/counts',
        builder: (context, state) => const MembershipCountsScreen(),
      ),
      GoRoute(
        path: '/membership/reports',
        builder: (context, state) => const MembershipReportsScreen(),
      ),
      GoRoute(
        path: '/membership/teachers',
        builder: (context, state) => MembershipTeachersScreen(
          initialStatus: state.uri.queryParameters['status'],
        ),
      ),
      GoRoute(
        path: '/membership/teachers/new',
        builder: (context, state) => const MembershipTeacherFormScreen(),
      ),
      GoRoute(
        path: '/membership/teachers/:id',
        builder: (context, state) =>
            MembershipTeacherDetailScreen(id: state.pathParameters['id']!),
      ),
      GoRoute(
        path: '/membership/whats-new',
        builder: (context, state) => const MembershipWhatsNewScreen(),
      ),
    ],
  );
});
