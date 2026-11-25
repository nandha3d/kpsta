import 'dart:math' as math;
import 'package:flutter/material.dart';

/// Way2News 3D Paper-Folding Page Transition
/// Delivers realistic book-fold / paper-flip 3D perspective animation on page navigation.
class Way2NewsFoldTransition extends StatelessWidget {
  final Animation<double> animation;
  final Animation<double> secondaryAnimation;
  final Widget child;

  const Way2NewsFoldTransition({
    super.key,
    required this.animation,
    required this.secondaryAnimation,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    // Primary entrance: curved animation from 0.0 to 1.0
    final curvedAnimation = CurvedAnimation(
      parent: animation,
      curve: Curves.easeOutCubic,
      reverseCurve: Curves.easeInCubic,
    );

    // Secondary exit: when another page is pushed on top
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

        // When a new page is pushed on top of this one (secondary exit)
        if (exitValue > 0.001) {
          // Exit fold: Folds backward toward left spine with perspective
          final exitAngle = -exitValue * 0.55 * (math.pi / 2); // max ~50 degrees
          final exitMatrix = Matrix4.identity()
            ..setEntry(3, 2, 0.0012)
            ..rotateY(exitAngle);

          return Transform(
            alignment: Alignment.centerLeft,
            transform: exitMatrix,
            child: Stack(
              children: [
                child,
                Positioned.fill(
                  child: IgnorePointer(
                    child: Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.centerLeft,
                          end: Alignment.centerRight,
                          colors: [
                            Colors.black.withValues(alpha: exitValue * 0.4),
                            Colors.black.withValues(alpha: exitValue * 0.15),
                            Colors.transparent,
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        }

        // Entering page (primary entrance)
        if (enterValue < 0.999) {
          // Entrance fold: Unfolds from right edge toward flat
          final enterAngle = (1.0 - enterValue) * 0.75 * (math.pi / 2); // starts at ~68 degrees
          final enterMatrix = Matrix4.identity()
            ..setEntry(3, 2, 0.0012)
            ..rotateY(enterAngle);

          return Transform(
            alignment: Alignment.centerRight,
            transform: enterMatrix,
            child: Stack(
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
                            Colors.black.withValues(alpha: (1.0 - enterValue) * 0.35),
                            Colors.black.withValues(alpha: (1.0 - enterValue) * 0.1),
                            Colors.transparent,
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        }

        return child;
      },
    );
  }
}

/// Custom PageTransitionsBuilder applying Way2News 3D folding animation across routes
class Way2NewsFoldPageTransitionsBuilder extends PageTransitionsBuilder {
  const Way2NewsFoldPageTransitionsBuilder();

  @override
  Widget buildTransitions<T>(
    PageRoute<T> route,
    BuildContext context,
    Animation<double> animation,
    Animation<double> secondaryAnimation,
    Widget child,
  ) {
    return Way2NewsFoldTransition(
      animation: animation,
      secondaryAnimation: secondaryAnimation,
      child: child,
    );
  }
}

