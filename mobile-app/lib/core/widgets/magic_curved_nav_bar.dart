import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

/// MagicCurvedNavBar
/// Animated curved cutout bottom navigation bar inspired by DS CODE's Figma design:
/// - Floating circular active indicator button with KPSTA flag orange glow.
/// - Dynamic curved cutout notch that fluidly glides across the top edge.
/// - Top tricolor flag hairline (Orange -> White -> Green).
/// - Clean inactive icons that elevate smoothly into the floating circle on selection.
class MagicCurvedNavBar extends StatefulWidget {
  final int selectedIndex;
  final ValueChanged<int> onTabSelected;
  final List<MagicNavItem> items;

  const MagicCurvedNavBar({
    super.key,
    required this.selectedIndex,
    required this.onTabSelected,
    required this.items,
  });

  @override
  State<MagicCurvedNavBar> createState() => _MagicCurvedNavBarState();
}

class _MagicCurvedNavBarState extends State<MagicCurvedNavBar>
    with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _notchAnim;
  double _currentNotchRatio = 0.1; // (index + 0.5) / items.length

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 200),
    );

    final initialRatio = _getRatio(widget.selectedIndex);
    _currentNotchRatio = initialRatio;

    _notchAnim = Tween<double>(begin: initialRatio, end: initialRatio)
        .animate(CurvedAnimation(
      parent: _animController,
      curve: Curves.easeInOutCubic,
    ));
  }

  double _getRatio(int index) {
    if (widget.items.isEmpty) return 0.5;
    return (index + 0.5) / widget.items.length;
  }

  @override
  void didUpdateWidget(covariant MagicCurvedNavBar oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.selectedIndex != widget.selectedIndex) {
      final newRatio = _getRatio(widget.selectedIndex);
      _notchAnim = Tween<double>(
        begin: _currentNotchRatio,
        end: newRatio,
      ).animate(CurvedAnimation(
        parent: _animController,
        curve: Curves.easeInOutCubic,
      ));

      _animController.forward(from: 0.0);
    }
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final totalWidth = constraints.maxWidth;
        const barHeight = 62.0;
        const barTop = 20.0;
        const circleDiameter = 46.0;
        const circleRadius = circleDiameter / 2;
        const horizontalInset = 12.0;

        return AnimatedBuilder(
          animation: _notchAnim,
          builder: (context, _) {
            _currentNotchRatio = _notchAnim.value;
            // Center the notch inside the padded tab area for symmetrical clearance
            final usableWidth = totalWidth - (2 * horizontalInset);
            final rawNotchX = horizontalInset + (_currentNotchRatio * usableWidth);

            // Compute safe limits so the cradle notch never collides with outer corner fillets
            const double cornerRadius = 14.0;
            const double notchR = 28.0;
            const double shoulderR = 8.0;
            final double dist = notchR + shoulderR;
            final double dY = shoulderR - 2.0;
            final double dX = math.sqrt(math.max(0.0, dist * dist - dY * dY));
            final double minSafeX = cornerRadius + dX + 2.0;
            final double maxSafeX = totalWidth - cornerRadius - dX - 2.0;
            final double notchX = rawNotchX.clamp(minSafeX, maxSafeX);

            return SizedBox(
              width: totalWidth,
              height: barHeight + barTop, // 82px height: top room for elevated floating circle
              child: Stack(
                clipBehavior: Clip.none,
                children: [
                  // 1. Curved Notch Bar Background as it was before
                  Positioned(
                    left: 0,
                    right: 0,
                    bottom: 0,
                    top: barTop,
                    child: CustomPaint(
                      painter: _CurvedNotchPainter(
                        notchX: notchX,
                        notchRadius: notchR,
                        notchDepth: 29.0,
                        barColor: const Color(0xFF092025),
                        barColorEnd: const Color(0xFF014D54),
                      ),
                    ),
                  ),

                  // 2. Sliding Elevated Floating Circle Button (KPSTA Flag Orange, zero stroke, floating)
                  Positioned(
                    left: notchX - circleRadius,
                    top: 0, // Elevated in mid-air with 5.5px air gap inside the round notch
                    child: Container(
                      width: circleDiameter,
                      height: circleDiameter,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        // KPSTA Flag Orange Gradient
                        gradient: const LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [
                            Color(0xFFFF7E36), // Vibrant bright KPSTA Flag Orange
                            Color(0xFFEE6A2A), // Official KPSTA Brand Orange
                            Color(0xFFDF5717), // Warm shadow orange
                          ],
                        ),
                        // Clean floating look: ZERO black stroke
                        boxShadow: [
                          // 1. Warm glowing orange aura (gives the magical floating luminescence)
                          BoxShadow(
                            color: const Color(0xFFEE6A2A).withValues(alpha: 0.55),
                            blurRadius: 16,
                            spreadRadius: 1.5,
                            offset: const Offset(0, 5),
                          ),
                          // 2. Soft ambient drop shadow underneath (elevates off the cradle)
                          BoxShadow(
                            color: const Color(0xFF00151A).withValues(alpha: 0.35),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Center(
                        child: AnimatedSwitcher(
                          duration: const Duration(milliseconds: 200),
                          transitionBuilder: (child, anim) => ScaleTransition(
                            scale: anim,
                            child: child,
                          ),
                          child: Icon(
                            widget.items[widget.selectedIndex].activeIcon,
                            key: ValueKey<int>(widget.selectedIndex),
                            size: 23,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                  ),

                  // 3. Tab Items Row (Touch targets and text labels as they were before)
                  Positioned(
                    left: horizontalInset,
                    right: horizontalInset,
                    bottom: 0,
                    height: barHeight,
                    child: Row(
                      children: List.generate(widget.items.length, (idx) {
                        final isSelected = widget.selectedIndex == idx;
                        final item = widget.items[idx];

                        return Expanded(
                          child: InkWell(
                            onTap: () {
                              HapticFeedback.lightImpact();
                              widget.onTabSelected(idx);
                            },
                            splashColor: Colors.transparent,
                            highlightColor: Colors.transparent,
                            child: Center(
                              child: AnimatedContainer(
                                duration: const Duration(milliseconds: 220),
                                curve: Curves.easeOutCubic,
                                padding: const EdgeInsets.only(top: 8),
                                child: isSelected
                                    ? Column(
                                        mainAxisAlignment: MainAxisAlignment.end,
                                        children: [
                                          const SizedBox(height: 18),
                                          Text(
                                            item.title,
                                            style: const TextStyle(
                                              fontSize: 10.5,
                                              fontWeight: FontWeight.w800,
                                              color: Colors.white,
                                              letterSpacing: 0.2,
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                          const SizedBox(height: 3),
                                          // Flag Green Active Dot Indicator
                                          Container(
                                            width: 4,
                                            height: 4,
                                            decoration: const BoxDecoration(
                                              color: Color(0xFF2E9E14),
                                              shape: BoxShape.circle,
                                            ),
                                          ),
                                          const SizedBox(height: 6),
                                        ],
                                      )
                                    : Column(
                                        mainAxisSize: MainAxisSize.min,
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          Icon(
                                            item.icon,
                                            size: 21,
                                            color: const Color(0xFF8CA5AC),
                                          ),
                                          const SizedBox(height: 3),
                                          Text(
                                            item.title,
                                            style: const TextStyle(
                                              fontSize: 9.5,
                                              fontWeight: FontWeight.w600,
                                              color: Color(0xFF8CA5AC),
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ],
                                      ),
                              ),
                            ),
                          ),
                        );
                      }),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }
}

/// Custom Painter for the Curved Cutout Notch along the top edge of the navigation bar
class _CurvedNotchPainter extends CustomPainter {
  final double notchX;
  final double notchRadius;
  final double notchDepth;
  final Color barColor;
  final Color barColorEnd;

  _CurvedNotchPainter({
    required this.notchX,
    required this.notchRadius,
    required this.notchDepth,
    required this.barColor,
    required this.barColorEnd,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final w = size.width;
    final h = size.height;
    final double R = notchRadius;
    const double sR = 8.0; // shoulder fillet radius
    const double centerY = 2.0; // notch circle center y in painter coordinates

    const cornerRadius = 14.0;

    // Mathematical tangent calculation between circular arc (radius R) and shoulder fillet (radius sR)
    final double dY = sR - centerY;
    final double dist = R + sR;
    final double dX = math.sqrt(math.max(0.0, dist * dist - dY * dY));

    // Clamp safe notch center inside canvas to strictly prevent backwards arc inversion
    final double safeX = notchX.clamp(cornerRadius + dX + 1.0, w - cornerRadius - dX - 1.0);
    final double leftStart = safeX - dX;
    final double rightEnd = safeX + dX;

    final double normX = dX / dist;
    final double normY = dY / dist;

    // Left and right tangent points on the circular notch
    final double tLX = safeX - R * normX;
    final double tLY = centerY + R * normY;
    final double tRX = safeX + R * normX;
    final double tRY = tLY;

    final path = Path();
    path.moveTo(0, cornerRadius);
    path.quadraticBezierTo(0, 0, cornerRadius, 0);

    path.lineTo(leftStart, 0);

    // 1. Left shoulder fillet into circle
    path.arcToPoint(
      Offset(tLX, tLY),
      radius: const Radius.circular(sR),
      clockwise: true,
    );

    // 2. THE PROPER ROUND NOTCH: Exact circular arc sweeping smoothly around the floating button
    path.arcToPoint(
      Offset(tRX, tRY),
      radius: Radius.circular(R),
      clockwise: false,
    );

    // 3. Right shoulder fillet out of circle
    path.arcToPoint(
      Offset(rightEnd, 0),
      radius: const Radius.circular(sR),
      clockwise: true,
    );

    // Right straight top edge to top-right corner
    path.lineTo(w - cornerRadius, 0);
    path.quadraticBezierTo(w, 0, w, cornerRadius);

    // Right edge, bottom-right corner
    path.lineTo(w, h - cornerRadius);
    path.quadraticBezierTo(w, h, w - cornerRadius, h);

    // Bottom edge, bottom-left corner
    path.lineTo(cornerRadius, h);
    path.quadraticBezierTo(0, h, 0, h - cornerRadius);
    path.close();

    // Fill paint with deep KPSTA Flag Green gradient
    final fillPaint = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [barColor, barColorEnd],
      ).createShader(Rect.fromLTWH(0, 0, w, h));

    // Outer elevation drop shadow as it was before
    canvas.drawShadow(
      path,
      const Color(0xFF00151A).withValues(alpha: 0.45),
      14.0,
      true,
    );

    canvas.drawPath(path, fillPaint);

    // Top Tricolor KPSTA Flag Line Highlight (follows the exact circular arc cradle)
    final topBorderPaint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2.2
      ..shader = const LinearGradient(
        colors: [
          Color(0xFFEE6A2A), // KPSTA Flag Orange
          Color(0xFFFFFFFF), // KPSTA Flag White
          Color(0xFF2E9E14), // KPSTA Flag Green
        ],
        stops: [0.0, 0.5, 1.0],
      ).createShader(Rect.fromLTWH(0, 0, w, h));

    final topPath = Path();
    topPath.moveTo(cornerRadius, 0);
    topPath.lineTo(leftStart, 0);
    topPath.arcToPoint(
      Offset(tLX, tLY),
      radius: const Radius.circular(sR),
      clockwise: true,
    );
    topPath.arcToPoint(
      Offset(tRX, tRY),
      radius: Radius.circular(R),
      clockwise: false,
    );
    topPath.arcToPoint(
      Offset(rightEnd, 0),
      radius: const Radius.circular(sR),
      clockwise: true,
    );
    topPath.lineTo(w - cornerRadius, 0);

    canvas.drawPath(topPath, topBorderPaint);
  }

  @override
  bool shouldRepaint(covariant _CurvedNotchPainter oldDelegate) {
    return oldDelegate.notchX != notchX || oldDelegate.barColor != barColor;
  }
}

class MagicNavItem {
  final String title;
  final IconData icon;
  final IconData activeIcon;

  const MagicNavItem({
    required this.title,
    required this.icon,
    required this.activeIcon,
  });
}
