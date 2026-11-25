import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';

/// KPSTA Footer matching web and mobile reference design with all sub-menus
class KpstaFooter extends StatelessWidget {
  const KpstaFooter({super.key});

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:${phone.replaceAll(RegExp(r'[^0-9+]'), '')}');
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  Future<void> _sendEmail(String email) async {
    final uri = Uri.parse('mailto:$email');
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      color: const Color(0xFF0F1E24),
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 32),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Logo & Organization Name
          Image.asset(
            'assets/images/logo.png',
            height: 52,
            errorBuilder: (_, __, ___) => const Icon(
              Icons.school,
              size: 40,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'KERALA PRADESH\nSCHOOL TEACHERS\' ASSOCIATION',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.white,
              fontSize: 13,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.5,
              height: 1.3,
            ),
          ),
          const SizedBox(height: 4),
          const Text(
            'AFFILIATED TO AIPTF, AIFTO & EDUCATION INTERNATIONAL',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Color(0xFF94A3B8),
              fontSize: 8.5,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.4,
            ),
          ),
          const SizedBox(height: 22),
          const Divider(color: Color(0xFF1E293B), thickness: 1),
          const SizedBox(height: 18),

          // All Menus and Sub-Menus (Structured 3-Column / Responsive Sitemap)
          LayoutBuilder(
            builder: (context, constraints) {
              final isNarrow = constraints.maxWidth < 360;

              return Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Column 1: Organization & Sub-menus
                      Expanded(
                        child: _FooterSection(
                          title: 'Organization',
                          links: [
                            _FooterLinkItem('State Office Bearers', () => context.push('/organization')),
                            _FooterLinkItem('District Committees', () => context.push('/organization/districts')),
                            _FooterLinkItem('Former Leaders', () => context.push('/organization/former-leaders')),
                            _FooterLinkItem('Memorandums', () => context.push('/home')),
                            _FooterLinkItem('Notices & Posters', () => context.push('/gallery')),
                          ],
                        ),
                      ),
                      const SizedBox(width: 14),

                      // Column 2: Downloads & Sub-menus
                      Expanded(
                        child: _FooterSection(
                          title: 'Downloads',
                          links: [
                            _FooterLinkItem('Official Forms', () => context.push('/downloads?tab=forms')),
                            _FooterLinkItem('Service Corner', () => context.push('/services')),
                            _FooterLinkItem('Academic Corner', () => context.push('/services')),
                            _FooterLinkItem('Software Tools', () => context.push('/downloads?tab=softwares')),
                            _FooterLinkItem('School Melakal', () => context.push('/downloads?tab=melakal')),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // Quick Links / Core Portals
                  _FooterSection(
                    title: 'Quick Links & Portals',
                    isGrid: !isNarrow,
                    links: [
                      _FooterLinkItem('Home', () => context.go('/home')),
                      _FooterLinkItem('Order & Circular', () => context.go('/circulars')),
                      _FooterLinkItem('News Window', () => context.push('/news')),
                      _FooterLinkItem('Photo Gallery', () => context.push('/gallery')),
                      _FooterLinkItem('Online Links', () => context.push('/links/quick')),
                      _FooterLinkItem('Contact Us', () => context.push('/contact')),
                      _FooterLinkItem('Admin Login', () => context.push('/login')),
                    ],
                  ),
                ],
              );
            },
          ),

          const SizedBox(height: 24),
          const Divider(color: Color(0xFF1E293B), thickness: 1),
          const SizedBox(height: 18),

          // Clean, Full-Width Contact Information
          _buildContactRow(
            icon: Icons.location_on,
            text: 'KPSTA BHAVAN, Chinmaya School Lane, Kunnumpuram, Trivandrum -1',
          ),
          const SizedBox(height: 12),
          InkWell(
            onTap: () => _makeCall('0471-2575797'),
            child: _buildContactRow(
              icon: Icons.call,
              text: '0471 - 2575797 (State Centre Helpdesk)',
            ),
          ),
          const SizedBox(height: 12),
          InkWell(
            onTap: () => _sendEmail('kpsta.in@gmail.com'),
            child: _buildContactRow(
              icon: Icons.email,
              text: 'kpsta.in@gmail.com',
            ),
          ),

          const SizedBox(height: 22),
          const Divider(color: Color(0xFF1E293B), thickness: 1),
          const SizedBox(height: 12),

          // Copyright
          Text(
            'Copyright © ${DateTime.now().year}, All Rights Reserved',
            style: const TextStyle(
              color: Color(0xFF64748B),
              fontSize: 11,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildContactRow({required IconData icon, required String text}) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 16, color: const Color(0xFFF05A22)),
        const SizedBox(width: 10),
        Expanded(
          child: Text(
            text,
            style: const TextStyle(
              color: Color(0xFFCBD5E1),
              fontSize: 12,
              height: 1.35,
            ),
          ),
        ),
      ],
    );
  }
}

class _FooterLinkItem {
  final String label;
  final VoidCallback onTap;

  const _FooterLinkItem(this.label, this.onTap);
}

class _FooterSection extends StatelessWidget {
  final String title;
  final List<_FooterLinkItem> links;
  final bool isGrid;

  const _FooterSection({
    required this.title,
    required this.links,
    this.isGrid = false,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title.toUpperCase(),
          style: const TextStyle(
            color: Color(0xFFF05A22),
            fontSize: 11.5,
            fontWeight: FontWeight.w800,
            letterSpacing: 0.6,
          ),
        ),
        const SizedBox(height: 4),
        Container(
          width: 32,
          height: 2,
          color: const Color(0xFFF05A22).withValues(alpha: 0.6),
        ),
        const SizedBox(height: 10),
        if (isGrid)
          Wrap(
            spacing: 16,
            runSpacing: 8,
            children: links.map((link) => _FooterLinkWidget(link: link)).toList(),
          )
        else
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: links.map((link) => _FooterLinkWidget(link: link)).toList(),
          ),
      ],
    );
  }
}

class _FooterLinkWidget extends StatelessWidget {
  final _FooterLinkItem link;

  const _FooterLinkWidget({required this.link});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: InkWell(
        onTap: link.onTap,
        borderRadius: BorderRadius.circular(4),
        child: Text(
          link.label,
          style: const TextStyle(
            color: Color(0xFFE2E8F0),
            fontSize: 12,
            fontWeight: FontWeight.w400,
            height: 1.3,
          ),
        ),
      ),
    );
  }
}
