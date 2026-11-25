import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/confirmation_dialog.dart';
import '../../../auth/presentation/auth_providers.dart';

/// MoreMenuScreen
/// Beautiful, clean sectionized LIST MENU with:
/// - Differentiated background colors per section
/// - Ambient KPSTA flag colors in the background
/// - Premium glassmorphic frosted cards with blur and translucent borders
/// - Pure list menu layout (never grids or tiles)
/// - 130px bottom clearance for floating navigation bar
class MoreMenuScreen extends ConsumerWidget {
  const MoreMenuScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authNotifierProvider);
    final isAuthenticated = authState.status == AuthStatus.authenticated;
    final user = authState.user;

    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: const AppTopBar(
        title: 'KPSTA Navigation & Menus',
        showBackButton: false,
      ),
      body: Stack(
        children: [
          // Scrollable Sectionized List Menu with Glassmorphic Cards over Global Flag Background
          ListView(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 130), // Accommodates floating nav bar
            physics: const BouncingScrollPhysics(),
            children: [
              // Staff & Member Portal Glass Card
              _buildAuthGlassCard(context, ref, isAuthenticated, user),

              const SizedBox(height: 20),

              // SECTION 1: Organization (സംഘടന) - Soft Teal Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Organization',
                malayalam: 'സംഘടനാനേതൃത്വം & ഘടന',
                icon: Icons.groups_rounded,
                themeColor: const Color(0xFF016D77),
                glassBgColor: const Color(0xFFE8F7F8),
                badgeText: '5 items',
                items: [
                  _MenuItemData(
                    icon: Icons.badge_outlined,
                    title: 'State Office Bearers',
                    malayalam: 'സംസ്ഥാന ഭാരവാഹികൾ',
                    tag: 'Leadership',
                    onTap: () => context.push('/organization'),
                  ),
                  _MenuItemData(
                    icon: Icons.map_outlined,
                    title: 'District Committees',
                    malayalam: 'ജില്ലാ കമ്മിറ്റികൾ (14 റവന്യൂ ജില്ലകൾ)',
                    tag: '14 Districts',
                    onTap: () => context.push('/organization/districts'),
                  ),
                  _MenuItemData(
                    icon: Icons.history_edu_outlined,
                    title: 'Former Association Leaders',
                    malayalam: 'മുൻ സംസ്ഥാന ഭാരവാഹികൾ',
                    onTap: () => context.push('/organization/former-leaders'),
                  ),
                  _MenuItemData(
                    icon: Icons.assignment_outlined,
                    title: 'Memorandums Submitted',
                    malayalam: 'സർക്കാരിന് സമർപ്പിച്ച നിവേദനങ്ങൾ',
                    onTap: () => context.push('/home'),
                  ),
                  _MenuItemData(
                    icon: Icons.campaign_outlined,
                    title: 'Agitation Notices & Posters',
                    malayalam: 'സമര അറിയിപ്പുകൾ & പോസ്റ്ററുകൾ',
                    onTap: () => context.push('/gallery'),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              // SECTION 2: Orders & Circulars (ഉത്തരവുകൾ) - Soft Sky Blue Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Orders & Circulars',
                malayalam: 'ഔദ്യോഗിക ഉത്തരവുകൾ & സർക്കുലറുകൾ',
                icon: Icons.description_rounded,
                themeColor: const Color(0xFF0284C7),
                glassBgColor: const Color(0xFFE3F3FD),
                badgeText: 'Official',
                items: [
                  _MenuItemData(
                    icon: Icons.file_present_rounded,
                    title: 'Official Orders & Circulars',
                    malayalam: 'General Education, HSE & VHSE ഉത്തരവുകൾ',
                    tag: 'Latest Orders',
                    onTap: () => context.go('/circulars'),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              // SECTION 3: Downloads & Teacher Corner (ഡൗൺലോഡുകൾ) - Soft Mint / Emerald Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Downloads & Teacher Corner',
                malayalam: 'ഡൗൺലോഡുകൾ & അക്കാദമിക് റിസോഴ്സുകൾ',
                icon: Icons.download_rounded,
                themeColor: const Color(0xFF059669),
                glassBgColor: const Color(0xFFD6F8E8),
                badgeText: '6 items',
                items: [
                  _MenuItemData(
                    icon: Icons.feed_outlined,
                    title: 'Official Forms',
                    malayalam: 'സാലറി ബിൽ, PF, ലീവ് & ക്ലെയിം ഫോമുകൾ',
                    tag: 'PDFs',
                    onTap: () => context.go('/downloads'),
                  ),
                  _MenuItemData(
                    icon: Icons.volunteer_activism_outlined,
                    title: 'Service Corner',
                    malayalam: 'KSR സർവീസ് നിയമങ്ങൾ, പെൻഷൻ, ശമ്പള പരിഷ്കരണം',
                    tag: 'KSR Rules',
                    onTap: () => context.push('/services'),
                  ),
                  _MenuItemData(
                    icon: Icons.school_outlined,
                    title: 'Academic Corner',
                    malayalam: 'LSS, USS മാതൃകാ ചോദ്യങ്ങൾ, ഹാൻഡ്ബുക്ക്',
                    tag: 'Study Aids',
                    onTap: () => context.push('/services'),
                  ),
                  _MenuItemData(
                    icon: Icons.terminal_outlined,
                    title: 'Software Tools & Calculators',
                    malayalam: 'സാലറി, ഇൻകം ടാക്സ് & സർവീസ് കാൽക്കുലേറ്ററുകൾ',
                    tag: 'Tools',
                    onTap: () => context.go('/downloads'),
                  ),
                  _MenuItemData(
                    icon: Icons.celebration_outlined,
                    title: 'School Melakal Manuals',
                    malayalam: 'കലോത്സവം, ശാസ്ത്രോത്സവം, കായികമേള ചട്ടങ്ങൾ',
                    onTap: () => context.go('/downloads'),
                  ),
                  _MenuItemData(
                    icon: Icons.menu_book_outlined,
                    title: 'Adayapaka Sabham Magazine',
                    malayalam: 'കെ.പി.എസ്.ടി.എ ഔദ്യോഗിക മാസിക ലക്കങ്ങൾ',
                    tag: 'Magazine',
                    onTap: () => context.push('/services/adayapaka'),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              // SECTION 4: Media & Photo Gallery (ചിത്രശാല) - Soft Sunset Peach Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Media & Galleries',
                malayalam: 'ചിത്രശാല & ഔദ്യോഗിക പോസ്റ്ററുകൾ',
                icon: Icons.photo_library_rounded,
                themeColor: const Color(0xFFEA580C),
                glassBgColor: const Color(0xFFFFECE0),
                badgeText: '2 items',
                items: [
                  _MenuItemData(
                    icon: Icons.collections_outlined,
                    title: 'Photo Gallery & Albums',
                    malayalam: 'സംസ്ഥാന സമ്മേളനങ്ങൾ & സംഘടനാ പരിപാടികൾ',
                    tag: 'Albums',
                    onTap: () => context.push('/gallery'),
                  ),
                  _MenuItemData(
                    icon: Icons.portrait_outlined,
                    title: 'Official Reaction Posters',
                    malayalam: 'സമകാലിക വിഷയങ്ങളിലെ നിലപാടുകൾ & പോസ്റ്ററുകൾ',
                    tag: 'Posters',
                    onTap: () => context.push('/reaction-gallery'),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              // SECTION 5: Online Portals (പോർട്ടലുകൾ) - Soft Lavender Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Official Portals',
                malayalam: 'വിദ്യാഭ്യാസ പോർട്ടലുകൾ & ലിങ്കുകൾ',
                icon: Icons.language_rounded,
                themeColor: const Color(0xFF6366F1),
                glassBgColor: const Color(0xFFEDE9FE),
                badgeText: 'Portals',
                items: [
                  _MenuItemData(
                    icon: Icons.link_rounded,
                    title: 'Quick Department Links',
                    malayalam: 'സമേതം, സമ്പൂർണ്ണ, BISE, സ്പാർക്ക് പോർട്ടലുകൾ',
                    tag: 'Direct Links',
                    onTap: () => context.push('/links/quick'),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              // SECTION 6: Contact & Support (സമ്പർക്കം) - Soft Pine Teal Glass
              _buildGlassSectionCard(
                context: context,
                title: 'Contact & Support',
                malayalam: 'സമ്പർക്കം, അന്വേഷണങ്ങൾ & നയങ്ങൾ',
                icon: Icons.contact_support_rounded,
                themeColor: const Color(0xFF0F766E),
                glassBgColor: const Color(0xFFD4FAF1),
                badgeText: '3 items',
                items: [
                  _MenuItemData(
                    icon: Icons.mail_outline_rounded,
                    title: 'Contact Us',
                    malayalam: 'ബന്ധപ്പെടുക & സന്ദേശം അയക്കുക',
                    tag: 'Enquiry',
                    onTap: () => context.push('/contact'),
                  ),
                  _MenuItemData(
                    icon: Icons.admin_panel_settings_outlined,
                    title: isAuthenticated && user?.isAdmin == true
                        ? 'Admin Dashboard'
                        : 'Admin Login',
                    malayalam: isAuthenticated && user?.isAdmin == true
                        ? 'അഡ്മിൻ മാനേജ്‌മെന്റ് ഡാഷ്‌ബോർഡ്'
                        : 'സംസ്ഥാന & ജില്ലാ അഡ്മിൻ ലോഗിൻ പോർട്ടൽ',
                    tag: isAuthenticated && user?.isAdmin == true ? 'Admin' : 'Login',
                    onTap: () {
                      if (isAuthenticated && user?.isAdmin == true) {
                        context.push('/admin/dashboard');
                      } else {
                        context.push('/login');
                      }
                    },
                  ),
                  _MenuItemData(
                    icon: Icons.policy_outlined,
                    title: 'Privacy Policy & Terms',
                    malayalam: 'ഡാറ്റാ സംരക്ഷണ നയങ്ങളും വ്യവസ്ഥകളും',
                    onTap: () => context.push('/privacy'),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ===========================================================================
  // GLASSMORPHIC SECTION CARD WITH DIFFERENTIATED PASTEL GLASS BACKGROUND
  // ===========================================================================
  Widget _buildGlassSectionCard({
    required BuildContext context,
    required String title,
    required String malayalam,
    required IconData icon,
    required Color themeColor,
    required Color glassBgColor,
    required String badgeText,
    required List<_MenuItemData> items,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // 1. Distinct Section Header Bar
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
          child: Row(
            children: [
              // Icon Badge with Glow
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [themeColor, themeColor.withValues(alpha: 0.85)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(9),
                  boxShadow: [
                    BoxShadow(
                      color: themeColor.withValues(alpha: 0.25),
                      blurRadius: 6,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Center(
                  child: Icon(icon, color: Colors.white, size: 17),
                ),
              ),
              const SizedBox(width: 10),

              // Title and Malayalam Subtitle
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title.toUpperCase(),
                      style: TextStyle(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w800,
                        color: themeColor,
                        letterSpacing: 0.6,
                        height: 1.15,
                      ),
                    ),
                    const SizedBox(height: 1),
                    Text(
                      malayalam,
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF64748B),
                        height: 1.15,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),

              // Category Count Pill
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 3.5),
                decoration: BoxDecoration(
                  color: themeColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: themeColor.withValues(alpha: 0.25),
                    width: 0.8,
                  ),
                ),
                child: Text(
                  badgeText,
                  style: TextStyle(
                    fontSize: 10.5,
                    fontWeight: FontWeight.w700,
                    color: themeColor,
                    letterSpacing: 0.2,
                  ),
                ),
              ),
            ],
          ),
        ),

        const SizedBox(height: 6),

        // 2. High-Performance Frosted Card with Differentiated Background Color
        Container(
          decoration: BoxDecoration(
            color: glassBgColor.withValues(alpha: 0.88),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(
              color: Colors.white,
              width: 1.4,
            ),
            boxShadow: [
              BoxShadow(
                color: themeColor.withValues(alpha: 0.08),
                blurRadius: 10,
                offset: const Offset(0, 3),
              ),
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 4,
                offset: const Offset(0, 1),
              ),
            ],
          ),
          child: Column(
                children: List.generate(items.length, (index) {
                  final item = items[index];
                  final isLast = index == items.length - 1;

                  return Column(
                    children: [
                      Material(
                        color: Colors.transparent,
                        child: InkWell(
                          onTap: () {
                            HapticFeedback.lightImpact();
                            item.onTap();
                          },
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            child: Row(
                              children: [
                                // Left Icon Pill Tinted with Section Color
                                Container(
                                  width: 38,
                                  height: 38,
                                  decoration: BoxDecoration(
                                    color: Colors.white.withValues(alpha: 0.85),
                                    borderRadius: BorderRadius.circular(10),
                                    border: Border.all(
                                      color: themeColor.withValues(alpha: 0.18),
                                      width: 1,
                                    ),
                                    boxShadow: [
                                      BoxShadow(
                                        color: themeColor.withValues(alpha: 0.10),
                                        blurRadius: 4,
                                        offset: const Offset(0, 1),
                                      ),
                                    ],
                                  ),
                                  child: Center(
                                    child: Icon(
                                      item.icon,
                                      color: themeColor,
                                      size: 19,
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),

                                // Text Content (English Title + Malayalam Subtitle)
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        item.title,
                                        style: const TextStyle(
                                          fontSize: 13.5,
                                          fontWeight: FontWeight.w700,
                                          color: Color(0xFF0F1E24),
                                          letterSpacing: 0.1,
                                          height: 1.2,
                                        ),
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        item.malayalam,
                                        style: const TextStyle(
                                          fontSize: 11,
                                          fontWeight: FontWeight.w500,
                                          color: Color(0xFF475569),
                                          height: 1.2,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),

                                // Optional Badge Tag
                                if (item.tag != null) ...[
                                  Container(
                                    margin: const EdgeInsets.only(right: 8),
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 7,
                                      vertical: 2.5,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Colors.white.withValues(alpha: 0.90),
                                      borderRadius: BorderRadius.circular(6),
                                      border: Border.all(
                                        color: themeColor.withValues(alpha: 0.20),
                                        width: 0.7,
                                      ),
                                    ),
                                    child: Text(
                                      item.tag!,
                                      style: TextStyle(
                                        fontSize: 9.5,
                                        fontWeight: FontWeight.w700,
                                        color: themeColor,
                                      ),
                                    ),
                                  ),
                                ],

                                // Trailing Chevron
                                Icon(
                                  Icons.chevron_right_rounded,
                                  color: themeColor.withValues(alpha: 0.65),
                                  size: 20,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),

                      // Inset Divider (stops neatly before the icon column)
                      if (!isLast)
                        Divider(
                          height: 1,
                          thickness: 0.8,
                          indent: 64,
                          endIndent: 14,
                          color: themeColor.withValues(alpha: 0.12),
                        ),
                    ],
                  );
                }),
              ),
            ),
          ],
        );
  }

  // ===========================================================================
  // AUTH / STAFF & MEMBER PORTAL GLASS CARD
  // ===========================================================================
  Widget _buildAuthGlassCard(
    BuildContext context,
    WidgetRef ref,
    bool isAuthenticated,
    dynamic user,
  ) {
    if (isAuthenticated) {
      return Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFF0F2C33), Color(0xFF01555E)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: Colors.white.withValues(alpha: 0.20),
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF016D77).withValues(alpha: 0.24),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.15),
                shape: BoxShape.circle,
                border: Border.all(
                  color: const Color(0xFFEE6A2A),
                  width: 2,
                ),
              ),
              child: const Center(
                child: Icon(Icons.person_rounded, color: Colors.white, size: 24),
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    user?.name ?? 'Member',
                    style: const TextStyle(
                      fontSize: 15.5,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      letterSpacing: 0.2,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    user?.isAdmin == true
                        ? 'System Administrator'
                        : 'KPSTA Verified Member',
                    style: const TextStyle(
                      fontSize: 11.5,
                      color: Color(0xFF86CCD2),
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            ElevatedButton(
              onPressed: () {
                if (user?.isAdmin == true) {
                  context.push('/admin/dashboard');
                } else {
                  context.push('/membership/dashboard');
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFFEE6A2A),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
                elevation: 0,
                minimumSize: const Size(0, 36),
              ),
              child: const Text(
                'Dashboard',
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
              ),
            ),
            const SizedBox(width: 6),
            IconButton(
              icon: const Icon(Icons.logout_rounded, color: Colors.white70, size: 20),
              tooltip: 'Sign Out',
              onPressed: () async {
                final confirm = await ConfirmationDialog.show(
                  context,
                  title: 'Sign Out',
                  message: 'Are you sure you want to sign out from your KPSTA account?',
                  confirmLabel: 'Sign Out',
                );
                if (confirm) {
                  await ref.read(authNotifierProvider.notifier).logout();
                }
              },
            ),
          ],
        ),
      );
    }

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF0F2C33), Color(0xFF01555E)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: Colors.white.withValues(alpha: 0.22),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF016D77).withValues(alpha: 0.24),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.14),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: Colors.white.withValues(alpha: 0.25),
                    width: 1,
                  ),
                ),
                child: const Center(
                  child: Icon(Icons.lock_outline_rounded, color: Colors.white, size: 22),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: const [
                    Text(
                      'Admin & Member Portal',
                      style: TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        letterSpacing: 0.2,
                      ),
                    ),
                    SizedBox(height: 2),
                    Text(
                      'Admin sign-in / WhatsApp OTP',
                      style: TextStyle(
                        fontSize: 11,
                        color: Color(0xFF86CCD2),
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
              ElevatedButton(
                onPressed: () => context.push('/login'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFEE6A2A),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  elevation: 0,
                  minimumSize: const Size(0, 36),
                ),
                child: const Text(
                  'Admin Login',
                  style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
        );
  }
}

/// Helper model for Menu items
class _MenuItemData {
  final IconData icon;
  final String title;
  final String malayalam;
  final String? tag;
  final VoidCallback onTap;

  const _MenuItemData({
    required this.icon,
    required this.title,
    required this.malayalam,
    this.tag,
    required this.onTap,
  });
}
