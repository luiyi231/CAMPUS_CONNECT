import 'package:flutter/foundation.dart';
import '../models/usuario_model.dart';
import '../services/auth_service.dart';
import '../services/database_service.dart';

class AuthViewModel extends ChangeNotifier {
  final AuthService _authService;
  final DatabaseService _db;

  String _email = '';
  String _password = '';
  String? _errorMessage;
  bool _isLoading = false;

  AuthViewModel(this._authService, this._db) {
    _authService.addListener(notifyListeners);
  }

  @override
  void dispose() {
    _authService.removeListener(notifyListeners);
    super.dispose();
  }

  Usuario? get currentUser => _authService.usuarioActual;
  bool get isAuthenticated => _authService.isAuthenticated;
  bool get isAdmin => _authService.isAdmin;
  String? get errorMessage => _errorMessage;
  bool get isLoading => _isLoading;

  List<Usuario> get allUsers => _db.usuarios;

  void setEmail(String val) => _email = val;
  void setPassword(String val) => _password = val;

  bool login() {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final success = _authService.login(_email, _password);
    _isLoading = false;
    
    if (!success) {
      _errorMessage = 'Credenciales inválidas. Intente nuevamente.';
    }
    notifyListeners();
    return success;
  }

  void switchUserRole(Usuario user) {
    _authService.setUsuarioActual(user);
    notifyListeners();
  }

  void logout() {
    _authService.logout();
    notifyListeners();
  }
}
