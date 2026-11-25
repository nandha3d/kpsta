import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Top bar with KPSTA branding, back button or menu, flag badge, and tricolor accent
class AppTopBar extends StatelessWidget implements PreferredSizeWidget {
  final String title;
  final List<Widget>? actions;
  final bool showBackButton;
  final Widget? leading;
  final PreferredSizeWidget? bottom;
  final bool showFlag;

  const AppTopBar({
    super.key,
    required this.title,
    this.actions,
    this.showBackButton = true,
    this.leading,
    this.bottom,
    this.showFlag = true,
  });

  @override
  Size get preferredSize =>
      Size.fromHeight(kToolbarHeight + (bottom?.preferredSize.height ?? 0.0));

  @override
  Widget build(BuildContext context) {
    final canPop = Navigator.of(context).canPop();

    return AppBar(
      backgroundColor: AppColors.primaryDark,
      foregroundColor: AppColors.white,
      elevation: 1,
      centerTitle: true,
      leading: leading ??
          (showBackButton && canPop
              ? IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new, size: 20),
                  onPressed: () => Navigator.of(context).pop(),
                )
              : (showFlag && (actions == null || actions!.isEmpty)
                  ? const SizedBox(width: 54) // Balances right flag for title centering
                  : null)),
      title: Text(
        title,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        textAlign: TextAlign.center,
        style: const TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.w700,
          letterSpacing: 0.2,
          color: AppColors.white,
        ),
      ),
      actions: [
        ...?actions,
        if (showFlag)
          Padding(
            padding: const EdgeInsets.only(right: 12, left: 4),
            child: Center(
              child: Image.asset(
                'assets/images/flag.png',
                width: 38,
                height: 26,
                fit: BoxFit.contain,
                errorBuilder: (_, _, _) => const SizedBox.shrink(),
              ),
            ),
          ),
      ],
      flexibleSpace: Align(
        alignment: Alignment.bottomCenter,
        child: Container(
          height: 2.5,
          width: double.infinity,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Color(0xFFEE6A2A),
                Color(0xFFFFA26B),
                Colors.white,
                Color(0xFF76D15E),
                Color(0xFF2E9E14),
              ],
              stops: [0.0, 0.25, 0.5, 0.75, 1.0],
            ),
          ),
        ),
      ),
      bottom: bottom,
    );
  }
}
