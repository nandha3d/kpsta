import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/widgets/app_network_image.dart';

/// ReactionGalleryPopupDialog
/// Versatile timed modal popup displaying KPSTA Reaction Gallery posters
/// and/or Latest News Window highlights with direct deep-links.
class ReactionGalleryPopupDialog extends StatefulWidget {
  final List<Map<String, dynamic>> items;

  const ReactionGalleryPopupDialog({
    super.key,
    required this.items,
  });

  static Future<void> show(BuildContext context, Map<String, dynamic> item) {
    return showList(context, [item]);
  }

  static Future<void> showList(
    BuildContext context,
    List<Map<String, dynamic>> items,
  ) {
    if (items.isEmpty) return Future.value();
    return showDialog<void>(
      context: context,
      barrierDismissible: true,
      barrierColor: Colors.black.withValues(alpha: 0.72),
      builder: (ctx) => ReactionGalleryPopupDialog(items: items),
    );
  }

  @override
  State<ReactionGalleryPopupDialog> createState() =>
      _ReactionGalleryPopupDialogState();
}

class _ReactionGalleryPopupDialogState
    extends State<ReactionGalleryPopupDialog> {
  late final PageController _pageController;
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  bool _isNewsItem(Map<String, dynamic> item) {
    return item['type']?.toString().toLowerCase() == 'news';
  }

  String _resolveRoute(Map<String, dynamic> item) {
    final explicitRoute = item['route']?.toString() ?? '';
    if (explicitRoute.isNotEmpty) return explicitRoute;

    if (_isNewsItem(item)) {
      final id = item['id']?.toString() ?? '';
      return (id.isNotEmpty && id != '0') ? '/news/$id' : '/news';
    }
    return '/reaction-gallery';
  }

  void _navigateToActiveItem(Map<String, dynamic> item) {
    final route = _resolveRoute(item);
    Navigator.of(context).pop();
    context.push(route);
  }

  @override
  Widget build(BuildContext context) {
    final items = widget.items;
    final activeItem = items[_currentIndex.clamp(0, items.length - 1)];
    final isNews = _isNewsItem(activeItem);

    final mediaQuery = MediaQuery.of(context);
    final dialogWidth = (mediaQuery.size.width * 0.88).clamp(280.0, 380.0);
    final dialogHeight = (mediaQuery.size.height * 0.78).clamp(430.0, 580.0);

    return Dialog(
      backgroundColor: Colors.transparent,
      insetPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
      child: Container(
        width: dialogWidth,
        height: dialogHeight,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.4),
              blurRadius: 28,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(22),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Dynamic Header
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  color: isNews
                      ? const Color(0xFF0A5863)
                      : const Color(0xFF016D77),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 30,
                      height: 30,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.2),
                        shape: BoxShape.circle,
                      ),
                      child: Center(
                        child: Icon(
                          isNews
                              ? Icons.newspaper_rounded
                              : Icons.campaign_rounded,
                          color: Colors.white,
                          size: 16,
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            isNews
                                ? 'KPSTA News Bulletin'
                                : 'KPSTA Reaction Poster',
                            style: const TextStyle(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                              letterSpacing: 0.2,
                            ),
                          ),
                          Text(
                            isNews ? 'പ്രധാന വാർത്തകൾ' : 'പ്രതികരണ പോസ്റ്റർ',
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w500,
                              color: Color(0xFFBFE9EC),
                            ),
                          ),
                        ],
                      ),
                    ),
                    Material(
                      color: Colors.transparent,
                      child: InkWell(
                        onTap: () => Navigator.of(context).pop(),
                        borderRadius: BorderRadius.circular(20),
                        child: Container(
                          padding: const EdgeInsets.all(5),
                          decoration: BoxDecoration(
                            color: Colors.black.withValues(alpha: 0.22),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.close_rounded,
                            color: Colors.white,
                            size: 20,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // Source Switcher Pills when Both Reaction Gallery & News Window are present
              if (items.length > 1)
                Container(
                  color: const Color(0xFFF1F5F9),
                  padding: const EdgeInsets.fromLTRB(10, 8, 10, 8),
                  child: Row(
                    children: List.generate(items.length, (idx) {
                      final entry = items[idx];
                      final entryIsNews = _isNewsItem(entry);
                      final selected = idx == _currentIndex;
                      return Expanded(
                        child: Padding(
                          padding: EdgeInsets.only(
                            left: idx == 0 ? 0 : 4,
                            right: idx == items.length - 1 ? 0 : 4,
                          ),
                          child: GestureDetector(
                            onTap: () {
                              _pageController.animateToPage(
                                idx,
                                duration: const Duration(milliseconds: 240),
                                curve: Curves.easeOutCubic,
                              );
                            },
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 180),
                              padding: const EdgeInsets.symmetric(
                                vertical: 7,
                                horizontal: 8,
                              ),
                              decoration: BoxDecoration(
                                color: selected
                                    ? const Color(0xFF016D77)
                                    : Colors.white,
                                borderRadius: BorderRadius.circular(10),
                                border: Border.all(
                                  color: selected
                                      ? const Color(0xFF016D77)
                                      : const Color(0xFFCBD5E1),
                                ),
                              ),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    entryIsNews
                                        ? Icons.newspaper_rounded
                                        : Icons.campaign_rounded,
                                    size: 14,
                                    color: selected
                                        ? Colors.white
                                        : const Color(0xFF475569),
                                  ),
                                  const SizedBox(width: 5),
                                  Flexible(
                                    child: Text(
                                      entryIsNews
                                          ? 'News Window'
                                          : 'Reaction Poster',
                                      style: TextStyle(
                                        fontSize: 11.5,
                                        fontWeight: FontWeight.w700,
                                        color: selected
                                            ? Colors.white
                                            : const Color(0xFF334155),
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    }),
                  ),
                ),

              // Slideable Content Area (Reaction Poster + News Window)
              Expanded(
                child: PageView.builder(
                  controller: _pageController,
                  itemCount: items.length,
                  onPageChanged: (idx) {
                    setState(() => _currentIndex = idx);
                  },
                  itemBuilder: (context, idx) {
                    final item = items[idx];
                    return GestureDetector(
                      onTap: () => _navigateToActiveItem(item),
                      child: _buildItemBody(item),
                    );
                  },
                ),
              ),

              // Footer Actions
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  border: Border(
                    top: BorderSide(color: Color(0xFFE2E8F0), width: 1),
                  ),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => Navigator.of(context).pop(),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: const Color(0xFF475569),
                          side: const BorderSide(color: Color(0xFFCBD5E1)),
                          padding: const EdgeInsets.symmetric(vertical: 11),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: const Text(
                          'Close',
                          style: TextStyle(
                            fontWeight: FontWeight.w700,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      flex: 2,
                      child: ElevatedButton.icon(
                        icon: Icon(
                          isNews
                              ? Icons.newspaper_rounded
                              : Icons.campaign_rounded,
                          size: 16,
                        ),
                        onPressed: () => _navigateToActiveItem(activeItem),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: isNews
                              ? const Color(0xFFEE6A2A)
                              : const Color(0xFF016D77),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 11),
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        label: Text(
                          isNews ? 'Read News →' : 'Reaction Gallery →',
                          style: const TextStyle(
                            fontWeight: FontWeight.w700,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildItemBody(Map<String, dynamic> item) {
    final imageUrl = item['image_url']?.toString() ?? '';
    final title = item['title']?.toString() ?? '';
    final description = item['description']?.toString() ?? '';
    final isNews = _isNewsItem(item);
    final publishedAt = item['published_at']?.toString() ?? '';

    if (imageUrl.isNotEmpty) {
      return Stack(
        fit: StackFit.expand,
        children: [
          ImageFiltered(
            imageFilter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
            child: AppNetworkImage(
              imageUrl: imageUrl,
              fit: BoxFit.cover,
              alignment: Alignment.center,
            ),
          ),
          Container(color: Colors.black.withValues(alpha: 0.35)),
          AppNetworkImage(
            imageUrl: imageUrl,
            fit: BoxFit.contain,
            alignment: Alignment.center,
            placeholder: Container(
              color: const Color(0xFFE5F3F4),
              child: const Center(
                child: CircularProgressIndicator(
                  color: Color(0xFF016D77),
                  strokeWidth: 2,
                ),
              ),
            ),
            errorWidget: _buildNewsFallbackCard(title, description, publishedAt),
          ),
          if (title.isNotEmpty || description.isNotEmpty)
            Positioned(
              bottom: 0,
              left: 0,
              right: 0,
              child: Container(
                padding: const EdgeInsets.fromLTRB(14, 28, 14, 12),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.bottomCenter,
                    end: Alignment.topCenter,
                    colors: [
                      Colors.black.withValues(alpha: 0.86),
                      Colors.black.withValues(alpha: 0.45),
                      Colors.transparent,
                    ],
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (isNews && publishedAt.isNotEmpty)
                      Container(
                        margin: const EdgeInsets.only(bottom: 4),
                        padding: const EdgeInsets.symmetric(
                          horizontal: 7,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEE6A2A),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          publishedAt.split('T').first,
                          style: const TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    if (title.isNotEmpty)
                      Text(
                        title,
                        style: const TextStyle(
                          fontSize: 13,
                          color: Colors.white,
                          fontWeight: FontWeight.w700,
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    if (isNews && description.isNotEmpty) ...[
                      const SizedBox(height: 3),
                      Text(
                        description,
                        style: const TextStyle(
                          fontSize: 11,
                          color: Color(0xFFE2E8F0),
                          height: 1.35,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ],
                ),
              ),
            ),
        ],
      );
    }

    return _buildNewsFallbackCard(title, description, publishedAt);
  }

  Widget _buildNewsFallbackCard(
    String title,
    String description,
    String publishedAt,
  ) {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFFF0F9FA), Color(0xFFFFFFFF)],
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
        ),
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: const Color(0xFFEE6A2A).withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(
                  Icons.newspaper_rounded,
                  size: 14,
                  color: Color(0xFFEE6A2A),
                ),
                const SizedBox(width: 6),
                Text(
                  publishedAt.isNotEmpty
                      ? 'KPSTA NEWS · ${publishedAt.split('T').first}'
                      : 'KPSTA NEWS WINDOW',
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFFEE6A2A),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),
          Text(
            title.isNotEmpty ? title : 'Latest KPSTA News Bulletin',
            style: const TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.w900,
              color: Color(0xFF0F1E24),
              height: 1.35,
            ),
            maxLines: 4,
            overflow: TextOverflow.ellipsis,
          ),
          if (description.isNotEmpty) ...[
            const SizedBox(height: 10),
            Text(
              description,
              style: const TextStyle(
                fontSize: 13.5,
                color: Color(0xFF475569),
                height: 1.5,
              ),
              maxLines: 6,
              overflow: TextOverflow.ellipsis,
            ),
          ],
          const SizedBox(height: 16),
          const Text(
            'Tap anywhere or press "Read News" below for full details.',
            style: TextStyle(
              fontSize: 11.5,
              fontWeight: FontWeight.w600,
              color: Color(0xFF016D77),
            ),
          ),
        ],
      ),
    );
  }
}
