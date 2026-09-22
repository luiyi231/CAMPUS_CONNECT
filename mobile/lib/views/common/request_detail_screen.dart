import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../services/auth_service.dart';
import '../../services/database_service.dart';
import '../../theme/app_theme.dart';
import '../../viewmodels/request_detail_viewmodel.dart';
import '../admin/assign_responsible_dialog.dart';
import '../widgets/evidence_picker.dart';
import '../widgets/status_badge.dart';

class RequestDetailScreen extends StatelessWidget {
  final int idSolicitud;

  const RequestDetailScreen({super.key, required this.idSolicitud});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<RequestDetailViewModel>(
      create: (ctx) => RequestDetailViewModel(
        ctx.read<DatabaseService>(),
        ctx.read<AuthService>(),
        idSolicitud,
      ),
      child: const _RequestDetailContent(),
    );
  }
}

class _RequestDetailContent extends StatelessWidget {
  const _RequestDetailContent();

  @override
  Widget build(BuildContext context) {
    final detailVm = context.watch<RequestDetailViewModel>();
    final dateFormat = DateFormat('dd/MM/yyyy HH:mm');
    final sol = detailVm.solicitud;

    if (sol == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Detalle de Solicitud')),
        body: const Center(child: Text('Solicitud no encontrada.')),
      );
    }

    final estadoActualObj = detailVm.estadoActual;
    final estudianteObj = detailVm.estudiante;
    final responsableObj = detailVm.responsable;
    final tipoObj = detailVm.tipo;

    return Scaffold(
      appBar: AppBar(
        title: Text('Solicitud #${sol.idSolicitud}'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Status Header Card
            Card(
              color: AppTheme.getStatusColor(estadoActualObj?.nombre ?? '').withOpacity(0.08),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'TIPO: ${tipoObj?.nombre.toUpperCase() ?? ''}',
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: Colors.black54,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Estado Actual:',
                          style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                        ),
                      ],
                    ),
                    StatusBadge(
                      estadoNombre: estadoActualObj?.nombre ?? 'RECIBIDA',
                      isLarge: true,
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Description Card
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.description_outlined, size: 18, color: AppTheme.primaryBlue),
                        SizedBox(width: 8),
                        Text(
                          'Descripción',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Text(
                      sol.descripcion,
                      style: const TextStyle(fontSize: 14, height: 1.4),
                    ),
                    const SizedBox(height: 12),
                    const Divider(),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        const Icon(Icons.person_outline, size: 16, color: Colors.grey),
                        const SizedBox(width: 6),
                        Text(
                          'Registrado por: ${estudianteObj?.nombreCompleto ?? 'Estudiante'} (${estudianteObj?.correo ?? ''})',
                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        const Icon(Icons.access_time_rounded, size: 16, color: Colors.grey),
                        const SizedBox(width: 6),
                        Text(
                          'Fecha de registro: ${dateFormat.format(sol.fechaCreacion)}',
                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // HU2: Responsible Section (Admin can assign, Student can view)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.engineering_outlined, size: 18, color: AppTheme.primaryBlue),
                            SizedBox(width: 8),
                            Text(
                              'Responsable Asignado (HU2)',
                              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                        if (detailVm.currentUserIsAdmin)
                          ElevatedButton.icon(
                            onPressed: () {
                              showDialog(
                                context: context,
                                builder: (_) => AssignResponsibleDialog(
                                  responsables: detailVm.posiblesResponsables,
                                  currentResponsableId: sol.idResponsable,
                                  onAssign: (selectedId) {
                                    detailVm.asignarResponsable(selectedId);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text('Responsable asignado correctamente.'),
                                        backgroundColor: Colors.green,
                                      ),
                                    );
                                  },
                                ),
                              );
                            },
                            icon: const Icon(Icons.person_add_alt_outlined, size: 16),
                            label: Text(sol.idResponsable == null ? 'Asignar' : 'Cambiar'),
                            style: ElevatedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    if (responsableObj == null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.amber.shade50,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: Colors.amber.shade200),
                        ),
                        child: const Row(
                          children: [
                            Icon(Icons.warning_amber_rounded, color: Colors.amber, size: 20),
                            SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Aún no se ha asignado un responsable para esta solicitud.',
                                style: TextStyle(fontSize: 13, color: Colors.amber),
                              ),
                            ),
                          ],
                        ),
                      )
                    else
                      ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: const CircleAvatar(
                          backgroundColor: Color(0xFFEFF6FF),
                          child: Icon(Icons.badge_outlined, color: AppTheme.primaryBlue),
                        ),
                        title: Text(
                          responsableObj.nombreCompleto,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                        subtitle: Text('${responsableObj.rol} • ${responsableObj.correo}'),
                      ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // HU5: Admin Action: Update Status
            if (detailVm.currentUserIsAdmin) ...[
              Card(
                color: Colors.blue.shade50.withOpacity(0.5),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Row(
                        children: [
                          Icon(Icons.edit_attributes_rounded, size: 20, color: AppTheme.primaryBlue),
                          SizedBox(width: 8),
                          Text(
                            'Actualizar Estado de Solicitud (HU5)',
                            style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: detailVm.estadosDisponibles.map((e) {
                          final isSelected = sol.idEstado == e.idEstado;
                          final color = AppTheme.getStatusColor(e.nombre);
                          return ChoiceChip(
                            label: Text(e.nombre),
                            selected: isSelected,
                            selectedColor: color,
                            backgroundColor: Colors.white,
                            labelStyle: TextStyle(
                              color: isSelected ? Colors.white : color,
                              fontWeight: FontWeight.bold,
                            ),
                            onSelected: isSelected
                                ? null
                                : (_) {
                                    detailVm.actualizarEstado(e.idEstado);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                        content: Text('Estado actualizado a ${e.nombre}'),
                                        backgroundColor: color,
                                      ),
                                    );
                                  },
                          );
                        }).toList(),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
            ],

            // HU6: Photo Evidence Gallery & Attachments
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: EvidencePicker(
                  photos: detailVm.evidencias.map((e) => e.archivo).toList(),
                  onPickImage: detailVm.addPhotoEvidence,
                  onAddSample: detailVm.addSamplePhotoEvidence,
                  isReadOnly: false, // Students and Admins can add evidence
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Timeline / Historial de Cambios de Estado
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.history_rounded, size: 18, color: AppTheme.primaryBlue),
                        SizedBox(width: 8),
                        Text(
                          'Historial de Cambios de Estado',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    if (detailVm.historial.isEmpty)
                      const Text('Sin historial de cambios registrado.')
                    else
                      ListView.separated(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: detailVm.historial.length,
                        separatorBuilder: (_, __) => const Divider(height: 12),
                        itemBuilder: (context, index) {
                          final hist = detailVm.historial[index];
                          final estadoNombre = detailVm.getEstadoName(hist.idEstado);
                          final usuarioNombre = detailVm.getUserName(hist.idUsuario);

                          return Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Padding(
                                padding: const EdgeInsets.only(top: 2),
                                child: StatusBadge(estadoNombre: estadoNombre),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      usuarioNombre,
                                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                                    ),
                                    Text(
                                      dateFormat.format(hist.fechaCambio),
                                      style: const TextStyle(fontSize: 11, color: Colors.grey),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
