import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

/// Descriptor for a single tab item in [ModernPillTabBar].
class ModernPillTab {
  final String label;
  final IconData? icon;

  const ModernPillTab({
    required this.label,
    this.icon,
  });
}

/// Modern floating pill tab selector replacing traditional underlined Material TabBars.
/// Syncs bidirectionally with [TabController] and smoothly auto-centers the active pill.
class ModernPillTabBar extends StatefulWidget implements PreferredSizeWidget {
  final TabController controller;
  final List<ModernPillTab> tabs;

  const ModernPillTabBar({
    super.key,
    required this.controller,
    required this.tabs,
  });

  @override
  Size get preferredSize => const Size.fromHeight(54);

  @override
  State<ModernPillTabBar> createState() => _ModernPillTabBarState();
}

class _ModernPillTabBarState extends State<ModernPillTabBar> {
  late int _activeIndex;
  late List<GlobalKey> _tabKeys;

  @override
  void initState() {
    super.initState();
    _activeIndex = widget.controller.index;
    _tabKeys = List.generate(widget.tabs.length, (_) => GlobalKey());
    widget.controller.addListener(_handleControllerChange);
    WidgetsBinding.instance.addPostFrameCallback((_) => _scrollToActive(animate: false));
  }

  @override
  void didUpdateWidget(covariant ModernPillTabBar oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.controller != widget.controller) {
      oldWidget.controller.removeListener(_handleControllerChange);
      widget.controller.addListener(_handleControllerChange);
      _activeIndex = widget.controller.index;
    }
    if (oldWidget.tabs.length != widget.tabs.length) {
      _tabKeys = List.generate(widget.tabs.length, (_) => GlobalKey());
    }
  }

  @override
  void dispose() {
    widget.controller.removeListener(_handleControllerChange);
    super.dispose();
  }

  void _handleControllerChange() {
    if (!mounted) return;
    final nextIndex = widget.controller.index;
    if (nextIndex != _activeIndex) {
      setState(() => _activeIndex = nextIndex);
      _scrollToActive(animate: true);
    }
  }

  void _scrollToActive({bool animate = true}) {
    if (!mounted || _activeIndex < 0 || _activeIndex >= _tabKeys.length) return;
    final ctx = _tabKeys[_activeIndex].currentContext;
    if (ctx == null) return;
    Scrollable.ensureVisible(
      ctx,
      alignment: 0.5,
      duration: animate ? const Duration(milliseconds: 260) : Duration.zero,
      curve: Curves.easeOutCubic,
    );
  }

  @override
  Widget build(BuildContext context) {
    return ClipRect(
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
        child: Container(
          height: widget.preferredSize.height,
          width: double.infinity,
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.78),
            border: Border(
              bottom: BorderSide(
                color: const Color(0xFF016D77).withValues(alpha: 0.10),
                width: 1,
              ),
            ),
          ),
          child: LayoutBuilder(
            builder: (context, constraints) {
              final minRowWidth =
                  (constraints.maxWidth - 28).clamp(0.0, double.infinity);
              return SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                child: ConstrainedBox(
                  constraints: BoxConstraints(minWidth: minRowWidth),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(widget.tabs.length, (index) {
                final tab = widget.tabs[index];
                final isSelected = index == _activeIndex;
                return Padding(
                  key: _tabKeys[index],
                  padding: EdgeInsets.only(
                    right: index == widget.tabs.length - 1 ? 0 : 8,
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () {
                        HapticFeedback.selectionClick();
                        if (widget.controller.index != index) {
                          widget.controller.animateTo(index);
                        }
                      },
                      borderRadius: BorderRadius.circular(24),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 220),
                        curve: Curves.easeOutCubic,
                        height: 36,
                        padding: const EdgeInsets.symmetric(horizontal: 14),
                        decoration: BoxDecoration(
                          gradient: isSelected
                              ? const LinearGradient(
                                  colors: [Color(0xFF016D77), Color(0xFF015159)],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                )
                              : null,
                          color: isSelected
                              ? null
                              : Colors.white.withValues(alpha: 0.92),
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(
                            color: isSelected
                                ? const Color(0xFF016D77)
                                : const Color(0xFFCBD5E1).withValues(alpha: 0.75),
                            width: 1,
                          ),
                          boxShadow: isSelected
                              ? [
                                  BoxShadow(
                                    color: const Color(0xFF016D77)
                                        .withValues(alpha: 0.28),
                                    blurRadius: 8,
                                    offset: const Offset(0, 3),
                                  ),
                                ]
                              : [
                                  BoxShadow(
                                    color: Colors.black.withValues(alpha: 0.03),
                                    blurRadius: 4,
                                    offset: const Offset(0, 1),
                                  ),
                                ],
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            if (tab.icon != null) ...[
                              Icon(
                                tab.icon,
                                size: 15,
                                color: isSelected
                                    ? const Color(0xFFFFD5B8)
                                    : const Color(0xFF016D77),
                              ),
                              const SizedBox(width: 6),
                            ],
                            Text(
                              tab.label,
                              style: TextStyle(
                                fontSize: 12.5,
                                fontWeight: isSelected
                                    ? FontWeight.w700
                                    : FontWeight.w600,
                                color: isSelected
                                    ? Colors.white
                                    : const Color(0xFF334155),
                                letterSpacing: 0.15,
                              ),
                            ),
                            if (isSelected) ...[
                              const SizedBox(width: 6),
                              Container(
                                width: 6,
                                height: 6,
                                decoration: const BoxDecoration(
                                  color: Color(0xFFEE6A2A),
                                  shape: BoxShape.circle,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              }),
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }
}

