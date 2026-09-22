import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../viewmodels/auth_viewmodel.dart';
import '../../viewmodels/student_requests_viewmodel.dart';
import '../auth/login_screen.dart';
import '../common/request_detail_screen.dart';
import '../student/create_request_screen.dart';
import '../widgets/status_badge.dart';

class StudentRequestsScreen extends StatelessWidget {
  const StudentRequestsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authVm = context.watch<AuthViewModel>();
    final studentVm = context.watch<StudentRequestsViewModel>();
    final dateFormat = DateFormat('dd/MM/yyyy HH:mm');

    return Scaffold(
      appBar: AppBar(
        title: const Text('Mis Solicitudes (HU4)'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout_rounded),
            tooltip: 'Cerrar Sesión / Cambiar Rol',
            onPressed: () {
              authVm.logout();
              Navigator.of(context).pushReplacement(
                MaterialPageRoute(builder: (_) => const LoginScreen()),
              );
            },
          ),
        ],
      ),
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Student Info
          Container(
            padding: const EdgeInsets.all(16),
            color: AppTheme.primaryBlue,
            child: Row(
              children: [
                const CircleAvatar(
                  backgroundColor: Colors.white24,
                  child: Icon(Icons.person, color: Colors.white),
                ),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Estudiante: ${authVm.currentUser?.nombreCompleto ?? 'Usuario'}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      authVm.currentUser?.correo ?? '',
                      style: const TextStyle(color: Colors.white70, fontSize: 12),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Status Filters Tab (HU4)
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              children: [
                'TODAS',
                'RECIBIDA',
                'PENDIENTE',
                'EN-PROCESO',
                'COMPLETADA',
              ].map((status) {
                final isSelected = studentVm.selectedStatusFilter == status;
                return Padding(
                  padding: const EdgeInsets.only(right: 8.0),
                  child: FilterChip(
                    label: Text(status),
                    selected: isSelected,
                    selectedColor: AppTheme.getStatusColor(status == 'TODAS' ? 'RECIBIDA' : status).withOpacity(0.2),
                    checkmarkColor: AppTheme.getStatusColor(status == 'TODAS' ? 'RECIBIDA' : status),
                    labelStyle: TextStyle(
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      color: isSelected
                          ? AppTheme.getStatusColor(status == 'TODAS' ? 'RECIBIDA' : status)
                          : Colors.black87,
                    ),
                    onSelected: (_) => studentVm.setStatusFilter(status),
                  ),
                );
              }).toList(),
            ),
          ),

          const Divider(height: 1),

          // Requests List
          Expanded(
            child: studentVm.mySolicitudes.isEmpty
                ? Center(
                    child: Padding(
                      padding: const EdgeInsets.all(32.0),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.assignment_outlined, size: 56, color: Colors.grey.shade400),
                          const SizedBox(height: 12),
                          const Text(
                            'No tienes solicitudes en esta categoría',
                            style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            'Presiona el botón "+" para registrar una nueva solicitud.',
                            textAlign: TextAlign.center,
                            style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
                          ),
                        ],
                      ),
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    itemCount: studentVm.mySolicitudes.length,
                    itemBuilder: (context, index) {
                      final sol = studentVm.mySolicitudes[index];
                      final tipoNombre = studentVm.getTipoName(sol.idTipo);
                      final estadoObj = studentVm.getEstado(sol.idEstado);
                      final respNombre = studentVm.getResponsableName(sol.idResponsable);
                      final numEvidencias = studentVm.getEvidenciaCount(sol.idSolicitud);

                      return Card(
                        margin: const EdgeInsets.only(bottom: 12),
                        child: InkWell(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => RequestDetailScreen(idSolicitud: sol.idSolicitud),
                              ),
                            );
                          },
                          borderRadius: BorderRadius.circular(16),
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: Colors.blue.shade50,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Text(
                                        '#${sol.idSolicitud} • $tipoNombre',
                                        style: TextStyle(
                                          color: Colors.blue.shade900,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 12,
                                        ),
                                      ),
                                    ),
                                    StatusBadge(estadoNombre: estadoObj.nombre),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                Text(
                                  sol.descripcion,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                                const SizedBox(height: 12),
                                const Divider(height: 1),
                                const SizedBox(height: 8),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Row(
                                      children: [
                                        const Icon(Icons.person_outline, size: 14, color: Colors.grey),
                                        const SizedBox(width: 4),
                                        Text(
                                          'Atendido por: $respNombre',
                                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                                        ),
                                      ],
                                    ),
                                    if (numEvidencias > 0)
                                      Row(
                                        children: [
                                          const Icon(Icons.photo_library_outlined, size: 14, color: Colors.blue),
                                          const SizedBox(width: 4),
                                          Text(
                                            '$numEvidencias foto(s)',
                                            style: const TextStyle(fontSize: 11, color: Colors.blue, fontWeight: FontWeight.bold),
                                          ),
                                        ],
                                      ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Registrada el: ${dateFormat.format(sol.fechaCreacion)}',
                                  style: const TextStyle(fontSize: 11, color: Colors.grey),
                                ),
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const CreateRequestScreen()),
          );
        },
        icon: const Icon(Icons.add_rounded),
        label: const Text('NUEVA SOLICITUD'),
      ),
    );
  }
}
