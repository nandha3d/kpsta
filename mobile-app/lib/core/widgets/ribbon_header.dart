import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Reusable RibbonSectionHeader matching the KPSTA webapp design:
/// Teal or Navy slanted badge on the left + horizontal line extending to the right.
class RibbonHeader extends StatelessWidget {
  final String title;
  final Color color;
  final Color textColor;
  final double height;
  final double fontSize;
  final EdgeInsetsGeometry margin;

  const RibbonHeader({
    super.key,
    required this.title,
    this.color = const Color(0xFF016D77),
    this.textColor = Colors.white,
    this.height = 38.0,
    this.fontSize = 15.0,
    this.margin = const EdgeInsets.only(top: 24, bottom: 16),
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: margin,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          // Main Badge Box
          Flexible(
            child: Container(
              height: height,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: color,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(6),
                ),
              ),
              alignment: Alignment.center,
              child: Text(
                title,
                style: TextStyle(
                  color: textColor,
                  fontSize: fontSize,
                  fontWeight: FontWeight.w700,
                  letterSpacing: 0.2,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ),
          // Sloped Tail
          CustomPaint(
            size: Size(height * 0.75, height),
            painter: _RibbonTailPainter(color: color),
          ),
          // Horizontal Rule extending to right
          Expanded(
            child: Container(
              height: 3.5,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}

class _RibbonTailPainter extends CustomPainter {
  final Color color;

  _RibbonTailPainter({required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    // Draw the wedge matching the webapp's diagonal tail
    final path = Path();
    path.moveTo(0, 0);
    // Slight curve into slope
    path.quadraticBezierTo(size.width * 0.25, 0, size.width * 0.45, size.height * 0.35);
    // Slope down to rule line
    path.lineTo(size.width, size.height - 3.5);
    path.lineTo(size.width, size.height);
    path.lineTo(0, size.height);
    path.close();

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant _RibbonTailPainter oldDelegate) =>
      oldDelegate.color != color;
}
