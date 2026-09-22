import 'package:flutter/foundation.dart';
import '../models/usuario_model.dart';
import 'database_service.dart';

class AuthService extends ChangeNotifier {
  final DatabaseService _db;
  Usuario? _usuarioActual;

  AuthService(this._db) {
    // Usuario por defecto para prueba inmediata (Carlos Mendoza - Estudiante)
    if (_db.usuarios.isNotEmpty) {
      _usuarioActual = _db.usuarios.first;
    }
  }

  Usuario? get usuarioActual => _usuarioActual;
  bool get isAuthenticated => _usuarioActual != null;
  bool get isAdmin => _usuarioActual?.isAdmin ?? false;
  bool get isEstudiante => _usuarioActual?.isEstudiante ?? false;

  void setUsuarioActual(Usuario usuario) {
    _usuarioActual = usuario;
    notifyListeners();
  }

  void logout() {
    _usuarioActual = null;
    notifyListeners();
  }

  bool login(String correo, String contrasena) {
    try {
      final user = _db.usuarios.firstWhere(
        (u) => u.correo.toLowerCase() == correo.trim().toLowerCase() && u.contrasena == contrasena,
      );
      _usuarioActual = user;
      notifyListeners();
      return true;
    } catch (_) {
      return false;
    }
  }
}
