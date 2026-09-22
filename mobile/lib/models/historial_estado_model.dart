class HistorialEstado {
  final int idHistorial;
  final int idSolicitud;
  final int idEstado;
  final int idUsuario;
  final DateTime fechaCambio;

  HistorialEstado({
    required this.idHistorial,
    required this.idSolicitud,
    required this.idEstado,
    required this.idUsuario,
    required this.fechaCambio,
  });

  Map<String, dynamic> toMap() {
    return {
      'id_historial': idHistorial,
      'id_solicitud': idSolicitud,
      'id_estado': idEstado,
      'id_usuario': idUsuario,
      'fecha_cambio': fechaCambio.toIso8601String(),
    };
  }

  factory HistorialEstado.fromMap(Map<String, dynamic> map) {
    return HistorialEstado(
      idHistorial: map['id_historial'],
      idSolicitud: map['id_solicitud'],
      idEstado: map['id_estado'],
      idUsuario: map['id_usuario'],
      fechaCambio: DateTime.parse(map['fecha_cambio']),
    );
  }
}
