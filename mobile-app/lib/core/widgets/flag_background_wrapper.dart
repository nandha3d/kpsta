import 'package:flutter/material.dart';

/// FlagBackgroundWrapper
/// Provides the global stationary, fixed-to-mobile-length very light KPSTA flag background:
/// - Soft saffron orange ambient wash at the top
/// - Clean luminous pearl white across the center
/// - Soft flag green ambient wash at the bottom
/// - 100% fixed to the mobile screen length: scrolling content NEVER moves this background
class FlagBackgroundWrapper extends StatelessWidget {
  final Widget child;

  const FlagBackgroundWrapper({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return Stack(
      fit: StackFit.expand,
      children: [
        // 1. Stationary, fixed-to-mobile-length very light KPSTA tricolor canvas
        const Positioned.fill(
          child: IgnorePointer(
            child: DecoratedBox(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFFFFEFE6), // Very light KPSTA Flag Orange wash at top
                    Color(0xFFFFF8F3), // Soft warm saffron transition
                    Color(0xFFFFFFFF), // Luminous pearl white center
                    Color(0xFFF3FAF1), // Soft mint green transition
                    Color(0xFFEAF7E7), // Very light KPSTA Flag Green wash at bottom
                  ],
                  stops: [0.0, 0.24, 0.50, 0.76, 1.0],
                ),
              ),
            ),
          ),
        ),

        // Soft stationary Flag Orange ambient glow (top-right, matching More screen feel)
        Positioned(
          top: -40,
          right: -40,
          child: IgnorePointer(
            child: Container(
              width: 220,
              height: 220,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    const Color(0xFFEE6A2A).withValues(alpha: 0.10),
                    const Color(0xFFEE6A2A).withValues(alpha: 0.0),
                  ],
                ),
              ),
            ),
          ),
        ),

        // Soft stationary Flag Green ambient glow (bottom-left, matching More screen feel)
        Positioned(
          bottom: 50,
          left: -40,
          child: IgnorePointer(
            child: Container(
              width: 230,
              height: 230,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    const Color(0xFF2E9E14).withValues(alpha: 0.09),
                    const Color(0xFF2E9E14).withValues(alpha: 0.0),
                  ],
                ),
              ),
            ),
          ),
        ),

        // 2. Foreground content (scrolls smoothly over the fixed background)
        child,
      ],
    );
  }
}
