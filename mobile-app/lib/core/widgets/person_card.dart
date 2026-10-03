import 'package:flutter/material.dart';
import 'app_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constants/app_colors.dart';

/// Person Card for Office Bearers, Former Leaders, and Editorial Board
class PersonCard extends StatelessWidget {
  final String name;
  final String designation;
  final String? photoUrl;
  final String? mobile;
  final String? email;
  final String? tenure;
  final String? district;

  const PersonCard({
    super.key,
    required this.name,
    required this.designation,
    this.photoUrl,
    this.mobile,
    this.email,
    this.tenure,
    this.district,
  });

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _sendEmail(String mail) async {
    final uri = Uri.parse('mailto:$mail');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            // Avatar
            ClipRRect(
              borderRadius: BorderRadius.circular(30),
              child: AppNetworkImage(
                imageUrl: photoUrl,
                width: 60,
                height: 60,
                fit: BoxFit.cover,
                placeholder: Container(
                  width: 60,
                  height: 60,
                  color: AppColors.bgLight,
                  child: const Icon(
                    Icons.person,
                    color: AppColors.textLight,
                    size: 30,
                  ),
                ),
                errorWidget: Container(
                  width: 60,
                  height: 60,
                  color: AppColors.primary.withValues(alpha: 0.1),
                  child: const Icon(
                    Icons.person,
                    color: AppColors.primary,
                    size: 30,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 14),

            // Details
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    name,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textDark,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    designation,
                    style: const TextStyle(
                      fontSize: 13,
                      color: AppColors.primary,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  if (district != null && district!.isNotEmpty) ...[
                    const SizedBox(height: 2),
                    Text(
                      district!,
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.textMuted,
                      ),
                    ),
                  ],
                  if (tenure != null && tenure!.isNotEmpty) ...[
                    const SizedBox(height: 2),
                    Text(
                      'Tenure: $tenure',
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.orange,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ],
              ),
            ),

            // Call & Email Actions
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (mobile != null && mobile!.isNotEmpty)
                  IconButton(
                    icon: const Icon(
                      Icons.phone_in_talk_outlined,
                      color: AppColors.green,
                      size: 22,
                    ),
                    tooltip: 'Call $name',
                    onPressed: () => _makeCall(mobile!),
                  ),
                if (email != null && email!.isNotEmpty)
                  IconButton(
                    icon: const Icon(
                      Icons.mail_outline,
                      color: AppColors.indigo,
                      size: 22,
                    ),
                    tooltip: 'Email $name',
                    onPressed: () => _sendEmail(email!),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
