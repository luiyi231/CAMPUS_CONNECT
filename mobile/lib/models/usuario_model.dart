class Usuario {
  final int idUsuario;
  final String nombre;
  final String apellido;
  final String correo;
  final String contrasena;
  final String rol; // 'ADMINISTRADOR', 'ESTUDIANTE', 'RESPONSABLE'

  Usuario({
    required this.idUsuario,
    required this.nombre,
    required this.apellido,
    required this.correo,
    required this.contrasena,
    required this.rol,
  });

  String get nombreCompleto => '$nombre $apellido';

  bool get isAdmin => rol.toUpperCase() == 'ADMINISTRADOR';
  bool get isEstudiante => rol.toUpperCase() == 'ESTUDIANTE';
  bool get isResponsable => rol.toUpperCase() == 'RESPONSABLE' || isAdmin;

  Map<String, dynamic> toMap() {
    return {
      'id_usuario': idUsuario,
      'nombre': nombre,
      'apellido': apellido,
      'correo': correo,
      'contraseña': contrasena,
      'rol': rol,
    };
  }

  factory Usuario.fromMap(Map<String, dynamic> map) {
    return Usuario(
      idUsuario: map['id_usuario'],
      nombre: map['nombre'],
      apellido: map['apellido'],
      correo: map['correo'],
      contrasena: map['contraseña'] ?? map['contrasena'] ?? '',
      rol: map['rol'],
    );
  }
}
