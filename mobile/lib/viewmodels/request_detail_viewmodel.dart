import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import '../models/solicitud_model.dart';
import '../models/usuario_model.dart';
import '../models/tipo_solicitud_model.dart';
import '../models/estado_solicitud_model.dart';
import '../models/evidencia_model.dart';
import '../models/historial_estado_model.dart';
import '../services/auth_service.dart';
import '../services/database_service.dart';

class RequestDetailViewModel extends ChangeNotifier {
  final DatabaseService _db;
  final AuthService _authService;
  final int idSolicitud;
  final ImagePicker _picker = ImagePicker();

  RequestDetailViewModel(this._db, this._authService, this.idSolicitud) {
    _db.addListener(notifyListeners);
  }

  @override
  void dispose() {
    _db.removeListener(notifyListeners);
    super.dispose();
  }

  Solicitud? get solicitud {
    try {
      return _db.solicitudes.firstWhere((s) => s.idSolicitud == idSolicitud);
    } catch (_) {
      return null;
    }
  }

  Usuario? get estudiante {
    final sol = solicitud;
    return sol != null ? _db.getUsuarioById(sol.idEstudiante) : null;
  }

  Usuario? get responsable {
    final sol = solicitud;
    return sol != null ? _db.getUsuarioById(sol.idResponsable) : null;
  }

  TipoSolicitud? get tipo {
    final sol = solicitud;
    return sol != null ? _db.getTipoById(sol.idTipo) : null;
  }

  EstadoSolicitud? get estadoActual {
    final sol = solicitud;
    return sol != null ? _db.getEstadoById(sol.idEstado) : null;
  }

  List<Evidencia> get evidencias => _db.getEvidenciasForSolicitud(idSolicitud);
  List<HistorialEstado> get historial => _db.getHistorialForSolicitud(idSolicitud);
  List<Usuario> get posiblesResponsables => _db.responsables;
  List<EstadoSolicitud> get estadosDisponibles => _db.estadosSolicitud;

  bool get currentUserIsAdmin => _authService.isAdmin;

  String getUserName(int idUsuario) {
    final user = _db.getUsuarioById(idUsuario);
    return user != null ? '${user.nombreCompleto} (${user.rol})' : 'Usuario #$idUsuario';
  }

  String getEstadoName(int idEstado) {
    final est = _db.getEstadoById(idEstado);
    return est != null ? est.nombre : 'Estado #$idEstado';
  }

  // HU2: Asignar Responsable
  bool asignarResponsable(int idResponsable) {
    final ok = _db.asignarResponsable(
      idSolicitud: idSolicitud,
      idResponsable: idResponsable,
    );
    notifyListeners();
    return ok;
  }

  // HU5: Actualizar Estado (RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA)
  bool actualizarEstado(int nuevoIdEstado) {
    final currentUser = _authService.usuarioActual;
    if (currentUser == null) return false;

    final ok = _db.actualizarEstado(
      idSolicitud: idSolicitud,
      nuevoIdEstado: nuevoIdEstado,
      idUsuarioAccion: currentUser.idUsuario,
    );
    notifyListeners();
    return ok;
  }

  // HU6: Adjuntar Evidencia Adicional
  Future<void> addPhotoEvidence(ImageSource source) async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: source,
        imageQuality: 80,
      );
      if (photo != null) {
        _db.agregarEvidencia(
          idSolicitud: idSolicitud,
          archivo: photo.path,
        );
        notifyListeners();
      }
    } catch (_) {}
  }

  void addSamplePhotoEvidence(String photoUrl) {
    _db.agregarEvidencia(
      idSolicitud: idSolicitud,
      archivo: photoUrl,
    );
    notifyListeners();
  }
}
