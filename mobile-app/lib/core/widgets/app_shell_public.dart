import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'magic_curved_nav_bar.dart';

/// AppShellPublic with a floating KPSTA theme + flag-colored navigation bar,
/// exactly 5 core menus, and seamless paper curly fold page transitions on swipe.
class AppShellPublic extends StatefulWidget {
  final Widget child;

  const AppShellPublic({super.key, required this.child});

  @override
  State<AppShellPublic> createState() => _AppShellPublicState();
}

class _AppShellPublicState extends State<AppShellPublic> {
  int _calculateSelectedIndex(BuildContext context) {
    final location = GoRouterState.of(context).uri.toString();
    if (location == '/' || location.startsWith('/home')) return 0;
    if (location.startsWith('/organization') ||
        location.startsWith('/districts') ||
        location.startsWith('/former-leaders')) {
      return 1;
    }
    if (location.startsWith('/downloads')) return 2;
    if (location.startsWith('/circulars')) return 3;
    return 4; // More
  }

  void _onItemTapped(int index, BuildContext context) {
    final currentIndex = _calculateSelectedIndex(context);

    // If re-tapping the active tab, simply do nothing (do not pop up unwanted sub-menus)
    if (index == currentIndex) {
      return;
    }

    HapticFeedback.selectionClick();

    switch (index) {
      case 0:
        context.go('/home');
        break;
      case 1:
        context.go('/organization');
        break;
      case 2:
        context.go('/downloads');
        break;
      case 3:
        context.go('/circulars');
        break;
      case 4:
        context.go('/more');
        break;
    }
  }

  void _handleHorizontalSwipe(DragEndDetails details, BuildContext context) {
    final velocity = details.primaryVelocity;
    if (velocity == null || velocity.abs() < 280) return;

    final currentIndex = _calculateSelectedIndex(context);

    if (velocity < -280 && currentIndex < 4) {
      _onItemTapped(currentIndex + 1, context);
    } else if (velocity > 280 && currentIndex > 0) {
      _onItemTapped(currentIndex - 1, context);
    }
  }

  @override
  Widget build(BuildContext context) {
    final selectedIndex = _calculateSelectedIndex(context);
    final location = GoRouterState.of(context).uri.toString();

    return Scaffold(
      extendBody: true,
      backgroundColor: Colors.transparent,
      body: KeyedSubtree(
        key: ValueKey<String>(location),
        child: widget.child,
      ),
      bottomNavigationBar: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(12, 0, 12, 10),
          child: RepaintBoundary(
            child: MagicCurvedNavBar(
              selectedIndex: selectedIndex,
              onTabSelected: (idx) => _onItemTapped(idx, context),
              items: const [
                MagicNavItem(
                  title: 'Home',
                  icon: Icons.home_outlined,
                  activeIcon: Icons.home_rounded,
                ),
                MagicNavItem(
                  title: 'Organization',
                  icon: Icons.groups_outlined,
                  activeIcon: Icons.groups_rounded,
                ),
                MagicNavItem(
                  title: 'Downloads',
                  icon: Icons.download_outlined,
                  activeIcon: Icons.download_rounded,
                ),
                MagicNavItem(
                  title: 'Orders',
                  icon: Icons.description_outlined,
                  activeIcon: Icons.description_rounded,
                ),
                MagicNavItem(
                  title: 'More',
                  icon: Icons.grid_view_outlined,
                  activeIcon: Icons.grid_view_rounded,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
