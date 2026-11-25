import 'package:flutter/material.dart';

/// NewsTickerBar with orange "KPSTA NEWS" badge + silky-smooth vsync marquee.
/// Uses mathematical periodic translation by exact unitWidth, guaranteeing
/// 100% glitch-free, continuous seamless infinite scrolling.
class NewsTickerBar extends StatefulWidget {
  final List<dynamic> items;
  final double speed; // Pixels per second (e.g. 65.0)

  const NewsTickerBar({
    super.key,
    required this.items,
    this.speed = 65.0,
  });

  @override
  State<NewsTickerBar> createState() => _NewsTickerBarState();
}

class _NewsTickerBarState extends State<NewsTickerBar>
    with SingleTickerProviderStateMixin {
  late final AnimationController _animController;
  double _lastUnitWidth = 0.0;

  static const TextStyle _textStyle = TextStyle(
    fontSize: 13,
    fontWeight: FontWeight.w600,
    color: Color(0xFF1E293B),
  );

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 20),
    )..repeat();
  }

  @override
  void didUpdateWidget(covariant NewsTickerBar oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.items != widget.items || oldWidget.speed != widget.speed) {
      _updateDuration(_lastUnitWidth);
    }
  }

  void _updateDuration(double unitWidth) {
    if (unitWidth <= 0) return;
    _lastUnitWidth = unitWidth;
    final effectiveSpeed = widget.speed.clamp(20.0, 300.0);
    final ms = ((unitWidth / effectiveSpeed) * 1000).round().clamp(2000, 120000);
    final newDuration = Duration(milliseconds: ms);
    if (_animController.duration != newDuration) {
      _animController.duration = newDuration;
      if (!_animController.isAnimating) {
        _animController.repeat();
      }
    }
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  String _buildUnitText() {
    final list = widget.items
        .map((item) {
          if (item is Map) {
            return item['title']?.toString() ??
                item['description']?.toString() ??
                '';
          }
          return item.toString();
        })
        .where((t) => t.trim().isNotEmpty)
        .toList();

    if (list.isEmpty) return '';
    return '${list.join('    ★    ')}    ★    ';
  }

  @override
  Widget build(BuildContext context) {
    if (widget.items.isEmpty) return const SizedBox.shrink();

    final unitText = _buildUnitText();
    if (unitText.trim().isEmpty) return const SizedBox.shrink();

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
            child: LayoutBuilder(
              builder: (context, constraints) {
                final textPainter = TextPainter(
                  text: TextSpan(text: unitText, style: _textStyle),
                  textDirection: TextDirection.ltr,
                  maxLines: 1,
                )..layout();

                final unitWidth = textPainter.width;
                if (unitWidth <= 0) return const SizedBox.shrink();

                // Ensure controller duration matches current unitWidth & speed
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (mounted && _lastUnitWidth != unitWidth) {
                    _updateDuration(unitWidth);
                  }
                });

                final visibleWidth = constraints.maxWidth;
                final repeatsNeeded = (visibleWidth / unitWidth).ceil() + 2;
                final fullText = unitText * repeatsNeeded;

                return ClipRect(
                  child: AnimatedBuilder(
                    animation: _animController,
                    builder: (context, child) {
                      final dx = -(_animController.value * unitWidth);
                      return Transform.translate(
                        offset: Offset(dx, 0),
                        child: child,
                      );
                    },
                    child: OverflowBox(
                      alignment: Alignment.centerLeft,
                      minWidth: 0.0,
                      maxWidth: double.infinity,
                      child: Text(
                        fullText,
                        maxLines: 1,
                        softWrap: false,
                        style: _textStyle,
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
