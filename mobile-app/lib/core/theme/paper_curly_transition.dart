import 'dart:math' as math;
import 'package:flutter/material.dart';

/// Paper Curly Fold Direction
enum PaperCurlDirection {
  /// Swiping left: Right edge curls and turns over to finish on the left side
  leftSwipe,

  /// Swiping right: Left edge curls and turns over to finish on the right side
  rightSwipe,
}

/// PaperCurlyTransition
/// Implements a realistic paper curly fold / book page curl:
/// - On left swipe: Right edge curls, rolls over, and finishes on the left side.
/// - On right swipe: Left edge curls, rolls over, and finishes on the right side.
/// Features 3D perspective, cylinder curl curvature, rolling specular highlight ridge,
/// and deep realistic page shadow.
class PaperCurlyTransition extends StatelessWidget {
  final Animation<double> animation;
  final Animation<double> secondaryAnimation;
  final Widget child;
  final PaperCurlDirection direction;

  const PaperCurlyTransition({
    super.key,
    required this.animation,
    required this.secondaryAnimation,
    required this.child,
    this.direction = PaperCurlDirection.leftSwipe,
  });

  @override
  Widget build(BuildContext context) {
    final curvedAnimation = CurvedAnimation(
      parent: animation,
      curve: Curves.easeOutCubic,
      reverseCurve: Curves.easeInCubic,
    );

    final curvedSecondary = CurvedAnimation(
      parent: secondaryAnimation,
      curve: Curves.easeOutCubic,
      reverseCurve: Curves.easeInCubic,
    );

    return AnimatedBuilder(
      animation: Listenable.merge([curvedAnimation, curvedSecondary]),
      builder: (context, _) {
        final enterValue = curvedAnimation.value;
        final exitValue = curvedSecondary.value;

        // When outgoing page curls away (secondary exit)
        if (exitValue > 0.001) {
          return _buildCurlEffect(
            child: child,
            progress: exitValue,
            isEntering: false,
            direction: direction,
          );
        }

        // Incoming page entering underneath or being revealed
        if (enterValue < 0.999) {
          return _buildCurlEffect(
            child: child,
            progress: enterValue,
            isEntering: true,
            direction: direction,
          );
        }

        return child;
      },
    );
  }

  Widget _buildCurlEffect({
    required Widget child,
    required double progress,
    required bool isEntering,
    required PaperCurlDirection direction,
  }) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final width = constraints.maxWidth;

