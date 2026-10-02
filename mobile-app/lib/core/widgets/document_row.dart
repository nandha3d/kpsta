import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constants/app_colors.dart';
import 'status_badge.dart';

/// Unified document row for circulars, forms, downloads, and memorandums
class DocumentRow extends StatelessWidget {
  final String title;
  final String? date;
  final String? category;
  final String? fileUrl;
  final bool? isPublished;
  final VoidCallback? onTap;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;

  const DocumentRow({
    super.key,
    required this.title,
    this.date,
    this.category,
    this.fileUrl,
    this.isPublished,
    this.onTap,
    this.onEdit,
    this.onDelete,
  });

  String _formatDate(String? raw) {
    if (raw == null || raw.isEmpty) return '';
    try {
      final dt = DateTime.parse(raw);
      return DateFormat('dd MMM yyyy').format(dt);
    } catch (_) {
      return raw;
    }
  }

  Future<void> _openFile() async {
    if (fileUrl != null && fileUrl!.isNotEmpty) {
      final uri = Uri.parse(fileUrl!);
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final formattedDate = _formatDate(date);

    return InkWell(
      onTap: onTap ?? _openFile,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: const BoxDecoration(
          color: AppColors.white,
          border: Border(
            bottom: BorderSide(color: AppColors.border, width: 0.8),
          ),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // PDF/Document Icon
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.08),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(
                Icons.picture_as_pdf_outlined,
                color: AppColors.primary,
                size: 24,
              ),
            ),
            const SizedBox(width: 14),

            // Content Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textDark,
                      height: 1.3,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      if (formattedDate.isNotEmpty) ...[
                        const Icon(
                          Icons.calendar_today_outlined,
                          size: 13,
                          color: AppColors.textMuted,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          formattedDate,
                          style: const TextStyle(
                            fontSize: 12,
                            color: AppColors.textMuted,
                          ),
                        ),
                        const SizedBox(width: 12),
                      ],
                      if (category != null && category!.isNotEmpty)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 6,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: AppColors.bgLight,
                            borderRadius: BorderRadius.circular(4),
                            border: Border.all(
                              color: AppColors.border,
                              width: 0.6,
                            ),
                          ),
                          child: Text(
                            category!,
                            style: const TextStyle(
                              fontSize: 11,
                              color: AppColors.textMuted,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                    ],
                  ),
                ],
              ),
            ),

            // Status or Actions
            if (isPublished != null) ...[
              const SizedBox(width: 8),
              StatusBadge(isPublished: isPublished!),
            ],

            if (fileUrl != null && fileUrl!.isNotEmpty)
              IconButton(
                icon: const Icon(
                  Icons.download_for_offline_outlined,
                  color: AppColors.primary,
                  size: 22,
                ),
                tooltip: 'Download File',
                onPressed: _openFile,
              ),

            if (onEdit != null || onDelete != null)
              PopupMenuButton<String>(
                icon: const Icon(Icons.more_vert, color: AppColors.textMuted),
                onSelected: (val) {
                  if (val == 'edit') onEdit?.call();
                  if (val == 'delete') onDelete?.call();
                },
                itemBuilder: (ctx) => [
                  if (onEdit != null)
                    const PopupMenuItem(
                      value: 'edit',
                      child: Row(
                        children: [
                          Icon(Icons.edit_outlined, size: 18),
                          SizedBox(width: 8),
                          Text('Edit'),
                        ],
                      ),
                    ),
                  if (onDelete != null)
                    const PopupMenuItem(
                      value: 'delete',
                      child: Row(
                        children: [
                          Icon(Icons.delete_outline,
                              size: 18, color: AppColors.error),
                          SizedBox(width: 8),
                          Text('Delete',
                              style: TextStyle(color: AppColors.error)),
                        ],
                      ),
                    ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}
