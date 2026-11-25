import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/widgets/app_network_image.dart';

/// PortraitReactionGallery
/// Displays KPSTA reaction gallery posters in authentic vertical portrait mode (A4 / 9:14 format):
/// - Uncropped full-height portrait display with BoxFit.contain and ambient blurred backdrop
/// - True vertical aspect ratio (0.70) matching 867x1221 portrait posters
/// - Swipeable carousel with active scale animation and page indicator dots
class PortraitReactionGallery extends StatefulWidget {
  final List<dynamic> reactions;

  const PortraitReactionGallery({
    super.key,
    required this.reactions,
  });

  @override
  State<PortraitReactionGallery> createState() => _PortraitReactionGalleryState();
}

class _PortraitReactionGalleryState extends State<PortraitReactionGallery> {
  int _currentIndex = 0;
  late final PageController _pageController;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(viewportFraction: 0.72);
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.reactions.isEmpty) {
      return const SizedBox.shrink();
    }

    final hasMultiple = widget.reactions.length > 1;

    return Container(
      margin: const EdgeInsets.symmetric(vertical: 14),
      padding: const EdgeInsets.symmetric(vertical: 20),
      decoration: const BoxDecoration(
        color: Color(0xFF0F2C33), // Deep elegant KPSTA teal background
      ),
      child: Column(
        children: [
          // Section Header
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Reaction Gallery',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                        letterSpacing: 0.3,
                      ),
                    ),
                    SizedBox(height: 2),
                    Text(
                      'പ്രതികരണ ഗാലറി · Official Posters',
                      style: TextStyle(
                        fontSize: 11.5,
                        fontWeight: FontWeight.w500,
                        color: Color(0xFF86CCD2),
                      ),
                    ),
                  ],
                ),
                OutlinedButton(
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.white,
                    side: const BorderSide(color: Color(0xFF86CCD2), width: 1.2),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(20),
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                    minimumSize: const Size(0, 32),
                  ),
                  onPressed: () => context.push('/reaction-gallery'),
                  child: const Text(
                    'All Posters',
                    style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // Authentic Vertical Portrait Poster Container (Height 420px, 0.70 ratio)
          SizedBox(
            height: 420,
            child: hasMultiple
                ? PageView.builder(
                    controller: _pageController,
                    itemCount: widget.reactions.length,
                    onPageChanged: (idx) {
                      setState(() {
                        _currentIndex = idx;
                      });
                    },
                    itemBuilder: (context, index) {
                      final item = widget.reactions[index];
                      return _buildPortraitCard(
                        context,
                        item,
                        isCurrent: index == _currentIndex,
                      );
                    },
                  )
                : Center(
                    child: SizedBox(
                      width: 280,
                      child: _buildPortraitCard(
                        context,
                        widget.reactions.first,
                        isCurrent: true,
                      ),
                    ),
                  ),
          ),

          // Page Indicator Dots
          if (hasMultiple) ...[
            const SizedBox(height: 14),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(widget.reactions.length, (idx) {
                final isSelected = idx == _currentIndex;
                return AnimatedContainer(
                  duration: const Duration(milliseconds: 250),
                  margin: const EdgeInsets.symmetric(horizontal: 3),
                  width: isSelected ? 18 : 6,
                  height: 6,
                  decoration: BoxDecoration(
                    color: isSelected ? const Color(0xFFEE6A2A) : Colors.white30,
                    borderRadius: BorderRadius.circular(3),
                  ),
                );
              }),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildPortraitCard(
    BuildContext context,
    dynamic item, {
    required bool isCurrent,
  }) {
    final imageUrl = item['image_url']?.toString();

    return AnimatedScale(
      scale: isCurrent ? 1.0 : 0.92,
      duration: const Duration(milliseconds: 250),
      curve: Curves.easeOutCubic,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 290),
            child: AspectRatio(
              aspectRatio: 0.70, // Matches 867x1221 true vertical portrait ratio
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: () => context.push('/reaction-gallery'),
                  borderRadius: BorderRadius.circular(18),
                  child: Container(
                    decoration: BoxDecoration(
                      color: const Color(0xFF071C21),
                      borderRadius: BorderRadius.circular(18),
                      border: Border.all(
                        color: isCurrent
                            ? const Color(0xFFEE6A2A).withValues(alpha: 0.90)
                            : Colors.white.withValues(alpha: 0.20),
                        width: isCurrent ? 2 : 1.2,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: isCurrent
                              ? const Color(0xFFEE6A2A).withValues(alpha: 0.35)
                              : Colors.black.withValues(alpha: 0.40),
                          blurRadius: isCurrent ? 18 : 10,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(16),
                      child: Stack(
                        fit: StackFit.expand,
                        children: [
                          // 1. Ambient Blurred Backdrop of the Poster
                          if (imageUrl != null)
                            ImageFiltered(
                              imageFilter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
                              child: AppNetworkImage(
                                imageUrl: imageUrl,
                                fit: BoxFit.cover,
                                alignment: Alignment.center,
                              ),
                            ),

                          // Subtle dark tint over backdrop
                          Container(
                            color: Colors.black.withValues(alpha: 0.40),
                          ),

                          // 2. Full Uncropped Vertical Portrait Poster
                          AppNetworkImage(
                            imageUrl: imageUrl,
                            fit: BoxFit.contain, // Displays 100% of the vertical poster
                            alignment: Alignment.center,
                            placeholder: Container(
                              color: const Color(0xFF0A5863),
                              child: const Center(
                                child: CircularProgressIndicator(
                                  color: Color(0xFF86CCD2),
                                  strokeWidth: 2,
                                ),
                              ),
                            ),
                            errorWidget: Container(
                              color: const Color(0xFF033138),
                              child: const Center(
                                child: Icon(
                                  Icons.broken_image_outlined,
                                  color: Colors.white54,
                                  size: 48,
                                ),
                              ),
                            ),
                          ),

                          // 3. Top "PORTRAIT POSTER" Pill Badge
                          Positioned(
                            top: 10,
                            left: 10,
                            child: Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 8,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: const Color(0xFF0F1E24)
                                    .withValues(alpha: 0.88),
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(
                                  color: Colors.white24,
                                  width: 0.8,
                                ),
                              ),
                              child: const Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(
                                    Icons.portrait_rounded,
                                    color: Color(0xFFEE6A2A),
                                    size: 13,
                                  ),
                                  SizedBox(width: 4),
                                  Text(
                                    'PORTRAIT POSTER',
                                    style: TextStyle(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