        if (direction == PaperCurlDirection.leftSwipe) {
          // ==========================================
          // LEFT SWIPE (FORWARD)
          // Right edge curls and rolls toward the left
          // ==========================================
          if (isEntering) {
            // Incoming page underneath: revealed as the curling page rolls left
            final reveal = progress.clamp(0.0, 1.0);
            final scale = 0.96 + (0.04 * reveal);

            return Transform.scale(
              scale: scale,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  child,
                  // Diminishing shadow cast onto incoming page by the curling sheet above
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                            colors: [
                              Colors.black.withValues(
                                  alpha: (1.0 - reveal) * 0.45),
                              Colors.black.withValues(
                                  alpha: (1.0 - reveal) * 0.18),
                              Colors.transparent,
                            ],
                            stops: const [0.0, 0.35, 0.85],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          } else {
            // Outgoing page: right edge curls, rotates, and rolls to finish on the left
            final curl = progress.clamp(0.0, 1.0);
            final angle = -curl * 1.15 * (math.pi / 2); // rolls up to ~103 deg
            final xOffset = -curl * width * 0.40;

            final curlMatrix = Matrix4.translationValues(xOffset, 0.0, 0.0)
              ..setEntry(3, 2, 0.0014)
              ..rotateY(angle);

            final highlightPeak = (1.0 - (curl * 0.75)).clamp(0.0, 1.0);
            final highlightStart = (highlightPeak - 0.2).clamp(0.0, 1.0);
            final highlightEnd = (highlightPeak + 0.15).clamp(0.0, 1.0);

            return Transform(
              alignment: Alignment.centerLeft,
              transform: curlMatrix,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  child,
                  // Dynamic Cylinder Highlight Ridge & Crease Shadows
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                            colors: [
                              Colors.black.withValues(alpha: curl * 0.40),
                              Colors.white.withValues(
                                  alpha: (curl * 0.35).clamp(0.0, 0.35)),
                              Colors.black.withValues(alpha: curl * 0.55),
                              Colors.transparent,
                            ],
                            stops: [
                              0.0,
                              highlightStart,
                              highlightPeak,
                              highlightEnd,
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                  // Underside paper drop shadow at curling edge
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerRight,
                            end: Alignment.centerLeft,
                            colors: [
                              Colors.black.withValues(alpha: curl * 0.50),
                              Colors.transparent,
                            ],
                            stops: const [0.0, 0.25],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          }
        } else {
          // ==========================================
          // RIGHT SWIPE (BACKWARD)
          // Left edge curls and rolls toward the right
          // ==========================================
          if (isEntering) {
            // Incoming page underneath: revealed as the curling page rolls right
            final reveal = progress.clamp(0.0, 1.0);
            final scale = 0.96 + (0.04 * reveal);

            return Transform.scale(
              scale: scale,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  child,
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerRight,
                            end: Alignment.centerLeft,
                            colors: [
                              Colors.black.withValues(
                                  alpha: (1.0 - reveal) * 0.45),
                              Colors.black.withValues(
                                  alpha: (1.0 - reveal) * 0.18),
                              Colors.transparent,
                            ],
                            stops: const [0.0, 0.35, 0.85],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          } else {
            // Outgoing page: left edge curls, rotates, and rolls to finish on the right
            final curl = progress.clamp(0.0, 1.0);
            final angle = curl * 1.15 * (math.pi / 2);
            final xOffset = curl * width * 0.40;

            final curlMatrix = Matrix4.translationValues(xOffset, 0.0, 0.0)
              ..setEntry(3, 2, 0.0014)
              ..rotateY(angle);

            final highlightPeak = (curl * 0.75).clamp(0.0, 1.0);
            final highlightStart = (highlightPeak - 0.15).clamp(0.0, 1.0);
            final highlightEnd = (highlightPeak + 0.2).clamp(0.0, 1.0);

            return Transform(
              alignment: Alignment.centerRight,
              transform: curlMatrix,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  child,
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerRight,
                            end: Alignment.centerLeft,
                            colors: [
                              Colors.black.withValues(alpha: curl * 0.40),
                              Colors.white.withValues(
                                  alpha: (curl * 0.35).clamp(0.0, 0.35)),
                              Colors.black.withValues(alpha: curl * 0.55),
                              Colors.transparent,
                            ],
                            stops: [
                              0.0,
                              highlightStart,
                              highlightPeak,
                              highlightEnd,
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                  Positioned.fill(
                    child: IgnorePointer(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                            colors: [
                              Colors.black.withValues(alpha: curl * 0.50),
                              Colors.transparent,
                            ],
                            stops: const [0.0, 0.25],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          }
        }
      },
    );
  }
}

/// PaperCurlyPageTransitionsBuilder
/// Registered in PageTransitionsTheme for route navigation
class PaperCurlyPageTransitionsBuilder extends PageTransitionsBuilder {
  const PaperCurlyPageTransitionsBuilder();

  @override
  Widget buildTransitions<T>(
    PageRoute<T> route,
    BuildContext context,
    Animation<double> animation,
    Animation<double> secondaryAnimation,
    Widget child,
  ) {
    return PaperCurlyTransition(
      animation: animation,
      secondaryAnimation: secondaryAnimation,
      direction: PaperCurlDirection.leftSwipe,
      child: child,
    );
  }
}
