import 'package:flutter/material.dart';

/// NewsTickerBar with orange "KPSTA NEWS" badge + silky-smooth vsync marquee.
/// Uses Flutter's AnimationController (vsync) instead of raw Timers and ScrollControllers,
/// completely avoiding mouse tracker conflicts on desktop platforms.
class NewsTickerBar extends StatefulWidget {
  final List<dynamic> items;

  const NewsTickerBar({super.key, required this.items});

  @override
  State<NewsTickerBar> createState() => _NewsTickerBarState();
}

class _NewsTickerBarState extends State<NewsTickerBar>
    with SingleTickerProviderStateMixin {
  late final AnimationController _animController;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 25),
    )..repeat();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.items.isEmpty) return const SizedBox.shrink();

    final tickerText = widget.items
        .map((item) {
          if (item is Map) {
            return item['title']?.toString() ??
                item['description']?.toString() ??
                '';
          }
          return item.toString();
        })
        .where((t) => t.trim().isNotEmpty)
        .join('    ★    ');

    if (tickerText.trim().isEmpty) return const SizedBox.shrink();

    final fullText = '$tickerText    ★    $tickerText    ★    ';

    return Container(
      height: 38,
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(
          bottom: BorderSide(color: Color(0xFFE2E8F0), width: 1),
        ),
      ),
      child: Row(
        children: [
          // Orange Badge
          Container(
            height: 38,
            padding: const EdgeInsets.symmetric(horizontal: 14),
            color: const Color(0xFFF05A22),
            alignment: Alignment.center,
            child: const Text(
              'KPSTA NEWS',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w800,
                fontSize: 12,
                letterSpacing: 0.5,
              ),
            ),
          ),
          // Infinite Smooth Ticker
          Expanded(
            child: ClipRect(
              child: AnimatedBuilder(
                animation: _animController,
                builder: (context, child) {
                  return FractionalTranslation(
                    translation: Offset(-_animController.value * 0.5, 0.0),
                    child: child,
                  );
                },
                child: Text(
                  fullText,
                  maxLines: 1,
                  softWrap: false,
                  overflow: TextOverflow.visible,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF1E293B),
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
