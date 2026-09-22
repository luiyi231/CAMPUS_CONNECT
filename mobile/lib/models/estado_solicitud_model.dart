class EstadoSolicitud {
  final int idEstado;
  final String nombre; // 'RECIBIDA', 'PENDIENTE', 'EN-PROCESO', 'COMPLETADA'

  EstadoSolicitud({
    required this.idEstado,
    required this.nombre,
  });

  Map<String, dynamic> toMap() {
    return {
      'id_estado': idEstado,
      'nombre': nombre,
    };
  }

  factory EstadoSolicitud.fromMap(Map<String, dynamic> map) {
    return EstadoSolicitud(
      idEstado: map['id_estado'],
      nombre: map['nombre'],
    );
  }
}
