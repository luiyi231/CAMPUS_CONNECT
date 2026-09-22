import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../viewmodels/create_request_viewmodel.dart';
import '../widgets/evidence_picker.dart';

class CreateRequestScreen extends StatelessWidget {
  const CreateRequestScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final createVm = context.watch<CreateRequestViewModel>();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Registrar Solicitud (HU3)'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Header Info Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue.shade50,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.blue.shade200),
              ),
              child: const Row(
                children: [
                  Icon(Icons.info_outline, color: AppTheme.primaryBlue),
                  SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      'Reporte un inconveniente o reclamo a la universidad para iniciar su atención.',
                      style: TextStyle(fontSize: 13, color: AppTheme.primaryBlue, fontWeight: FontWeight.w500),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // HU3: Selector de Tipo de Solicitud (Mantenimiento, soporte, infraestructura, otro)
            const Text(
              'Tipo de Solicitud (HU3)',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
            ),
            const SizedBox(height: 8),
            DropdownButtonFormField<int>(
              initialValue: createVm.selectedTipoId,
              isExpanded: true,
              decoration: const InputDecoration(
                prefixIcon: Icon(Icons.category_outlined),
                hintText: 'Seleccione un tipo',
              ),
              items: createVm.tiposSolicitud.map((tipo) {
                return DropdownMenuItem<int>(
                  value: tipo.idTipo,
                  child: Text(
                    tipo.nombre,
                    style: const TextStyle(fontWeight: FontWeight.bold),
                  ),
                );
              }).toList(),
              onChanged: createVm.setSelectedTipoId,
            ),
            const SizedBox(height: 20),

            // Descripción
            const Text(
              'Descripción detallada del inconveniente',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
            ),
            const SizedBox(height: 8),
            TextField(
              maxLines: 4,
              onChanged: createVm.setDescripcion,
              decoration: const InputDecoration(
                hintText: 'Ej. El aire acondicionado del aula 101 no enciende y gotea agua...',
                alignLabelWithHint: true,
              ),
            ),
            const SizedBox(height: 24),

            // HU6: Adjuntar evidencia fotográfica
            EvidencePicker(
              photos: createVm.attachedPhotos,
              onPickImage: createVm.pickPhoto,
              onAddSample: createVm.addSamplePhoto,
              onRemoveImage: createVm.removePhoto,
            ),

            if (createVm.errorMessage != null) ...[
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Colors.red, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        createVm.errorMessage!,
                        style: const TextStyle(color: Colors.red, fontSize: 13),
                      ),
                    ),
                  ],
                ),
              ),
            ],

            const SizedBox(height: 32),

            // Submit Button
            ElevatedButton(
              onPressed: createVm.isSubmitting
                  ? null
                  : () async {
                      final success = await createVm.submitRequest();
                      if (success && context.mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('¡Solicitud registrada exitosamente!'),
                            backgroundColor: Colors.green,
                          ),
                        );
                        Navigator.pop(context);
                      }
                    },
              child: createVm.isSubmitting
                  ? const CircularProgressIndicator(color: Colors.white)
                  : const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.send_rounded, size: 20),
                        SizedBox(width: 8),
                        Text('REGISTRAR SOLICITUD'),
                      ],
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
