import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../../core/widgets/flag_background_wrapper.dart';
import '../../../auth/presentation/auth_providers.dart';

final reactionGalleryListProvider =
    FutureProvider<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.reactionGallery, cacheDuration: const Duration(minutes: 5));
  return response['data'] as List<dynamic>? ?? [];
});

/// ReactionGalleryScreen
/// Premium reaction poster gallery with:
/// - Full-sized uncropped photo display with horizontal swipe navigation
/// - Pinch zoom-out to reveal the gallery boxes grid overview
/// - Tap any gallery box to smoothly inspect that photo in full size
/// - Top counter pill, navigation controls, and subtle overlays
class ReactionGalleryScreen extends ConsumerStatefulWidget {
  const ReactionGalleryScreen({super.key});

  @override
  ConsumerState<ReactionGalleryScreen> createState() =>
      _ReactionGalleryScreenState();
}

class _ReactionGalleryScreenState extends ConsumerState<ReactionGalleryScreen> {
  late final PageController _pageController;
  late final TransformationController _transformationController;
  int _currentIndex = 0;
  bool _isGridView = false;
  bool _showOverlays = true;

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
    _transformationController = TransformationController();
  }

  @override
  void dispose() {
    _pageController.dispose();
    _transformationController.dispose();
    super.dispose();
  }

  void _openPosterFromGrid(int index) {
    HapticFeedback.selectionClick();
    setState(() {
      _currentIndex = index;
      _isGridView = false;
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_pageController.hasClients) {
        _pageController.jumpToPage(index);
      }
    });
  }

  void _goToPrevious() {
    if (_currentIndex > 0) {
      HapticFeedback.selectionClick();
      _pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeOutCubic,
      );
    }
  }

  void _goToNext(int total) {
    if (_currentIndex < total - 1) {
      HapticFeedback.selectionClick();
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeOutCubic,
      );
    }
  }

  void _handlePinchEnd(ScaleEndDetails details) {
    final scale = _transformationController.value.getMaxScaleOnAxis();
    // When pinching zoom out below 0.85, smoothly reveal the gallery boxes
    if (scale < 0.85) {
      HapticFeedback.mediumImpact();
      setState(() {
        _isGridView = true;
      });
      _transformationController.value = Matrix4.identity();
    }
  }

  @override
  Widget build(BuildContext context) {
    final reactionsAsync = ref.watch(reactionGalleryListProvider);

    return Scaffold(
      backgroundColor: _isGridView ? Colors.transparent : const Color(0xFF0F172A),
      extendBodyBehindAppBar: !_isGridView,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(56),
        child: _isGridView
            ? AppBar(
                backgroundColor: Colors.white.withValues(alpha: 0.88),
                elevation: 0.5,
                centerTitle: true,
                leading: IconButton(
                  icon: const Icon(Icons.arrow_back_rounded, color: Color(0xFF016D77)),
                  onPressed: () => Navigator.of(context).pop(),
                ),
                title: const Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Text(
                      'Reaction Gallery',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F1E24),
                      ),
                    ),
                    Text(
                      'Select an image to view full size',
                      style: TextStyle(
                        fontSize: 11,
                        color: Color(0xFF64748B),
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
                actions: [
                  IconButton(
                    icon: const Icon(Icons.fullscreen_rounded, color: Color(0xFFEE6A2A)),
                    tooltip: 'Full size view',
                    onPressed: () {
                      HapticFeedback.selectionClick();
                      setState(() => _isGridView = false);
                    },
                  ),
                ],
              )
            : AnimatedOpacity(
                opacity: _showOverlays ? 1.0 : 0.0,
                duration: const Duration(milliseconds: 220),
                child: IgnorePointer(
                  ignoring: !_showOverlays,
                  child: AppBar(
                    backgroundColor: Colors.transparent,
                    elevation: 0,
                    centerTitle: true,
                    leading: IconButton(
                      icon: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.45),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 20),
                      ),
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    title: const Text(
                      'Reaction Gallery',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: Colors.white,
                      ),
                    ),
                    actions: [
                      // Gallery Boxes Toggle Button
                      IconButton(
                        icon: Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(
                            color: Colors.black.withValues(alpha: 0.45),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.grid_view_rounded, color: Colors.white, size: 20),
                        ),
                        tooltip: 'View all gallery boxes',
                        onPressed: () {
                          HapticFeedback.selectionClick();
                          setState(() => _isGridView = true);
                        },
                      ),
                    ],
                  ),
                ),
              ),
      ),
      body: reactionsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.orange),
        ),
        error: (err, stack) => Center(
          child: ErrorState(
            message: err.toString().replaceAll('ApiException: ', ''),
            onRetry: () => ref.refresh(reactionGalleryListProvider),
          ),
        ),
        data: (reactions) {
          if (reactions.isEmpty) {
            return const EmptyState(
              title: 'No reaction posters found',
              message: 'Official reaction posters will be displayed here.',
              icon: Icons.campaign_outlined,
            );
          }

          return AnimatedSwitcher(
            duration: const Duration(milliseconds: 300),
            switchInCurve: Curves.easeOutCubic,
            switchOutCurve: Curves.easeInCubic,
            transitionBuilder: (child, animation) {
              return FadeTransition(
                opacity: animation,
                child: ScaleTransition(
                  scale: Tween<double>(begin: 0.95, end: 1.0).animate(animation),
                  child: child,
                ),
              );
            },
            child: _isGridView
                ? _buildGalleryBoxesGrid(reactions)
                : _buildFullScreenSwiper(reactions),
          );
        },
      ),
    );
  }

  // ===========================================================================
  // 1. FULL-SIZED PHOTO SWIPABLE VIEW (With Pinch-to-Zoom Out Detector)
  // ===========================================================================
  Widget _buildFullScreenSwiper(List<dynamic> reactions) {
    final total = reactions.length;
    final currentItem = reactions[_currentIndex.clamp(0, total - 1)];
    final currentTitle = currentItem['title']?.toString() ??
        currentItem['description']?.toString() ??
        'KPSTA Official Reaction';
    final currentDate = currentItem['created_at']?.toString();

    return Stack(
      key: const ValueKey('full_screen_swiper'),
      fit: StackFit.expand,
      children: [
        // Horizontal PageView
        GestureDetector(
          onTap: () {
            setState(() {
              _showOverlays = !_showOverlays;
            });
          },
          child: PageView.builder(
            controller: _pageController,
            physics: const BouncingScrollPhysics(),
            itemCount: total,
            onPageChanged: (idx) {
              HapticFeedback.selectionClick();
              setState(() {
                _currentIndex = idx;
              });
            },
            itemBuilder: (ctx, idx) {
              final item = reactions[idx];
              final imageUrl = item['image_url']?.toString();

              return Stack(
                fit: StackFit.expand,
                children: [
                  // Ambient Blurred Edge Extension
                  if (imageUrl != null)
                    ImageFiltered(
                      imageFilter: ImageFilter.blur(sigmaX: 30, sigmaY: 30),
                      child: AppNetworkImage(
                        imageUrl: imageUrl,
                        fit: BoxFit.cover,
                      ),
                    ),
                  Container(
                    color: Colors.black.withValues(alpha: 0.55),
                  ),

                  // Full-Sized Uncropped Photo with Interactive Pinch Zoom Out
                  SafeArea(
                    child: Center(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 20,
                        ),
                        child: InteractiveViewer(
                          transformationController: idx == _currentIndex
                              ? _transformationController
                              : null,
                          minScale: 0.65, // Allows pinch zoom out below 1.0 to trigger grid
                          maxScale: 4.5,
                          onInteractionEnd: idx == _currentIndex ? _handlePinchEnd : null,
                          child: AppNetworkImage(
                            imageUrl: imageUrl,
                            fit: BoxFit.contain,
                            alignment: Alignment.center,
                            placeholder: const Center(
                              child: CircularProgressIndicator(
                                color: AppColors.orange,
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              );
            },
          ),
        ),

        // Floating Top Pill ("1 / 5 · Pinch out for grid")
        if (_showOverlays)
          Positioned(
            top: MediaQuery.of(context).padding.top + 62,
            left: 0,
            right: 0,
            child: Center(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 14, sigmaY: 14),
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 14,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.50),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.25),
                        width: 1,
                      ),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 7,
                          height: 7,
                          decoration: const BoxDecoration(
                            color: Color(0xFFEE6A2A),
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 7),
                        Text(
                          '${_currentIndex + 1} of $total',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            letterSpacing: 0.3,
                          ),
                        ),
                        const SizedBox(width: 8),
                        const Icon(
                          Icons.pinch_rounded,
                          color: Colors.white70,
                          size: 13,
                        ),
                        const SizedBox(width: 3),
                        const Text(
                          'Pinch out',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 11,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),

        // Left Navigation Arrow Button
        if (_showOverlays && _currentIndex > 0)
          Positioned(
            left: 12,
            top: 0,
            bottom: 0,
            child: Center(
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: _goToPrevious,
                  borderRadius: BorderRadius.circular(25),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(25),
                    child: BackdropFilter(
                      filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                      child: Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.45),
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.30),
                            width: 1.2,
                          ),
                        ),
                        child: const Icon(
                          Icons.arrow_back_ios_new_rounded,
                          color: Colors.white,
                          size: 18,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),

        // Right Navigation Arrow Button
        if (_showOverlays && _currentIndex < total - 1)
          Positioned(
            right: 12,
            top: 0,
            bottom: 0,
            child: Center(
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: () => _goToNext(total),
                  borderRadius: BorderRadius.circular(25),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(25),
                    child: BackdropFilter(
                      filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                      child: Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.45),
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.30),
                            width: 1.2,
                          ),
                        ),
                        child: const Icon(
                          Icons.arrow_forward_ios_rounded,
                          color: Colors.white,
                          size: 18,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),

        // Bottom Details Card
        if (_showOverlays)
          Positioned(
            left: 16,
            right: 16,
            bottom: 24,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFF0F172A).withValues(alpha: 0.75),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: Colors.white.withValues(alpha: 0.20),
                      width: 1.2,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.25),
                        blurRadius: 16,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 3,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFFEE6A2A).withValues(alpha: 0.20),
                              borderRadius: BorderRadius.circular(6),
                              border: Border.all(
                                color: const Color(0xFFEE6A2A).withValues(alpha: 0.40),
                                width: 0.8,
                              ),
                            ),
                            child: const Text(
                              'OFFICIAL REACTION POSTER',
                              style: TextStyle(
                                color: Color(0xFFFF9E66),
                                fontSize: 9,
                                fontWeight: FontWeight.w800,
                                letterSpacing: 0.5,
                              ),
                            ),
                          ),
                          const Spacer(),
                          if (currentDate != null && currentDate.isNotEmpty)
                            Text(
                              currentDate,
                              style: const TextStyle(
                                color: Colors.white60,
                                fontSize: 11,
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Text(
                        currentTitle,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                      const Row(
                        children: [
                          Icon(
                            Icons.touch_app_rounded,
                            color: Colors.white54,
                            size: 13,
                          ),
                          SizedBox(width: 4),
                          Text(
                            'Swipe left/right · Pinch in to zoom · Pinch out for grid',
                            style: TextStyle(
                              color: Colors.white54,
                              fontSize: 10.5,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
      ],
    );
  }

  // ===========================================================================
  // 2. GALLERY BOXES OVERVIEW (Revealed on Pinch Zoom Out)
  // ===========================================================================
  Widget _buildGalleryBoxesGrid(List<dynamic> reactions) {
    return FlagBackgroundWrapper(
      key: const ValueKey('gallery_boxes_grid'),
      child: SafeArea(
        top: false,
        child: GridView.builder(
          padding: const EdgeInsets.fromLTRB(14, 14, 14, 40),
          physics: const BouncingScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.64,
            crossAxisSpacing: 12,
            mainAxisSpacing: 14,
          ),
          itemCount: reactions.length,
          itemBuilder: (ctx, idx) {
            final item = reactions[idx];
            final imageUrl = item['image_url']?.toString();
            final title = item['title']?.toString() ??
                item['description']?.toString() ??
                'KPSTA Poster';
            final isCurrent = idx == _currentIndex;

            return ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.85),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: isCurrent
                          ? const Color(0xFFEE6A2A)
                          : Colors.white.withValues(alpha: 0.90),
                      width: isCurrent ? 2.2 : 1.2,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: isCurrent
                            ? const Color(0xFFEE6A2A).withValues(alpha: 0.25)
                            : const Color(0xFF016D77).withValues(alpha: 0.08),
                        blurRadius: isCurrent ? 14 : 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () => _openPosterFromGrid(idx),
                      borderRadius: BorderRadius.circular(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Poster Image Box
                          Expanded(
                            child: Stack(
                              fit: StackFit.expand,
                              children: [
                                ClipRRect(
                                  borderRadius: const BorderRadius.vertical(
                                    top: Radius.circular(15),
                                  ),
                                  child: AppNetworkImage(
                                    imageUrl: imageUrl,
                                    fit: BoxFit.cover,
                                    alignment: Alignment.topCenter,
                                  ),
                                ),
                                // Selection Ribbon if currently active
                                if (isCurrent)
                                  Positioned(
                                    top: 8,
                                    right: 8,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 7,
                                        vertical: 3,
                                      ),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFFEE6A2A),
                                        borderRadius: BorderRadius.circular(10),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black.withValues(alpha: 0.20),
                                            blurRadius: 4,
                                          ),
                                        ],
                                      ),
                                      child: const Text(
                                        'Viewing',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontSize: 9,
                                          fontWeight: FontWeight.w800,
                                        ),
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                          ),

                          // Poster Details Footnote
                          Padding(
                            padding: const EdgeInsets.all(9),
                            child: Text(
                              title,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                fontSize: 11.5,
                                fontWeight: isCurrent
                                    ? FontWeight.w800
                                    : FontWeight.w600,
                                color: isCurrent
                                    ? const Color(0xFF0F1E24)
                                    : const Color(0xFF334155),
                                height: 1.2,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
