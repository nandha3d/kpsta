import 'package:flutter/material.dart';
import 'app_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constants/app_colors.dart';

/// BearerCard matching the webapp's asymmetric photo frame, bold name, role, and phone chip.
class BearerCard extends StatelessWidget {
  final String name;
  final String designation;
  final String? photoUrl;
  final String? phone;
  final String? year;
  final double width;
  final double photoHeight;

  const BearerCard({
    super.key,
    required this.name,
    required this.designation,
    this.photoUrl,
    this.phone,
    this.year,
    this.width = 160.0,
    this.photoHeight = 165.0,
  });

  Future<void> _makeCall(String phoneNumber) async {
    final cleanPhone = phoneNumber.replaceAll(RegExp(r'[^0-9+]'), '');
    final uri = Uri.parse('tel:$cleanPhone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    final displayRole = (year != null && year!.isNotEmpty)
        ? '$designation ($year)'
        : designation;

    return Container(
      width: width,
      margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Asymmetric Photo Frame
          Container(
            width: width,
            height: photoHeight,
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(44),
                topRight: Radius.circular(12),
                bottomLeft: Radius.circular(12),
                bottomRight: Radius.circular(12),
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.08),
                  blurRadius: 10,
                  offset: const Offset(2, 4),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(40),
                topRight: Radius.circular(8),
                bottomLeft: Radius.circular(8),
                bottomRight: Radius.circular(8),
              ),
              child: AppNetworkImage(
                imageUrl: photoUrl,
                fit: BoxFit.cover,
                alignment: Alignment.topCenter,
                placeholder: Container(
                  color: const Color(0xFF1E2846).withValues(alpha: 0.08),
                  child: const Center(
                    child: Icon(
                      Icons.person,
                      size: 48,
                      color: Color(0xFF64748B),
                    ),
                  ),
                ),
                errorWidget: Container(
                  color: const Color(0xFF1E2846),
                  child: const Center(
                    child: Icon(
                      Icons.person,
                      size: 52,
                      color: Colors.white38,
                    ),
                  ),
                ),
              ),
            ),
          ),
          const SizedBox(height: 8),

          // Leader Name
          Text(
            name,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w700,
              color: Color(0xFF111111),
              height: 1.2,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 3),

          // Leader Role
          Text(
            displayRole,
            style: const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w400,
              color: Color(0xFF555555),
              height: 1.2,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),

          // Phone Call Action
          if (phone != null && phone!.trim().isNotEmpty) ...[
            const SizedBox(height: 6),
            InkWell(
              onTap: () => _makeCall(phone!),
              borderRadius: BorderRadius.circular(16),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 20,
                      height: 20,
                      decoration: const BoxDecoration(
                        color: Color(0xFF0D7A72),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.call,
                        size: 12,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(width: 4),
                    Flexible(
                      child: Text(
                        phone!,
                        style: const TextStyle(
                          fontSize: 10.5,
                          fontWeight: FontWeight.w500,
                          color: Color(0xFF111111),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
