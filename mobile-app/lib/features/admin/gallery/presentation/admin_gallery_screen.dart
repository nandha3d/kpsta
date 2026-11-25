import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/app_network_image.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final adminGalleriesProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.adminGalleries);
  return (response['data'] as List? ?? []);
});

class AdminGalleryScreen extends ConsumerWidget {
  const AdminGalleryScreen({super.key});

  Future<void> _deleteAlbum(BuildContext context, WidgetRef ref, int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Photo Album'),
        content: const Text(
            'This will permanently delete this album and all its photos. Proceed?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Delete Album'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      try {
        final client = ref.read(apiClientProvider);
        await client.delete('${ApiConstants.adminGalleries}/$id');
        ref.refresh(adminGalleriesProvider);
      } catch (e) {
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(e.toString().replaceAll('ApiException: ', '')),
              backgroundColor: AppColors.error,
            ),
          );
        }
      }
    }
  }

  void _showCreateAlbumDialog(BuildContext context, WidgetRef ref) {
    final nameController = TextEditingController();
    final descController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Create New Album'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: nameController,
              decoration: const InputDecoration(labelText: 'Album Title *'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: descController,
              maxLines: 2,
              decoration: const InputDecoration(labelText: 'Description (optional)'),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              final name = nameController.text.trim();
              if (name.isEmpty) return;
              Navigator.pop(ctx);
              try {
                final client = ref.read(apiClientProvider);
                await client.post(
                  ApiConstants.adminGalleries,
                  data: {
                    'name': name,
                    'description': descController.text.trim(),
                    'is_publish': 1,
                  },
                );
                ref.refresh(adminGalleriesProvider);
              } catch (e) {
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(e.toString().replaceAll('ApiException: ', '')),
                      backgroundColor: AppColors.error,
                    ),
                  );
                }
              }
            },
            child: const Text('Create'),
          ),
        ],
      ),
    );
  }

  void _showPopupSettingsDialog(BuildContext context, WidgetRef ref) async {
    final client = ref.read(apiClientProvider);
    bool isEnabled = true;
    int duration = 20;

    try {
      final res = await client.get(ApiConstants.adminSettings);
      final data = res['data'] as Map<String, dynamic>?;
      if (data != null) {
        isEnabled = (data['reaction_popup_enabled'] ?? 1) == 1;
        duration = data['reaction_popup_duration'] ?? 20;
      }
    } catch (_) {}

    if (!context.mounted) return;

    final durationController = TextEditingController(text: duration.toString());

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) => AlertDialog(
          title: const Text('Reaction Gallery Popup Settings'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Customize the automatic Reaction Poster popup shown to visitors on the Home Page.',
                style: TextStyle(fontSize: 12, color: AppColors.textMuted),
              ),
              const SizedBox(height: 16),
              SwitchListTile(
                contentPadding: EdgeInsets.zero,
                title: const Text('Enable Auto-Popup', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                subtitle: const Text('Shows portrait reaction poster popup on home', style: TextStyle(fontSize: 11)),
                value: isEnabled,
                activeThumbColor: AppColors.primary,
                onChanged: (val) {
                  setDialogState(() {
                    isEnabled = val;
                  });
                },
              ),
              const SizedBox(height: 10),
              TextField(
                controller: durationController,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(
                  labelText: 'Delay Before Popup (Seconds)',
                  suffixText: 'sec',
                  helperText: 'Default: 20 seconds',
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () async {
                final d = int.tryParse(durationController.text.trim()) ?? 20;
                Navigator.pop(ctx);
                try {
                  await client.post(
                    ApiConstants.adminSettings,
                    data: {
                      'reaction_popup_enabled': isEnabled ? 1 : 0,
                      'reaction_popup_duration': d < 1 ? 20 : d,
                    },
                  );
                  if (context.mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Reaction popup settings saved successfully!'),
                        backgroundColor: AppColors.green,
                      ),
                    );
                  }
                } catch (e) {
                  if (context.mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text(e.toString().replaceAll('ApiException: ', '')),
                        backgroundColor: AppColors.error,
                      ),
                    );
                  }
                }
              },
              child: const Text('Save Settings'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final albumsAsync = ref.watch(adminGalleriesProvider);

    return Scaffold(
      appBar: AppTopBar(
        title: 'Manage Photo Albums',
        actions: [
          IconButton(
            icon: const Icon(Icons.tune, color: AppColors.white),
            tooltip: 'Reaction Popup Settings',
            onPressed: () => _showPopupSettingsDialog(context, ref),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showCreateAlbumDialog(context, ref),
        backgroundColor: AppColors.primary,
        icon: const Icon(Icons.add_photo_alternate, color: AppColors.white),
        label: const Text('New Album', style: TextStyle(color: AppColors.white)),
      ),
      body: albumsAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, _) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(adminGalleriesProvider),
        ),
        data: (albums) {
          if (albums.isEmpty) {
            return const EmptyState(
              icon: Icons.photo_library_outlined,
              title: 'No Gallery Albums',
              subtitle: 'Create a new album to upload photos of meetings and conferences.',
            );
          }

          return RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async => ref.refresh(adminGalleriesProvider),
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: albums.length,
              separatorBuilder: (_, __) => const SizedBox(height: 12),
              itemBuilder: (context, idx) {
                final album = albums[idx] as Map<String, dynamic>;
                final id = album['id'] as int;
                final name = album['name']?.toString() ?? 'Untitled Album';
                final desc = album['description']?.toString() ?? '';
                final count = album['image_count']?.toString() ?? '0';
                final cover = album['cover_image']?.toString();

                return Card(
                  clipBehavior: Clip.antiAlias,
                  child: InkWell(
                    onTap: () => context.push('/gallery/$id'),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Cover Thumbnail
                        Container(
                          width: 110,
                          height: 110,
                          color: AppColors.backgroundSecondary,
                          child: AppNetworkImage(
                            imageUrl: cover,
                            fit: BoxFit.cover,
                            errorWidget: const Icon(
                              Icons.photo_library,
                              color: AppColors.textMuted,
                              size: 36,
                            ),
                          ),
                        ),
                        Expanded(
                          child: Padding(
                            padding: const EdgeInsets.all(12),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Expanded(
                                      child: Text(
                                        name,
                                        style: const TextStyle(
                                          fontSize: 15,
                                          fontWeight: FontWeight.w700,
                                          color: AppColors.textDark,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    IconButton(
                                      icon: const Icon(Icons.delete_outline,
                                          size: 20, color: AppColors.error),
                                      onPressed: () =>
                                          _deleteAlbum(context, ref, id),
                                    ),
                                  ],
                                ),
                                if (desc.isNotEmpty) ...[
                                  const SizedBox(height: 2),
                                  Text(
                                    desc,
                                    style: const TextStyle(
                                      fontSize: 12,
                                      color: AppColors.textMuted,
                                    ),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ],
                                const SizedBox(height: 6),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 8, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: AppColors.primaryLight,
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    '$count photos',
                                    style: const TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: AppColors.primary,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
