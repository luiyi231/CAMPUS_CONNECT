import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../viewmodels/admin_dashboard_viewmodel.dart';
import '../../viewmodels/auth_viewmodel.dart';
import '../auth/login_screen.dart';
import '../common/request_detail_screen.dart';
import '../widgets/status_badge.dart';
import '../widgets/summary_card.dart';

class AdminDashboardScreen extends StatelessWidget {
  const AdminDashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authVm = context.watch<AuthViewModel>();
    final adminVm = context.watch<AdminDashboardViewModel>();
    final dateFormat = DateFormat('dd/MM/yyyy HH:mm');

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Administrador (HU1)'),
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
      body: CustomScrollView(
        slivers: [
          // Header Welcome Banner
          SliverToBoxAdapter(
            child: Container(
              padding: const EdgeInsets.all(16),
              color: AppTheme.primaryBlue,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const CircleAvatar(
                        backgroundColor: Colors.white24,
                        child: Icon(Icons.admin_panel_settings, color: Colors.white),
                      ),
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Hola, ${authVm.currentUser?.nombreCompleto ?? 'Administrador'}',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const Text(
                            'Control general de solicitudes del Campus',
                            style: TextStyle(color: Colors.white70, fontSize: 12),
                          ),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // HU1: Resumen Dashboard (Contadores por estado)
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Resumen de Solicitudes (HU1)',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.primaryBlue,
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Summary cards grid
                  GridView.count(
                    crossAxisCount: 2,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    childAspectRatio: 1.6,
                    mainAxisSpacing: 10,
                    crossAxisSpacing: 10,
                    children: [
                      SummaryCard(
                        title: 'Total Solicitudes',
                        count: adminVm.totalSolicitudes,
                        icon: Icons.assignment_rounded,
                        color: AppTheme.primaryBlue,
                        isSelected: adminVm.selectedStatusFilter == 'TODAS',
                        onTap: () => adminVm.setStatusFilter('TODAS'),
                      ),
                      SummaryCard(
                        title: 'RECIBIDAS',
                        count: adminVm.countRecibida,
                        icon: AppTheme.getStatusIcon('RECIBIDA'),
                        color: AppTheme.statusRecibida,
                        isSelected: adminVm.selectedStatusFilter == 'RECIBIDA',
                        onTap: () => adminVm.setStatusFilter('RECIBIDA'),
                      ),
                      SummaryCard(
                        title: 'PENDIENTES',
                        count: adminVm.countPendiente,
                        icon: AppTheme.getStatusIcon('PENDIENTE'),
                        color: AppTheme.statusPendiente,
                        isSelected: adminVm.selectedStatusFilter == 'PENDIENTE',
                        onTap: () => adminVm.setStatusFilter('PENDIENTE'),
                      ),
                      SummaryCard(
                        title: 'EN-PROCESO',
                        count: adminVm.countEnProceso,
                        icon: AppTheme.getStatusIcon('EN-PROCESO'),
                        color: AppTheme.statusEnProceso,
                        isSelected: adminVm.selectedStatusFilter == 'EN-PROCESO',
                        onTap: () => adminVm.setStatusFilter('EN-PROCESO'),
                      ),
                      SummaryCard(
                        title: 'COMPLETADAS',
                        count: adminVm.countCompletada,
                        icon: AppTheme.getStatusIcon('COMPLETADA'),
                        color: AppTheme.statusCompletada,
                        isSelected: adminVm.selectedStatusFilter == 'COMPLETADA',
                        onTap: () => adminVm.setStatusFilter('COMPLETADA'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // Search & Section Title
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Divider(height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Listado de Solicitudes (${adminVm.filteredSolicitudes.length})',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: AppTheme.primaryBlue,
                        ),
                      ),
                      if (adminVm.selectedStatusFilter != 'TODAS')
                        Chip(
                          label: Text(adminVm.selectedStatusFilter),
                          onDeleted: () => adminVm.setStatusFilter('TODAS'),
                          deleteIconColor: Colors.red,
                        ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    onChanged: adminVm.setSearchQuery,
                    decoration: InputDecoration(
                      hintText: 'Buscar por estudiante, tipo o descripción...',
                      prefixIcon: const Icon(Icons.search),
                      suffixIcon: adminVm.searchQuery.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () => adminVm.setSearchQuery(''),
                            )
                          : null,
                    ),
                  ),
                  const SizedBox(height: 12),
                ],
              ),
            ),
          ),

          // Solicitudes List
          if (adminVm.filteredSolicitudes.isEmpty)
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(32.0),
                child: Column(
                  children: [
                    Icon(Icons.inbox_outlined, size: 48, color: Colors.grey.shade400),
                    const SizedBox(height: 12),
                    Text(
                      'No hay solicitudes registradas con este filtro',
                      style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
                    ),
                  ],
                ),
              ),
            )
          else
            SliverList(
              delegate: SliverChildBuilderDelegate(
                (context, index) {
                  final sol = adminVm.filteredSolicitudes[index];
                  final estNombre = adminVm.getEstudianteName(sol.idEstudiante);
                  final respNombre = adminVm.getResponsableName(sol.idResponsable);
                  final tipoNombre = adminVm.getTipoName(sol.idTipo);
                  final estadoObj = adminVm.getEstado(sol.idEstado);
                  final numEvidencias = adminVm.getEvidenciaCount(sol.idSolicitud);

                  return Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 6.0),
                    child: Card(
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
                                  color: Colors.black87,
                                ),
                              ),
                              const SizedBox(height: 12),
                              const Divider(height: 1),
                              const SizedBox(height: 8),
                              Row(
                                children: [
                                  const Icon(Icons.person_outline, size: 16, color: Colors.grey),
                                  const SizedBox(width: 4),
                                  Expanded(
                                    child: Text(
                                      'Estudiante: $estNombre',
                                      style: const TextStyle(fontSize: 12, color: Colors.grey),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 4),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Row(
                                    children: [
                                      Icon(
                                        Icons.engineering_outlined,
                                        size: 16,
                                        color: sol.idResponsable == null ? Colors.orange : AppTheme.primaryBlue,
                                      ),
                                      const SizedBox(width: 4),
                                      Text(
                                        'Encargado: $respNombre',
                                        style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: sol.idResponsable == null ? FontWeight.normal : FontWeight.bold,
                                          color: sol.idResponsable == null ? Colors.orange.shade800 : AppTheme.primaryBlue,
                                        ),
                                      ),
                                    ],
                                  ),
                                  if (numEvidencias > 0)
                                    Row(
                                      children: [
                                        const Icon(Icons.attach_file, size: 14, color: Colors.blue),
                                        Text(
                                          '$numEvidencias foto(s)',
                                          style: const TextStyle(fontSize: 11, color: Colors.blue, fontWeight: FontWeight.bold),
                                        ),
                                      ],
                                    ),
                                ],
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'Fecha: ${dateFormat.format(sol.fechaCreacion)}',
                                style: const TextStyle(fontSize: 11, color: Colors.grey),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  );
                },
                childCount: adminVm.filteredSolicitudes.length,
              ),
            ),

          const SliverPadding(padding: EdgeInsets.only(bottom: 24)),
        ],
      ),
    );
  }
}
