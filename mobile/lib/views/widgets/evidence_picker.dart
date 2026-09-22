import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../../theme/app_theme.dart';

class EvidencePicker extends StatelessWidget {
  final List<String> photos;
  final Function(ImageSource source) onPickImage;
  final Function(String sampleUrl)? onAddSample;
  final Function(int index)? onRemoveImage;
  final bool isReadOnly;

  const EvidencePicker({
    super.key,
    required this.photos,
    required this.onPickImage,
    this.onAddSample,
    this.onRemoveImage,
    this.isReadOnly = false,
  });

  static const List<Map<String, String>> sampleEvidences = [
    {
      'title': 'Avería Equipo/Proyector',
      'url': 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500',
    },
    {
      'title': 'Fuga / Daño Baño',
      'url': 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=500',
    },
    {
      'title': 'Fallo Red / Conexión',
      'url': 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500',
    },
  ];

  void _showImageSourceDialog(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Adjuntar Evidencia Fotográfica (HU6)',
              style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            ListTile(
              leading: const CircleAvatar(
                backgroundColor: Color(0xFFEFF6FF),
                child: Icon(Icons.camera_alt_rounded, color: AppTheme.primaryBlue),
              ),
              title: const Text('Tomar Foto con la Cámara'),
              onTap: () {
                Navigator.pop(ctx);
                onPickImage(ImageSource.camera);
              },
            ),
            ListTile(
              leading: const CircleAvatar(
                backgroundColor: Color(0xFFF0FDF4),
                child: Icon(Icons.photo_library_rounded, color: Colors.green),
              ),
              title: const Text('Seleccionar de la Galería'),
              onTap: () {
                Navigator.pop(ctx);
                onPickImage(ImageSource.gallery);
              },
            ),
            if (onAddSample != null) ...[
              const Divider(height: 24),
              const Text(
                'O selecciona una foto de prueba rápida:',
                style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.grey),
              ),
              const SizedBox(height: 10),
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: sampleEvidences.map((sample) {
                    return Padding(
                      padding: const EdgeInsets.only(right: 10),
                      child: InkWell(
                        onTap: () {
                          Navigator.pop(ctx);
                          onAddSample!(sample['url']!);
                        },
                        borderRadius: BorderRadius.circular(10),
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.blue.shade200),
                            borderRadius: BorderRadius.circular(10),
                            color: Colors.blue.shade50,
                          ),
                          child: Row(
                            children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(6),
                                child: Image.network(
                                  sample['url']!,
                                  width: 32,
                                  height: 32,
                                  fit: BoxFit.cover,
                                  errorBuilder: (ctx, error, stackTrace) => const Icon(Icons.image, size: 20),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                sample['title']!,
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Row(
              children: [
                Icon(Icons.photo_camera_outlined, size: 18, color: AppTheme.primaryBlue),
                SizedBox(width: 6),
                Text(
                  'Evidencia Fotográfica (HU6)',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                ),
              ],
            ),
            if (!isReadOnly)
              TextButton.icon(
                onPressed: () => _showImageSourceDialog(context),
                icon: const Icon(Icons.add_a_photo_outlined, size: 16),
                label: const Text('Agregar'),
                style: TextButton.styleFrom(
                  foregroundColor: AppTheme.primaryBlue,
                ),
              ),
          ],
        ),
        const SizedBox(height: 8),
        if (photos.isEmpty)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
            decoration: BoxDecoration(
              color: Colors.grey.shade50,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade300, style: BorderStyle.solid),
            ),
            child: Column(
              children: [
                Icon(Icons.image_not_supported_outlined, size: 36, color: Colors.grey.shade400),
                const SizedBox(height: 8),
                Text(
                  isReadOnly
                      ? 'No se adjuntó ninguna evidencia fotográfica'
                      : 'Presiona "Agregar" para tomar o adjuntar fotos del inconveniente.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
                ),
              ],
            ),
          )
        else
          SizedBox(
            height: 110,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: photos.length,
              itemBuilder: (context, index) {
                final item = photos[index];
                final isNetwork = item.startsWith('http://') || item.startsWith('https://');

                return Container(
                  margin: const EdgeInsets.only(right: 12),
                  child: Stack(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          width: 110,
                          height: 110,
                          color: Colors.grey.shade200,
                          child: isNetwork
                              ? Image.network(
                                  item,
                                  fit: BoxFit.cover,
                                  errorBuilder: (ctx, err, stack) => const Center(
                                    child: Icon(Icons.broken_image, color: Colors.grey),
                                  ),
                                )
                              : (kIsWeb
                                  ? Image.network(item, fit: BoxFit.cover)
                                  : Image.file(
                                      File(item),
                                      fit: BoxFit.cover,
                                      errorBuilder: (ctx, err, stack) => const Center(
                                        child: Icon(Icons.image, color: Colors.grey),
                                      ),
                                    )),
                        ),
                      ),
                      if (!isReadOnly && onRemoveImage != null)
                        Positioned(
                          top: 4,
                          right: 4,
                          child: InkWell(
                            onTap: () => onRemoveImage!(index),
                            child: Container(
                              padding: const EdgeInsets.all(4),
                              decoration: const BoxDecoration(
                                color: Color(0x99000000),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.close_rounded,
                                size: 14,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ),
                    ],
                  ),
                );
              },
            ),
          ),
      ],
    );
  }
}
