import 'package:flutter/foundation.dart';
import '../models/solicitud_model.dart';
import '../models/estado_solicitud_model.dart';
import '../services/auth_service.dart';
import '../services/database_service.dart';

class StudentRequestsViewModel extends ChangeNotifier {
  final DatabaseService _db;
  final AuthService _authService;

  String _selectedStatusFilter = 'TODAS';

  StudentRequestsViewModel(this._db, this._authService) {
    _db.addListener(notifyListeners);
    _authService.addListener(notifyListeners);
  }

  @override
  void dispose() {
    _db.removeListener(notifyListeners);
    _authService.removeListener(notifyListeners);
    super.dispose();
  }

  String get selectedStatusFilter => _selectedStatusFilter;

  void setStatusFilter(String status) {
    _selectedStatusFilter = status;
    notifyListeners();
  }

  List<Solicitud> get mySolicitudes {
    final currentUserId = _authService.usuarioActual?.idUsuario;
    if (currentUserId == null) return [];

    List<Solicitud> list = _db.solicitudes
        .where((s) => s.idEstudiante == currentUserId)
        .toList();

    if (_selectedStatusFilter != 'TODAS') {
      final estado = _db.estadosSolicitud.firstWhere(
        (e) => e.nombre.toUpperCase() == _selectedStatusFilter.toUpperCase(),
        orElse: () => EstadoSolicitud(idEstado: -1, nombre: ''),
      );
      if (estado.idEstado != -1) {
        list = list.where((s) => s.idEstado == estado.idEstado).toList();
      }
    }

    return list;
  }

  String getTipoName(int idTipo) {
    final tipo = _db.getTipoById(idTipo);
    return tipo != null ? tipo.nombre : 'Tipo #$idTipo';
  }

  EstadoSolicitud getEstado(int idEstado) {
    return _db.getEstadoById(idEstado) ?? EstadoSolicitud(idEstado: idEstado, nombre: 'DESCONOCIDO');
  }

  String getResponsableName(int? idResponsable) {
    if (idResponsable == null) return 'Pendiente de asignación';
    final user = _db.getUsuarioById(idResponsable);
    return user != null ? user.nombreCompleto : 'Responsable #$idResponsable';
  }

  int getEvidenciaCount(int idSolicitud) {
    return _db.getEvidenciasForSolicitud(idSolicitud).length;
  }
}
