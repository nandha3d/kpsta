import 'package:flutter/material.dart';
import 'app_network_image.dart';
import 'package:url_launcher/url_launcher.dart';

/// BearerCard with responsive, proportionate photo framing and compact, polished typography.
class BearerCard extends StatelessWidget {
  final String name;
  final String designation;
  final String? photoUrl;
  final String? phone;
  final String? year;
  final double? width;
  final double? photoHeight;
  final double aspectRatio;

  const BearerCard({
    super.key,
    required this.name,
    required this.designation,
    this.photoUrl,
    this.phone,
    this.year,
    this.width,
    this.photoHeight,
    this.aspectRatio = 1.0,
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
      margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Proportionate, Responsive Photo Frame
          photoHeight != null
              ? Container(
                  width: width ?? double.infinity,
                  height: photoHeight,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: const Color(0xFF016D77).withValues(alpha: 0.18),
                      width: 1.5,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.06),
                        blurRadius: 8,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(14),
                    child: AppNetworkImage(
                      imageUrl: photoUrl,
                      fit: BoxFit.cover,
                      alignment: Alignment.topCenter,
                      placeholder: Container(
                        color: const Color(0xFFE5F3F4),
                        child: const Center(
                          child: Icon(
                            Icons.person,
                            size: 36,
                            color: Color(0xFF016D77),
                          ),
                        ),
                      ),
                      errorWidget: Container(
                        color: const Color(0xFF0A5863),
                        child: const Center(
                          child: Icon(
                            Icons.person,
                            size: 38,
                            color: Colors.white70,
                          ),
                        ),
                      ),
                    ),
                  ),
                )
              : AspectRatio(
                  aspectRatio: aspectRatio,
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: const Color(0xFF016D77).withValues(alpha: 0.18),
                        width: 1.5,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.06),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(14),
                      child: AppNetworkImage(
                        imageUrl: photoUrl,
                        fit: BoxFit.cover,
                        alignment: Alignment.topCenter,
                        placeholder: Container(
                          color: const Color(0xFFE5F3F4),
                          child: const Center(
                            child: Icon(
                              Icons.person,
                              size: 36,
                              color: Color(0xFF016D77),
                            ),
                          ),
                        ),
                        errorWidget: Container(
                          color: const Color(0xFF0A5863),
                          child: const Center(
                            child: Icon(
                              Icons.person,
                              size: 38,
                              color: Colors.white70,
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
          const SizedBox(height: 6),

          // Leader Name
          Text(
            name,
            style: const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: Color(0xFF0F1E24),
              height: 1.15,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 2),

          // Leader Role
          Text(
            displayRole,
            style: const TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w500,
              color: Color(0xFF556068),
              height: 1.15,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),

          // Phone Call Action
          if (phone != null && phone!.trim().isNotEmpty) ...[
            const SizedBox(height: 5),
            InkWell(
              onTap: () => _makeCall(phone!),
              borderRadius: BorderRadius.circular(14),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFFE5F3F4),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: const Color(0xFF016D77).withValues(alpha: 0.15),
                    width: 1,
                  ),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 15,
                      height: 15,
                      decoration: const BoxDecoration(
                        color: Color(0xFF016D77),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.call,
                        size: 9,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(width: 3),
                    Flexible(
                      child: Text(
                        phone!,
                        style: const TextStyle(
                          fontSize: 9,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF016D77),
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
