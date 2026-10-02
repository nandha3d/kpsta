import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Top bar with KPSTA branding, back button or menu, and quick actions
class AppTopBar extends StatelessWidget implements PreferredSizeWidget {
  final String title;
  final List<Widget>? actions;
  final bool showBackButton;
  final Widget? leading;
  final PreferredSizeWidget? bottom;

  const AppTopBar({
    super.key,
    required this.title,
    this.actions,
    this.showBackButton = true,
    this.leading,
    this.bottom,
  });

  @override
  Size get preferredSize =>
      Size.fromHeight(kToolbarHeight + (bottom?.preferredSize.height ?? 0.0));

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: AppColors.primaryDark,
      foregroundColor: AppColors.white,
      elevation: 1,
      leading: leading ??
          (showBackButton && Navigator.of(context).canPop()
              ? IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new, size: 20),
                  onPressed: () => Navigator.of(context).pop(),
                )
              : null),
      title: Text(
        title,
        style: const TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.w600,
          color: AppColors.white,
        ),
      ),
      actions: actions,
      bottom: bottom,
    );
  }
}
