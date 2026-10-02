import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../auth/presentation/auth_providers.dart';

class MoreMenuScreen extends ConsumerWidget {
  const MoreMenuScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authNotifierProvider);
    final isAuthenticated = authState.status == AuthStatus.authenticated;
    final user = authState.user;

    return Scaffold(
      appBar: const AppTopBar(
        title: 'More & Information',
        showBackButton: false,
      ),
      body: ListView(
        padding: const EdgeInsets.symmetric(vertical: 12),
        children: [
          // User / Auth Header Card
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppColors.primaryDark, AppColors.primary],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 26,
                  backgroundColor: AppColors.white.withOpacity(0.2),
                  child: Icon(
                    isAuthenticated ? Icons.person : Icons.lock_outline,
                    color: AppColors.white,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        isAuthenticated
                            ? (user?.username ?? 'Authorized User')
                            : 'Staff & Member Portal',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: AppColors.white,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        isAuthenticated
                            ? 'Role: ${user?.role.toUpperCase()}'
                            : 'Sign in with WhatsApp OTP',
                        style: TextStyle(
                          fontSize: 12,
                          color: AppColors.white.withOpacity(0.8),
                        ),
                      ),
                    ],
                  ),
                ),
                if (isAuthenticated)
                  IconButton(
                    icon: const Icon(Icons.logout, color: AppColors.white),
                    tooltip: 'Sign Out',
                    onPressed: () async {
                      final confirmed = await ConfirmationDialog.show(
                        context,
                        title: 'Sign Out',
                        message: 'Are you sure you want to end your session?',
                        confirmLabel: 'Sign Out',
                        isDestructive: true,
                      );
                      if (confirmed) {
                        await ref
                            .read(authNotifierProvider.notifier)
                            .logout();
                      }
                    },
                  )
                else
                  ElevatedButton(
                    onPressed: () => context.push('/login'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.orange,
                      padding: const EdgeInsets.symmetric(
                        horizontal: 14,
                        vertical: 8,
                      ),
                      minimumSize: const Size(80, 36),
                    ),
                    child: const Text('Login', style: TextStyle(fontSize: 13)),
                  ),
              ],
            ),
          ),

          if (isAuthenticated) ...[
            const SizedBox(height: 12),
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
              child: Text(
                'Staff Dashboards',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textMuted.withOpacity(0.8),
                  letterSpacing: 0.8,
                ),
              ),
            ),
            if (user?.isAdmin == true)
              _buildMenuItem(
                icon: Icons.admin_panel_settings_outlined,
                title: 'Administrator Dashboard',
                subtitle: 'Manage news, circulars, media & leadership',
                color: AppColors.indigo,
                onTap: () => context.push('/admin/dashboard'),
              ),
            _buildMenuItem(
              icon: Icons.badge_outlined,
              title: 'Membership Dashboard',
              subtitle: 'Manage teachers, school verification & WhatsNew',
              color: AppColors.green,
              onTap: () => context.push('/membership/dashboard'),
            ),
          ],

          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
            child: Text(
              'Association & Leadership',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: AppColors.textMuted.withOpacity(0.8),
                letterSpacing: 0.8,
              ),
            ),
          ),
          _buildMenuItem(
            icon: Icons.people_outline,
            title: 'State Office Bearers',
            subtitle: 'President, General Secretary & State Committee',
            onTap: () => context.push('/organization'),
          ),
          _buildMenuItem(
            icon: Icons.map_outlined,
            title: 'Districts of Kerala',
            subtitle: '14 District committees and active leadership',
            onTap: () => context.push('/organization/districts'),
          ),
          _buildMenuItem(
            icon: Icons.history_edu_outlined,
            title: 'Former Association Leaders',
            subtitle: 'Historical leaders and past presidents tenure',
            onTap: () => context.push('/organization/former-leaders'),
          ),

          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
            child: Text(
              'Services & Media',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: AppColors.textMuted.withOpacity(0.8),
                letterSpacing: 0.8,
              ),
            ),
          ),
          _buildMenuItem(
            icon: Icons.design_services_outlined,
            title: 'Service Corner',
            subtitle: 'Teacher service rules, promotion guides & benefits',
            onTap: () => context.push('/services'),
          ),
          _buildMenuItem(
            icon: Icons.menu_book_outlined,
            title: 'Adayapaka Sabham',
            subtitle: 'Association official magazine & periodical',
            onTap: () => context.push('/services/adayapaka'),
          ),
          _buildMenuItem(
            icon: Icons.photo_library_outlined,
            title: 'Photo Gallery',
            subtitle: 'Events, state conferences & protests albums',
            onTap: () => context.push('/gallery'),
          ),

          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
            child: Text(
              'Links & Exam Portals',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: AppColors.textMuted.withOpacity(0.8),
                letterSpacing: 0.8,
              ),
            ),
          ),
          _buildMenuItem(
            icon: Icons.open_in_new,
            title: 'Quick Links',
            subtitle: 'Sametham, SPARK, Directorate educational portals',
            onTap: () => context.push('/links/quick'),
          ),
          _buildMenuItem(
            icon: Icons.school_outlined,
            title: 'Exam Results',
            subtitle: 'SSLC, Plus Two, and KTET examination results',
            onTap: () => context.push('/links/results'),
          ),

          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
            child: Text(
              'Help & Support',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: AppColors.textMuted.withOpacity(0.8),
                letterSpacing: 0.8,
              ),
            ),
          ),
          _buildMenuItem(
            icon: Icons.volunteer_activism_outlined,
            title: 'Donate / Support KPSTA',
            subtitle: 'Contribute to the Teachers Welfare Fund',
            color: AppColors.green,
            onTap: () => context.push('/donation'),
          ),
          _buildMenuItem(
            icon: Icons.mail_outline,
            title: 'Contact State Office',
            subtitle: 'KPSTA Bhavan Trivandrum phone, email & enquiry',
            onTap: () => context.push('/contact'),
          ),
          _buildMenuItem(
            icon: Icons.privacy_tip_outlined,
            title: 'Privacy Policy',
            subtitle: 'Data protection and member security statement',
            onTap: () => context.push('/privacy'),
          ),
        ],
      ),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
    Color color = AppColors.primary,
  }) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: ListTile(
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color, size: 22),
        ),
        title: Text(
          title,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.textDark,
          ),
        ),
        subtitle: Text(
          subtitle,
          style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
        ),
        trailing: const Icon(
          Icons.arrow_forward_ios,
          size: 14,
          color: AppColors.textLight,
        ),
        onTap: onTap,
      ),
    );
  }
}
