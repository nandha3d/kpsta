import 'dart:async';
import 'package:flutter/material.dart';
import 'app_network_image.dart';

/// Self-contained Hero Carousel Slider widget with auto-play and indicator dots.
/// Keeps its own PageController, Timer, and slide state isolated from the parent screen,
/// completely preventing unnecessary parent rebuilds and re-entrant layout crashes.
class HeroCarouselSlider extends StatefulWidget {
  final List<dynamic> sliders;
  final double height;

  const HeroCarouselSlider({
    super.key,
    required this.sliders,
    this.height = 250.0,
  });

  @override
  State<HeroCarouselSlider> createState() => _HeroCarouselSliderState();
}

class _HeroCarouselSliderState extends State<HeroCarouselSlider> {
  late final PageController _pageController;
  int _currentSlide = 0;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
    _startTimer();
  }

  @override
  void didUpdateWidget(covariant HeroCarouselSlider oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.sliders.length != widget.sliders.length) {
      _startTimer();
    }
  }

  void _startTimer() {
    _timer?.cancel();
    final count = widget.sliders.isNotEmpty ? widget.sliders.length : 1;
    if (count <= 1) return;

    _timer = Timer.periodic(const Duration(seconds: 5), (_) {
      if (!mounted || !_pageController.hasClients) return;
      try {
        if (!_pageController.position.hasContentDimensions) return;
        final currentCount = widget.sliders.isNotEmpty ? widget.sliders.length : 1;
        if (currentCount <= 1) return;
        final nextPage = (_currentSlide + 1) % currentCount;
        _pageController.animateToPage(
          nextPage,
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOut,
        );
      } catch (_) {}
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final count = widget.sliders.isNotEmpty ? widget.sliders.length : 1;

    return SizedBox(
      height: widget.height,
      child: Stack(
        alignment: Alignment.bottomCenter,
        children: [
          Positioned.fill(
            child: PageView.builder(
              controller: _pageController,
              itemCount: count,
              onPageChanged: (idx) {
                if (mounted && _currentSlide != idx) {
                  setState(() {
                    _currentSlide = idx;
                  });
                }
              },
              itemBuilder: (ctx, idx) {
                final slider = widget.sliders.isNotEmpty ? widget.sliders[idx] : null;
                final imgUrl = slider?['image_url']?.toString() ?? '';
                final bool isFirstSlide = idx == 0;

                return Stack(
                  fit: StackFit.expand,
                  children: [
                    // Slider Image
                    AppNetworkImage(
                      imageUrl: imgUrl,
                      fit: BoxFit.cover,
                      placeholder: Container(color: const Color(0xFF083338)),
                      errorWidget: Container(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [Color(0xFF08414B), Color(0xFF031B20)],
                          ),
                        ),
                      ),
                    ),

                    if (isFirstSlide) ...[
                      // Dark-tinted Tricolour Scrim (keeps white text crisp & flag vibrant)
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              const Color(0xFF4A1C06).withValues(alpha: 0.78),
                              const Color(0xFF0A161A).withValues(alpha: 0.80),
                              const Color(0xFF0A300E).withValues(alpha: 0.78),
                            ],
                            stops: const [0.0, 0.5, 1.0],
                          ),
                        ),
                      ),

                      // Overlay Content matching Web Hero Banner (Slide 1 only)
                      Center(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset(
                                'assets/images/flag.png',
                                height: 48,
                                fit: BoxFit.contain,
                                errorBuilder: (_, __, ___) => const SizedBox.shrink(),
                              ),
                              const SizedBox(height: 6),
                              const Text(
                                'KPSTA',
                                style: TextStyle(
                                  fontSize: 32,
                                  fontWeight: FontWeight.w900,
                                  color: Colors.white,
                                  letterSpacing: 1.5,
                                  height: 1.1,
                                ),
                              ),
                              const Text(
                                "Kerala Pradesh School Teacher's Association",
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.white,
                                  letterSpacing: 0.3,
                                ),
                              ),
                              const SizedBox(height: 10),
                              const Text(
                                'UNITE FOR QUALITY EDUCATION',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w800,
                                  color: Colors.white,
                                  letterSpacing: 0.6,
                                ),
                              ),
                              const SizedBox(height: 2),
                              const Text(
                                'Better education for a better world',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 11.5,
                                  color: Colors.white70,
                                  fontStyle: FontStyle.italic,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ] else
                      // Subsequent slides: NO dark/tricolour screen and NO text — ONLY the flag
                      Center(
                        child: Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: RadialGradient(
                              colors: [
                                Colors.black.withValues(alpha: 0.32),
                                Colors.black.withValues(alpha: 0.0),
                              ],
                            ),
                          ),
                          child: Image.asset(
                            'assets/images/flag.png',
                            height: 76,
                            fit: BoxFit.contain,
                            errorBuilder: (_, __, ___) => const SizedBox.shrink(),
                          ),
                        ),
                      ),
                  ],
                );
              },
            ),
          ),

          // Slide Indicator Dots
          if (count > 1)
            Positioned(
              left: 0,
              right: 0,
              bottom: 12,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(count, (dotIdx) {
                  final isActive = _currentSlide == dotIdx;
                  return Container(
                    width: isActive ? 18 : 7,
                    height: 7,
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    decoration: BoxDecoration(
                      color: isActive ? Colors.white : Colors.white54,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  );
                }),
              ),
            ),
        ],
      ),
    );
  }
}
