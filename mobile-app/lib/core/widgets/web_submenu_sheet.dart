import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

/// WebSubmenuSheet
/// Displays the organized submenus matching the web app's navigation bar structure.
class WebSubmenuSheet extends StatelessWidget {
  final String category; // 'organization', 'downloads', 'links', 'all'

  const WebSubmenuSheet({
    super.key,
    this.category = 'all',
  });

  static Future<void> show(BuildContext context, {String category = 'all'}) {
    return showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => WebSubmenuSheet(category: category),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.82,
      ),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.only(
          topLeft: Radius.circular(24),
          topRight: Radius.circular(24),
        ),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Drag handle bar
          Center(
            child: Container(
              margin: const EdgeInsets.only(top: 10, bottom: 8),
              width: 44,
              height: 4,
              decoration: BoxDecoration(
                color: const Color(0xFFCBD5E1),
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),

          // Header
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _getHeaderTitle(category),
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F1E24),
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _getHeaderSubtitle(category),
                      style: const TextStyle(
                        fontSize: 12,
                        color: Color(0xFF64748B),
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
                IconButton(
                  icon: const Icon(Icons.close_rounded, color: Color(0xFF64748B)),
                  onPressed: () => Navigator.of(context).pop(),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: Color(0xFFE2E8F0)),

          // Submenu Content List
          Flexible(
            child: ListView(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              shrinkWrap: true,
              children: _buildSubmenuItems(context),
            ),
          ),
        ],
      ),
    );
  }

  String _getHeaderTitle(String cat) {
    switch (cat) {
      case 'organization':
        return 'Organization Menu';
      case 'downloads':
        return 'Downloads & Services';
      case 'links':
        return 'Online Portals & Links';
      default:
        return 'KPSTA Navigation Menus';
    }
  }

  String _getHeaderSubtitle(String cat) {
    switch (cat) {
      case 'organization':
        return 'സംഘടനാ നേതൃത്വം · State & District Committees';
      case 'downloads':
        return 'ഫോമുകൾ, സർവീസ് കോർണർ & അധ്യാപക സഹായികൾ';
      case 'links':
        return 'വിദ്യാഭ്യാസ പോർട്ടലുകൾ & പരീക്ഷാ ഫലങ്ങൾ';
      default:
        return 'വെബ്സൈറ്റിലെ അതേ ക്രമത്തിലുള്ള മെനു';
    }
  }

  List<Widget> _buildSubmenuItems(BuildContext context) {
    if (category == 'downloads') {
      return [
        _buildSubmenuRow(
          context,
          icon: Icons.insert_drive_file_outlined,
          title: 'Forms (ഫോമുകൾ)',
          subtitle: 'Salary bill, leave application, PF & welfare forms',
          color: const Color(0xFF016D77),
          onTap: () {
            Navigator.of(context).pop();
            context.go('/downloads');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.design_services_outlined,
          title: 'Service Corner (സർവീസ് കോർണർ)',
          subtitle: 'KSR service rules, pay revision, DA arrears & promotions',
          color: const Color(0xFF0D9488),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/services');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.school_outlined,
          title: 'Academic Corner (അക്കാദമിക് കോർണർ)',
          subtitle: 'LSS, USS model questions, handbook & learning aids',
          color: const Color(0xFF4F46E5),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/services');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.computer_outlined,
          title: 'Software Tools (സോഫ്റ്റ്‌വെയർ ടൂളുകൾ)',
          subtitle: 'Teacher utility software, income tax calculators & tools',
          color: const Color(0xFF2563EB),
          onTap: () {
            Navigator.of(context).pop();
            context.go('/downloads');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.archive_outlined,
          title: 'Melakal (മേളകൾ & കായിക മേളകൾ)',
          subtitle: 'Kalolsavam, Sasthrolsavam & Sports festival rules',
          color: const Color(0xFFD97706),
          onTap: () {
            Navigator.of(context).pop();
            context.go('/downloads');
          },
        ),
      ];
    }

    if (category == 'organization') {
      return [
        _buildSubmenuRow(
          context,
          icon: Icons.groups_outlined,
          title: 'State Office Bearers (സംസ്ഥാന ഭാരവാഹികൾ)',
          subtitle: 'President, General Secretary, Treasurer & State Centre',
          color: const Color(0xFF016D77),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/organization');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.map_outlined,
          title: 'District Office Bearers (ജില്ലാ ഭാരവാഹികൾ)',
          subtitle: '14 District committees and sub-district leadership',
          color: const Color(0xFF0284C7),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/organization/districts');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.history_edu_outlined,
          title: 'Former Leaders (മുൻകാല നേതാക്കൾ)',
          subtitle: 'Past state presidents, general secretaries & tenure',
          color: const Color(0xFFD97706),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/organization/former-leaders');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.note_alt_outlined,
          title: 'Memorandums (നിവേദനങ്ങൾ)',
          subtitle: 'Official teacher representations submitted to Govt & Ministers',
          color: const Color(0xFF4F46E5),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/organization/districts');
          },
        ),
        _buildSubmenuRow(
          context,
          icon: Icons.campaign_outlined,
          title: 'Notices & Posters (പോസ്റ്ററുകൾ & നോട്ടീസുകൾ)',
          subtitle: 'Agitation notices, campaign flyers & conference posters',
          color: const Color(0xFFDC2626),
          onTap: () {
            Navigator.of(context).pop();
            context.push('/gallery');
          },
        ),
      ];
    }

    // Default 'all' menu matching web navigation bar
    return [
      _buildSectionHeader('1. Organization (സംഘടന)'),
      _buildSubmenuRow(
        context,
        icon: Icons.groups_outlined,
        title: 'State Office Bearers',
        subtitle: 'State Committee & Leadership',
        color: const Color(0xFF016D77),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/organization');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.map_outlined,
        title: 'District Office Bearers',
        subtitle: '14 Revenue District Leadership',
        color: const Color(0xFF0284C7),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/organization/districts');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.history_edu_outlined,
        title: 'Former Leaders',
        subtitle: 'Past Presidents & History',
        color: const Color(0xFFD97706),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/organization/former-leaders');
        },
      ),

      const SizedBox(height: 12),
      _buildSectionHeader('2. Order & Circular (ഉത്തരവുകൾ)'),
      _buildSubmenuRow(
        context,
        icon: Icons.description_outlined,
        title: 'Order & Circular Repository',
        subtitle: 'Search & download all department orders',
        color: const Color(0xFF016D77),
        onTap: () {
          Navigator.of(context).pop();
          context.go('/circulars');
        },
      ),

      const SizedBox(height: 12),
      _buildSectionHeader('3. Downloads (ഡൗൺലോഡുകൾ)'),
      _buildSubmenuRow(
        context,
        icon: Icons.insert_drive_file_outlined,
        title: 'Forms & Documents',
        subtitle: 'Salary, leave & PF claim forms',
        color: const Color(0xFF0D9488),
        onTap: () {
          Navigator.of(context).pop();
          context.go('/downloads');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.design_services_outlined,
        title: 'Service Corner',
        subtitle: 'Teacher service rules & pension guidance',
        color: const Color(0xFF059669),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/services');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.school_outlined,
        title: 'Academic Corner',
        subtitle: 'LSS & USS model questions & materials',
        color: const Color(0xFF4F46E5),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/services');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.computer_outlined,
        title: 'Software Tools & Melakal',
        subtitle: 'Useful utilities and festival rules',
        color: const Color(0xFF2563EB),
        onTap: () {
          Navigator.of(context).pop();
          context.go('/downloads');
        },
      ),

      const SizedBox(height: 12),
      _buildSectionHeader('4. Gallery & Media (ഗാലറി)'),
      _buildSubmenuRow(
        context,
        icon: Icons.photo_library_outlined,
        title: 'Photo Gallery & Reaction Posters',
        subtitle: 'Conferences, protests & state events',
        color: const Color(0xFFEA580C),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/gallery');
        },
      ),

      const SizedBox(height: 12),
      _buildSectionHeader('5. Online Links (ലിങ്കുകൾ)'),
      _buildSubmenuRow(
        context,
        icon: Icons.open_in_new_rounded,
        title: 'Online Links & Portals',
        subtitle: 'Sametham, SPARK, Directorate educational portals',
        color: const Color(0xFF0284C7),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/links/quick');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.grade_outlined,
        title: 'Exam Results (SSLC, Plus Two, KTET)',
        subtitle: 'Direct examination portals & roll number lookups',
        color: const Color(0xFF7C3AED),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/links/results');
        },
      ),

      const SizedBox(height: 12),
      _buildSectionHeader('6. Contact & Support (ബന്ധപ്പെടുക)'),
      _buildSubmenuRow(
        context,
        icon: Icons.mail_outline_rounded,
        title: 'Contact State Centre',
        subtitle: 'KPSTA Bhavan Trivandrum phone & email',
        color: const Color(0xFF016D77),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/contact');
        },
      ),
      _buildSubmenuRow(
        context,
        icon: Icons.volunteer_activism_outlined,
        title: 'Teachers Welfare Fund Donation',
        subtitle: 'Contribute to member benevolent welfare',
        color: const Color(0xFF059669),
        onTap: () {
          Navigator.of(context).pop();
          context.push('/donation');
        },
      ),
    ];
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(top: 4, bottom: 6, left: 4),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w800,
          color: Color(0xFF016D77),
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _buildSubmenuRow(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 6),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: ListTile(
        onTap: onTap,
        dense: true,
        visualDensity: VisualDensity.compact,
        leading: Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
        title: Text(
          title,
          style: const TextStyle(
            fontSize: 13.5,
            fontWeight: FontWeight.w700,
            color: Color(0xFF0F1E24),
          ),
        ),
        subtitle: Text(
          subtitle,
          style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        trailing: const Icon(
          Icons.arrow_forward_ios_rounded,
          size: 13,
          color: Color(0xFF94A3B8),
        ),
      ),
    );
  }
}

