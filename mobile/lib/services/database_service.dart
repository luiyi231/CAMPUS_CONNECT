import 'package:flutter/foundation.dart';
import '../models/usuario_model.dart';
import '../models/tipo_solicitud_model.dart';
import '../models/estado_solicitud_model.dart';
import '../models/solicitud_model.dart';
import '../models/evidencia_model.dart';
import '../models/historial_estado_model.dart';

class DatabaseService extends ChangeNotifier {
  // Tables
  final List<Usuario> _usuarios = [];
  final List<TipoSolicitud> _tiposSolicitud = [];
  final List<EstadoSolicitud> _estadosSolicitud = [];
  final List<Solicitud> _solicitudes = [];
  final List<Evidencia> _evidencias = [];
  final List<HistorialEstado> _historialEstados = [];

  DatabaseService() {
    _initSeedData();
  }

  // --- Seed Data Initialization ---
  void _initSeedData() {
    // 1. Estados de Solicitud
    _estadosSolicitud.addAll([
      EstadoSolicitud(idEstado: 1, nombre: 'RECIBIDA'),
      EstadoSolicitud(idEstado: 2, nombre: 'PENDIENTE'),
      EstadoSolicitud(idEstado: 3, nombre: 'EN-PROCESO'),
      EstadoSolicitud(idEstado: 4, nombre: 'COMPLETADA'),
    ]);

    // 2. Tipos de Solicitud (HU3)
    _tiposSolicitud.addAll([
      TipoSolicitud(idTipo: 1, nombre: 'Mantenimiento'),
      TipoSolicitud(idTipo: 2, nombre: 'Soporte'),
      TipoSolicitud(idTipo: 3, nombre: 'Infraestructura'),
      TipoSolicitud(idTipo: 4, nombre: 'Otro'),
    ]);

    // 3. Usuarios (Estudiantes, Administradores, Responsables)
    _usuarios.addAll([
      Usuario(
        idUsuario: 1,
        nombre: 'Carlos',
        apellido: 'Mendoza',
        correo: 'carlos.mendoza@universidad.edu',
        contrasena: '123456',
        rol: 'ESTUDIANTE',
      ),
      Usuario(
        idUsuario: 2,
        nombre: 'Ana',
        apellido: 'Gomez',
        correo: 'ana.gomez@universidad.edu',
        contrasena: '123456',
        rol: 'ESTUDIANTE',
      ),
      Usuario(
        idUsuario: 3,
        nombre: 'Admin',
        apellido: 'General',
        correo: 'admin@universidad.edu',
        contrasena: 'admin123',
        rol: 'ADMINISTRADOR',
      ),
      Usuario(
        idUsuario: 4,
        nombre: 'Ing. Roberto',
        apellido: 'Vargas (Mantenimiento)',
        correo: 'roberto.vargas@universidad.edu',
        contrasena: 'resp123',
        rol: 'RESPONSABLE',
      ),
      Usuario(
        idUsuario: 5,
        nombre: 'Lic. Maria',
        apellido: 'Perez (Soporte IT)',
        correo: 'maria.perez@universidad.edu',
        contrasena: 'resp123',
        rol: 'RESPONSABLE',
      ),
    ]);

    final now = DateTime.now();

    // 4. Solicitudes de Prueba
    _solicitudes.addAll([
      Solicitud(
        idSolicitud: 101,
        idEstudiante: 1,
        idResponsable: 4,
        idTipo: 1, // Mantenimiento
        idEstado: 3, // EN-PROCESO
        descripcion: 'El proyector del aula B-202 no enciende y parpadea en rojo.',
        fechaCreacion: now.subtract(const Duration(days: 2)),
        fechaActualizacion: now.subtract(const Duration(hours: 5)),
      ),
      Solicitud(
        idSolicitud: 102,
        idEstudiante: 1,
        idResponsable: null,
        idTipo: 2, // Soporte
        idEstado: 1, // RECIBIDA
        descripcion: 'Problemas de acceso a la red Wi-Fi Campus-Student en la biblioteca central.',
        fechaCreacion: now.subtract(const Duration(hours: 12)),
        fechaActualizacion: now.subtract(const Duration(hours: 12)),
      ),
      Solicitud(
        idSolicitud: 103,
        idEstudiante: 2,
        idResponsable: 5,
        idTipo: 3, // Infraestructura
        idEstado: 4, // COMPLETADA
        descripcion: 'Fuga de agua detectada en los baños del 3er piso del bloque A.',
        fechaCreacion: now.subtract(const Duration(days: 5)),
        fechaActualizacion: now.subtract(const Duration(days: 1)),
      ),
      Solicitud(
        idSolicitud: 104,
        idEstudiante: 2,
        idResponsable: null,
        idTipo: 4, // Otro
        idEstado: 2, // PENDIENTE
        descripcion: 'Falta de sillas ergonómicas en la sala de lectura silenciosa.',
        fechaCreacion: now.subtract(const Duration(days: 1)),
        fechaActualizacion: now.subtract(const Duration(hours: 18)),
      ),
    ]);

    // 5. Historial de Estados
    _historialEstados.addAll([
      HistorialEstado(
        idHistorial: 1,
        idSolicitud: 101,
        idEstado: 1,
        idUsuario: 1,
        fechaCambio: now.subtract(const Duration(days: 2)),
      ),
      HistorialEstado(
        idHistorial: 2,
        idSolicitud: 101,
        idEstado: 3,
        idUsuario: 3,
        fechaCambio: now.subtract(const Duration(hours: 5)),
      ),
      HistorialEstado(
        idHistorial: 3,
        idSolicitud: 102,
        idEstado: 1,
        idUsuario: 1,
        fechaCambio: now.subtract(const Duration(hours: 12)),
      ),
      HistorialEstado(
        idHistorial: 4,
        idSolicitud: 103,
        idEstado: 1,
        idUsuario: 2,
        fechaCambio: now.subtract(const Duration(days: 5)),
      ),
      HistorialEstado(
        idHistorial: 5,
        idSolicitud: 103,
        idEstado: 3,
        idUsuario: 3,
        fechaCambio: now.subtract(const Duration(days: 3)),
      ),
      HistorialEstado(
        idHistorial: 6,
        idSolicitud: 103,
        idEstado: 4,
        idUsuario: 5,
        fechaCambio: now.subtract(const Duration(days: 1)),
      ),
      HistorialEstado(
        idHistorial: 7,
        idSolicitud: 104,
        idEstado: 1,
        idUsuario: 2,
        fechaCambio: now.subtract(const Duration(days: 1)),
      ),
      HistorialEstado(
        idHistorial: 8,
        idSolicitud: 104,
        idEstado: 2,
        idUsuario: 3,
        fechaCambio: now.subtract(const Duration(hours: 18)),
      ),
    ]);

    // 6. Evidencias Fotográficas Semilla (HU6)
    _evidencias.addAll([
      Evidencia(
        idEvidencia: 1,
        idSolicitud: 101,
        archivo: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500',
        fechaSubida: now.subtract(const Duration(days: 2)),
      ),
      Evidencia(
        idEvidencia: 2,
        idSolicitud: 103,
        archivo: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=500',
        fechaSubida: now.subtract(const Duration(days: 5)),
      ),
    ]);
  }

