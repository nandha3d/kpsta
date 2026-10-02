import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Accessible StatusBadge displaying color and icon
class StatusBadge extends StatelessWidget {
  final bool isPublished;
  final String? customLabel;

  const StatusBadge({
    super.key,
    required this.isPublished,
    this.customLabel,
  });

  @override
  Widget build(BuildContext context) {
    final bg = isPublished ? AppColors.statusPublishedBg : AppColors.statusDraftBg;
    final textCol =
        isPublished ? AppColors.statusPublishedText : AppColors.statusDraftText;
    final icon = isPublished ? Icons.check_circle_outline : Icons.pending_outlined;
    final label = customLabel ?? (isPublished ? 'Published' : 'Draft');

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: textCol.withOpacity(0.3), width: 0.8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: textCol),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: textCol,
            ),
          ),
        ],
      ),
    );
  }
}
