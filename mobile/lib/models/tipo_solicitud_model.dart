class TipoSolicitud {
  final int idTipo;
  final String nombre; // 'Mantenimiento', 'Soporte', 'Infraestructura', 'Otro'

  TipoSolicitud({
    required this.idTipo,
    required this.nombre,
  });

  Map<String, dynamic> toMap() {
    return {
      'id_tipo': idTipo,
      'nombre': nombre,
    };
  }

  factory TipoSolicitud.fromMap(Map<String, dynamic> map) {
    return TipoSolicitud(
      idTipo: map['id_tipo'],
      nombre: map['nombre'],
    );
  }
}
