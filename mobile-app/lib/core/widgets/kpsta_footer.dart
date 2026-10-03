import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constants/app_colors.dart';

/// KPSTA Footer matching web and mobile reference design
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
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 32),
      child: Column(
        children: [
          // Logo & Name
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
          const SizedBox(height: 24),
          const Divider(color: Color(0xFF1E293B)),
          const SizedBox(height: 16),

          // Navigation Links & Contact Details
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Links Column
              Expanded(
                flex: 4,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _FooterLink(
                      label: 'Home',
                      onTap: () => context.go('/home'),
                    ),
                    _FooterLink(
                      label: 'Organization',
                      onTap: () => context.push('/organization'),
                    ),
                    _FooterLink(
                      label: 'Order & Circular',
                      onTap: () => context.go('/circulars'),
                    ),
                    _FooterLink(
                      label: 'Downloads',
                      onTap: () => context.go('/downloads'),
                    ),
                    _FooterLink(
                      label: 'Gallery',
                      onTap: () => context.push('/gallery'),
                    ),
                    _FooterLink(
                      label: 'Online Links',
                      onTap: () => context.push('/links'),
                    ),
                    _FooterLink(
                      label: 'Contact',
                      onTap: () => context.push('/contact'),
                    ),
                  ],
                ),
              ),

              const SizedBox(width: 16),

              // Contact Info Column
              Expanded(
                flex: 6,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Address
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: const [
                        Icon(
                          Icons.location_on,
                          size: 16,
                          color: Color(0xFFF05A22),
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'KPSTA BHAVAN, Chinmaya School Lane, Kunnumpuram, Trivandrum -1',
                            style: TextStyle(
                              color: Color(0xFFCBD5E1),
                              fontSize: 12,
                              height: 1.4,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Phone
                    InkWell(
                      onTap: () => _makeCall('0471-2575797'),
                      child: Row(
                        children: const [
                          Icon(
                            Icons.call,
                            size: 16,
                            color: Color(0xFFF05A22),
                          ),
                          SizedBox(width: 8),
                          Text(
                            '0471 - 2575797',
                            style: TextStyle(
                              color: Color(0xFFCBD5E1),
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Email
                    InkWell(
                      onTap: () => _sendEmail('kpsta.in@gmail.com'),
                      child: Row(
                        children: const [
                          Icon(
                            Icons.email,
                            size: 16,
                            color: Color(0xFFF05A22),
                          ),
                          SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'kpsta.in@gmail.com',
                              style: TextStyle(
                                color: Color(0xFFCBD5E1),
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),

          const SizedBox(height: 28),
          const Divider(color: Color(0xFF1E293B)),
          const SizedBox(height: 12),

          // Copyright
          const Text(
            'Copyright © 2016, All Rights Reserved',
            style: TextStyle(
              color: Color(0xFF64748B),
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }
}

class _FooterLink extends StatelessWidget {
  final String label;
  final VoidCallback onTap;

  const _FooterLink({required this.label, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: InkWell(
        onTap: onTap,
        child: Text(
          label,
          style: const TextStyle(
            color: Color(0xFFE2E8F0),
            fontSize: 12.5,
            fontWeight: FontWeight.w500,
          ),
        ),
      ),
    );
  }
}