  // --- Getters ---
  List<Usuario> get usuarios => List.unmodifiable(_usuarios);
  List<TipoSolicitud> get tiposSolicitud => List.unmodifiable(_tiposSolicitud);
  List<EstadoSolicitud> get estadosSolicitud => List.unmodifiable(_estadosSolicitud);
  List<Solicitud> get solicitudes => List.unmodifiable(_solicitudes);
  List<Evidencia> get evidencias => List.unmodifiable(_evidencias);
  List<HistorialEstado> get historialEstados => List.unmodifiable(_historialEstados);

  // List of Responsables (HU2)
  List<Usuario> get responsables => _usuarios.where((u) => u.isResponsable).toList();

  // Helper resolvers
  Usuario? getUsuarioById(int? idUsuario) {
    if (idUsuario == null) return null;
    try {
      return _usuarios.firstWhere((u) => u.idUsuario == idUsuario);
    } catch (_) {
      return null;
    }
  }

  TipoSolicitud? getTipoById(int idTipo) {
    try {
      return _tiposSolicitud.firstWhere((t) => t.idTipo == idTipo);
    } catch (_) {
      return null;
    }
  }

  EstadoSolicitud? getEstadoById(int idEstado) {
    try {
      return _estadosSolicitud.firstWhere((e) => e.idEstado == idEstado);
    } catch (_) {
      return null;
    }
  }

  List<Evidencia> getEvidenciasForSolicitud(int idSolicitud) {
    return _evidencias.where((e) => e.idSolicitud == idSolicitud).toList();
  }

  List<HistorialEstado> getHistorialForSolicitud(int idSolicitud) {
    final list = _historialEstados.where((h) => h.idSolicitud == idSolicitud).toList();
    list.sort((a, b) => b.fechaCambio.compareTo(a.fechaCambio));
    return list;
  }

  // --- Dashboard Metrics (HU1) ---
  int get totalSolicitudes => _solicitudes.length;

