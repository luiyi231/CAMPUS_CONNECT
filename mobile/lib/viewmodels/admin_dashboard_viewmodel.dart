import 'package:flutter/foundation.dart';
import '../models/solicitud_model.dart';
import '../models/estado_solicitud_model.dart';
import '../services/database_service.dart';

class AdminDashboardViewModel extends ChangeNotifier {
  final DatabaseService _db;
  String _selectedStatusFilter = 'TODAS';
  String _searchQuery = '';

  AdminDashboardViewModel(this._db) {
    _db.addListener(notifyListeners);
  }

  @override
  void dispose() {
    _db.removeListener(notifyListeners);
    super.dispose();
  }

  String get selectedStatusFilter => _selectedStatusFilter;
  String get searchQuery => _searchQuery;

  // HU1 Metrics
  int get totalSolicitudes => _db.totalSolicitudes;
  int get countRecibida => _db.countByEstado('RECIBIDA');
  int get countPendiente => _db.countByEstado('PENDIENTE');
  int get countEnProceso => _db.countByEstado('EN-PROCESO');
  int get countCompletada => _db.countByEstado('COMPLETADA');

  void setStatusFilter(String status) {
    _selectedStatusFilter = status;
    notifyListeners();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  List<Solicitud> get filteredSolicitudes {
    List<Solicitud> list = List.from(_db.solicitudes);

    if (_selectedStatusFilter != 'TODAS') {
      final estado = _db.estadosSolicitud.firstWhere(
        (e) => e.nombre.toUpperCase() == _selectedStatusFilter.toUpperCase(),
        orElse: () => EstadoSolicitud(idEstado: -1, nombre: ''),
      );
      if (estado.idEstado != -1) {
        list = list.where((s) => s.idEstado == estado.idEstado).toList();
      }
    }

    if (_searchQuery.trim().isNotEmpty) {
      final q = _searchQuery.trim().toLowerCase();
      list = list.where((s) {
        final est = getEstudianteName(s.idEstudiante).toLowerCase();
        final tipo = getTipoName(s.idTipo).toLowerCase();
        final desc = s.descripcion.toLowerCase();
        final idStr = '#${s.idSolicitud}';
        return est.contains(q) || tipo.contains(q) || desc.contains(q) || idStr.contains(q);
      }).toList();
    }

    return list;
  }

  String getEstudianteName(int idEstudiante) {
    final user = _db.getUsuarioById(idEstudiante);
    return user != null ? user.nombreCompleto : 'Estudiante #$idEstudiante';
  }

  String getResponsableName(int? idResponsable) {
    if (idResponsable == null) return 'Sin asignar';
    final user = _db.getUsuarioById(idResponsable);
    return user != null ? user.nombreCompleto : 'Responsable #$idResponsable';
  }

  String getTipoName(int idTipo) {
    final tipo = _db.getTipoById(idTipo);
    return tipo != null ? tipo.nombre : 'Tipo #$idTipo';
  }

  EstadoSolicitud getEstado(int idEstado) {
    return _db.getEstadoById(idEstado) ?? EstadoSolicitud(idEstado: idEstado, nombre: 'DESCONOCIDO');
  }

  int getEvidenciaCount(int idSolicitud) {
    return _db.getEvidenciasForSolicitud(idSolicitud).length;
  }
}
