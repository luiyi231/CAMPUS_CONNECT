import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import '../models/tipo_solicitud_model.dart';
import '../services/auth_service.dart';
import '../services/database_service.dart';

class CreateRequestViewModel extends ChangeNotifier {
  final DatabaseService _db;
  final AuthService _authService;
  final ImagePicker _picker = ImagePicker();

  int? _selectedTipoId;
  String _descripcion = '';
  final List<String> _attachedPhotos = [];
  bool _isSubmitting = false;
  String? _errorMessage;

  CreateRequestViewModel(this._db, this._authService) {
    if (_db.tiposSolicitud.isNotEmpty) {
      _selectedTipoId = _db.tiposSolicitud.first.idTipo;
    }
  }

  int? get selectedTipoId => _selectedTipoId;
  String get descripcion => _descripcion;
  List<String> get attachedPhotos => List.unmodifiable(_attachedPhotos);
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;

  List<TipoSolicitud> get tiposSolicitud => _db.tiposSolicitud;

  void setSelectedTipoId(int? id) {
    _selectedTipoId = id;
    notifyListeners();
  }

  void setDescripcion(String val) {
    _descripcion = val;
    notifyListeners();
  }

  // HU6: Pick photo from Camera or Gallery
  Future<void> pickPhoto(ImageSource source) async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: source,
        imageQuality: 80,
      );
      if (photo != null) {
        _attachedPhotos.add(photo.path);
        notifyListeners();
      }
    } catch (e) {
      _errorMessage = 'Error al seleccionar imagen: $e';
      notifyListeners();
    }
  }

  // HU6: Add preset mock sample photo URL if camera isn't available
  void addSamplePhoto(String photoUrl) {
    _attachedPhotos.add(photoUrl);
    notifyListeners();
  }

  void removePhoto(int index) {
    if (index >= 0 && index < _attachedPhotos.length) {
      _attachedPhotos.removeAt(index);
      notifyListeners();
    }
  }

  Future<bool> submitRequest() async {
    if (_selectedTipoId == null) {
      _errorMessage = 'Seleccione un tipo de solicitud.';
      notifyListeners();
      return false;
    }

    if (_descripcion.trim().isEmpty) {
      _errorMessage = 'Ingrese una descripción para la solicitud.';
      notifyListeners();
      return false;
    }

    final currentUserId = _authService.usuarioActual?.idUsuario;
    if (currentUserId == null) {
      _errorMessage = 'Debe iniciar sesión como estudiante para crear una solicitud.';
      notifyListeners();
      return false;
    }

    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _db.registrarSolicitud(
        idEstudiante: currentUserId,
        idTipo: _selectedTipoId!,
        descripcion: _descripcion.trim(),
        fotosArchivos: _attachedPhotos,
      );

      _isSubmitting = false;
      resetForm();
      return true;
    } catch (e) {
      _isSubmitting = false;
      _errorMessage = 'Error al registrar solicitud: $e';
      notifyListeners();
      return false;
    }
  }

  void resetForm() {
    if (_db.tiposSolicitud.isNotEmpty) {
      _selectedTipoId = _db.tiposSolicitud.first.idTipo;
    }
    _descripcion = '';
    _attachedPhotos.clear();
    _errorMessage = null;
    _isSubmitting = false;
    notifyListeners();
  }
}
