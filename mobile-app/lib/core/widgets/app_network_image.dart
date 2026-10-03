import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Robust cross-platform image loader that works across Windows, Android, iOS, and Web.
/// Uses Flutter's native network image loader with built-in memory caching,
/// eliminating SQLite/platform channel dependency issues on desktop.
class AppNetworkImage extends StatelessWidget {
  final String? imageUrl;
  final double? width;
  final double? height;
  final BoxFit fit;
  final Alignment alignment;
  final BorderRadius? borderRadius;
  final Widget? placeholder;
  final Widget? errorWidget;

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
          alignment: Alignment.center,
          child: const SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              color: AppColors.primary,
            ),
          ),
        );
  }

  @override
  Widget build(BuildContext context) {
    String? url = imageUrl?.trim();

    // Auto-resolve relative image paths against the live website domain
    if (url != null && url.isNotEmpty && !url.startsWith('http://') && !url.startsWith('https://')) {
      if (url.startsWith('/')) {
        url = 'https://kpsta.in$url';
      } else {
        url = 'https://kpsta.in/$url';
      }
    }

    Widget content;
    if (url == null || url.isEmpty || (!url.startsWith('http://') && !url.startsWith('https://'))) {
      content = _buildErrorWidget();
    } else {
      content = Image.network(
        url,
        headers: const {'Connection': 'close'},
        width: width,
        height: height,
        fit: fit,
        alignment: alignment,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return _buildPlaceholder();
        },
        errorBuilder: (context, error, stackTrace) {
          return _buildErrorWidget();
        },
      );
    }

    if (borderRadius != null) {
      return ClipRRect(
        borderRadius: borderRadius!,
        child: content,
      );
    }

    return content;
  }
}
