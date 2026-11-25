import 'package:flutter/material.dart';

/// PillSectionHeader / Modern Pill-Shaped Title Bar
/// Replaces the old truncated ribbon header with a modern center-aligned pill-shaped title bar.
/// Ensures titles are ALWAYS center-aligned and NEVER hidden or truncated with ellipsis.
class RibbonHeader extends StatelessWidget {
  final String title;
  final Color color;
  final Color textColor;
  final double height;
  final double fontSize;
  final EdgeInsetsGeometry margin;
  final IconData? icon;
  final bool isCentered;

  const RibbonHeader({
    super.key,
    required this.title,
    this.color = const Color(0xFF016D77),
    this.textColor = Colors.white,
    this.height = 36.0,
    this.fontSize = 13.5,
    this.margin = const EdgeInsets.only(top: 8, bottom: 6),
    this.icon,
    this.isCentered = true, // Center-aligned by default
  });

  @override
  Widget build(BuildContext context) {
    final pill = Container(
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 7.5),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            color,
            const Color(0xFF0D4F57),
          ],
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: color.withValues(alpha: 0.25),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
        border: Border.all(
          color: const Color(0xFFEE6A2A).withValues(alpha: 0.50), // KPSTA Flag Orange border highlight
          width: 1.2,
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Left KPSTA Flag Orange Indicator Dot
          Container(
            width: 7,
            height: 7,
            decoration: const BoxDecoration(
              color: Color(0xFFEE6A2A), // Flag Orange
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 9),
          Flexible(
            child: Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                color: textColor,
                fontSize: fontSize,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.3,
                height: 1.2,
              ),
              softWrap: true,
              maxLines: 2, // Full title is always displayed, never cut off!
            ),
          ),
          const SizedBox(width: 9),
          // Right KPSTA Flag Green Indicator Dot for symmetric balance
          Container(
            width: 7,
            height: 7,
            decoration: const BoxDecoration(
              color: Color(0xFF2E9E14), // Flag Green
              shape: BoxShape.circle,
            ),
          ),
        ],
      ),
    );

    return Padding(
      padding: margin,
      child: isCentered ? Center(child: pill) : pill,
    );
  }
}