  int countByEstado(String estadoNombre) {
    final estadoObj = _estadosSolicitud.firstWhere(
      (e) => e.nombre.toUpperCase() == estadoNombre.toUpperCase(),
      orElse: () => EstadoSolicitud(idEstado: -1, nombre: ''),
    );
    if (estadoObj.idEstado == -1) return 0;
    return _solicitudes.where((s) => s.idEstado == estadoObj.idEstado).length;
  }

  // --- Actions ---

  // HU3 & HU6: Crear nueva solicitud con evidencias fotográficas opcionales
  Solicitud registrarSolicitud({
    required int idEstudiante,
    required int idTipo,
    required String descripcion,
    List<String> fotosArchivos = const [],
  }) {
    final newId = _solicitudes.isEmpty
        ? 101
        : (_solicitudes.map((s) => s.idSolicitud).reduce((a, b) => a > b ? a : b) + 1);
    
    final estadoRecibida = _estadosSolicitud.firstWhere(
      (e) => e.nombre.toUpperCase() == 'RECIBIDA',
      orElse: () => _estadosSolicitud.first,
    );

    final now = DateTime.now();

    final nuevaSolicitud = Solicitud(
      idSolicitud: newId,
      idEstudiante: idEstudiante,
      idResponsable: null,
      idTipo: idTipo,
      idEstado: estadoRecibida.idEstado,
      descripcion: descripcion,
      fechaCreacion: now,
      fechaActualizacion: now,
    );

    _solicitudes.insert(0, nuevaSolicitud);

    // Registro inicial en Historial
    final newHistorialId = _historialEstados.isEmpty
        ? 1
        : (_historialEstados.map((h) => h.idHistorial).reduce((a, b) => a > b ? a : b) + 1);

    _historialEstados.add(HistorialEstado(
      idHistorial: newHistorialId,
      idSolicitud: newId,
      idEstado: estadoRecibida.idEstado,
      idUsuario: idEstudiante,
      fechaCambio: now,
    ));

    // Adjuntar evidencias si las hay (HU6)
    int evCounter = _evidencias.isEmpty
        ? 1
        : (_evidencias.map((e) => e.idEvidencia).reduce((a, b) => a > b ? a : b) + 1);

    for (var archivo in fotosArchivos) {
      _evidencias.add(Evidencia(
        idEvidencia: evCounter++,
        idSolicitud: newId,
        archivo: archivo,
        fechaSubida: now,
      ));
    }

    notifyListeners();
    return nuevaSolicitud;
  }

  // HU2: Asignar un responsable a una solicitud
  bool asignarResponsable({
    required int idSolicitud,
    required int idResponsable,
  }) {
    final index = _solicitudes.indexWhere((s) => s.idSolicitud == idSolicitud);
    if (index == -1) return false;

    final actual = _solicitudes[index];
    _solicitudes[index] = actual.copyWith(
      idResponsable: idResponsable,
      fechaActualizacion: DateTime.now(),
    );

    notifyListeners();
    return true;
  }

  // HU5: Actualizar el estado de una solicitud (RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA)
  bool actualizarEstado({
    required int idSolicitud,
    required int nuevoIdEstado,
    required int idUsuarioAccion,
  }) {
    final index = _solicitudes.indexWhere((s) => s.idSolicitud == idSolicitud);
    if (index == -1) return false;

    final actual = _solicitudes[index];
    final now = DateTime.now();

    _solicitudes[index] = actual.copyWith(
      idEstado: nuevoIdEstado,
      fechaActualizacion: now,
    );

    final newHistorialId = _historialEstados.isEmpty
        ? 1
        : (_historialEstados.map((h) => h.idHistorial).reduce((a, b) => a > b ? a : b) + 1);

    _historialEstados.add(HistorialEstado(
      idHistorial: newHistorialId,
      idSolicitud: idSolicitud,
      idEstado: nuevoIdEstado,
      idUsuario: idUsuarioAccion,
      fechaCambio: now,
    ));

    notifyListeners();
    return true;
  }

  // HU6: Adjuntar fotos de evidencia adicionales a una solicitud existente
  void agregarEvidencia({
    required int idSolicitud,
    required String archivo,
  }) {
    final newId = _evidencias.isEmpty
        ? 1
        : (_evidencias.map((e) => e.idEvidencia).reduce((a, b) => a > b ? a : b) + 1);

    _evidencias.add(Evidencia(
      idEvidencia: newId,
      idSolicitud: idSolicitud,
      archivo: archivo,
      fechaSubida: DateTime.now(),
    ));

    notifyListeners();
  }
}
