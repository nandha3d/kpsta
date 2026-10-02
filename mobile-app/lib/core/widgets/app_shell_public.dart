import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../constants/app_colors.dart';

/// AppShellPublic with bottom navigation and drawer for quick access to all public modules
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
    if (location.startsWith('/news')) return 1;
    if (location.startsWith('/circulars')) return 2;
    if (location.startsWith('/downloads')) return 3;
    return 4; // More
  }

  void _onItemTapped(int index, BuildContext context) {
    switch (index) {
      case 0:
        context.go('/home');
        break;
      case 1:
        context.go('/news');
        break;
      case 2:
        context.go('/circulars');
        break;
      case 3:
        context.go('/downloads');
        break;
      case 4:
        context.go('/more');
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    final selectedIndex = _calculateSelectedIndex(context);

    return Scaffold(
      body: widget.child,
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: selectedIndex,
        onTap: (idx) => _onItemTapped(idx, context),
        selectedItemColor: AppColors.primary,
        unselectedItemColor: AppColors.textMuted,
        selectedLabelStyle:
            const TextStyle(fontWeight: FontWeight.w600, fontSize: 12),
        unselectedLabelStyle:
            const TextStyle(fontWeight: FontWeight.normal, fontSize: 12),
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_outlined),
            activeIcon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.newspaper_outlined),
            activeIcon: Icon(Icons.newspaper),
            label: 'News',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.description_outlined),
            activeIcon: Icon(Icons.description),
            label: 'Circulars',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.download_outlined),
            activeIcon: Icon(Icons.download),
            label: 'Downloads',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.grid_view_outlined),
            activeIcon: Icon(Icons.grid_view),
            label: 'More',
          ),
        ],
      ),
    );
  }
}
