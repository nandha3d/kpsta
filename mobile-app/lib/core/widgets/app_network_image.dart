import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../constants/api_constants.dart';
import '../constants/app_colors.dart';

/// Ultra-Fast, Low-Memory Image Loader
/// - Downsamples decoded raster bitmaps using memCacheWidth / memCacheHeight
/// - Uses persistent disk and memory caching via CachedNetworkImage
/// - Smooth 60/120fps scrolling with FilterQuality.low during rasterization
/// - Cross-platform fallback on Web and Desktop
class AppNetworkImage extends StatelessWidget {
  final String? imageUrl;
  final double? width;
  final double? height;
  final BoxFit fit;
  final Alignment alignment;
  final BorderRadius? borderRadius;
  final Widget? placeholder;
  final Widget? errorWidget;
  final int? memCacheWidth;
  final int? memCacheHeight;

  const AppNetworkImage({
    super.key,
    required this.imageUrl,
    this.width,
    this.height,
    this.fit = BoxFit.cover,
    this.alignment = Alignment.center,
    this.borderRadius,
    this.placeholder,
    this.errorWidget,
    this.memCacheWidth,
    this.memCacheHeight,
  });

  Widget _buildErrorWidget() {
    return errorWidget ??
        Container(
          width: width,
          height: height,
          color: const Color(0xFFF1F5F9),
          alignment: Alignment.center,
          child: const Icon(
            Icons.broken_image_outlined,
            color: AppColors.textMuted,
            size: 24,
          ),
        );
  }

  Widget _buildPlaceholder() {
    return placeholder ??
        Container(
          width: width,
          height: height,
          color: const Color(0xFFF1F5F9),
        );
  }

  @override
  Widget build(BuildContext context) {
    String? url = imageUrl?.trim();

    // Re-route legacy kpsta.in upload domain to active live animazon server
    if (url != null && url.contains('kpsta.in/uploads')) {
      url = url.replaceAll('kpsta.in/uploads', 'animazon.in/kpsta/uploads');
    }

    // Auto-normalize gallery URLs to thumb directory on live server
    if (url != null &&
        url.contains('/uploads/gallery/') &&
        !url.contains('/uploads/gallery/thumb/') &&
        !url.contains('/uploads/gallery/original/')) {
      url = url.replaceAll('/uploads/gallery/', '/uploads/gallery/thumb/');
    }

    // Auto-resolve relative image paths against the active live website domain
    if (url != null &&
        url.isNotEmpty &&
        !url.startsWith('http://') &&
        !url.startsWith('https://')) {
      final base = ApiConstants.siteUrl;
      if (url.startsWith('/')) {
        url = '$base$url';
      } else {
        url = '$base/$url';
      }
    }

    if (url == null ||
        url.isEmpty ||
        (!url.startsWith('http://') && !url.startsWith('https://'))) {
      return _wrapRadius(_buildErrorWidget());
    }

    // Downsample resolution for memory savings (avoids decoding full 4K bitmaps in small cards)
    final targetMemWidth = memCacheWidth ??
        (width != null ? (width! * 1.6).round().clamp(60, 900) : 600);
    final targetMemHeight = memCacheHeight ??
        (height != null ? (height! * 1.6).round().clamp(60, 1200) : null);

    Widget imageContent;

    // Use CachedNetworkImage on mobile platforms
    if (!kIsWeb) {
      imageContent = CachedNetworkImage(
        imageUrl: url,
        width: width,
        height: height,
        fit: fit,
        alignment: alignment,
        memCacheWidth: targetMemWidth,
        memCacheHeight: targetMemHeight,
        filterQuality: FilterQuality.low, // Maximum scroll FPS
        placeholder: (context, _) => _buildPlaceholder(),
        errorWidget: (context, _, _) => _buildErrorWidget(),
        fadeInDuration: const Duration(milliseconds: 80),
        fadeOutDuration: const Duration(milliseconds: 60),
      );
    } else {
      // Clean fallback on web
      imageContent = Image.network(
        url,
        width: width,
        height: height,
        fit: fit,
        alignment: alignment,
        cacheWidth: targetMemWidth,
        cacheHeight: targetMemHeight,
        filterQuality: FilterQuality.low,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return _buildPlaceholder();
        },
        errorBuilder: (context, error, stackTrace) => _buildErrorWidget(),
      );
    }

    return _wrapRadius(imageContent);
  }

  Widget _wrapRadius(Widget content) {
    if (borderRadius != null) {
      return ClipRRect(
        borderRadius: borderRadius!,
        child: content,
      );
    }
    return content;
  }
}
