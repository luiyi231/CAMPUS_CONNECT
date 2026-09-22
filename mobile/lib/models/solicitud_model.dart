class Solicitud {
  final int idSolicitud;
  final int idEstudiante;
  final int? idResponsable; // Nullable hasta ser asignado (HU2)
  final int idTipo;
  final int idEstado;
  final String descripcion;
  final DateTime fechaCreacion;
  final DateTime fechaActualizacion;

  Solicitud({
    required this.idSolicitud,
    required this.idEstudiante,
    this.idResponsable,
    required this.idTipo,
    required this.idEstado,
    required this.descripcion,
    required this.fechaCreacion,
    required this.fechaActualizacion,
  });

  Solicitud copyWith({
    int? idSolicitud,
    int? idEstudiante,
    int? idResponsable,
    bool clearResponsable = false,
    int? idTipo,
    int? idEstado,
    String? descripcion,
    DateTime? fechaCreacion,
    DateTime? fechaActualizacion,
  }) {
    return Solicitud(
      idSolicitud: idSolicitud ?? this.idSolicitud,
      idEstudiante: idEstudiante ?? this.idEstudiante,
      idResponsable: clearResponsable ? null : (idResponsable ?? this.idResponsable),
      idTipo: idTipo ?? this.idTipo,
      idEstado: idEstado ?? this.idEstado,
      descripcion: descripcion ?? this.descripcion,
      fechaCreacion: fechaCreacion ?? this.fechaCreacion,
      fechaActualizacion: fechaActualizacion ?? this.fechaActualizacion,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id_solicitud': idSolicitud,
      'id_estudiante': idEstudiante,
      'id_responsable': idResponsable,
      'id_tipo': idTipo,
      'id_estado': idEstado,
      'descripcion': descripcion,
      'fecha_creacion': fechaCreacion.toIso8601String(),
      'fecha_actualizacion': fechaActualizacion.toIso8601String(),
    };
  }

  factory Solicitud.fromMap(Map<String, dynamic> map) {
    return Solicitud(
      idSolicitud: map['id_solicitud'],
      idEstudiante: map['id_estudiante'],
      idResponsable: map['id_responsable'],
      idTipo: map['id_tipo'],
      idEstado: map['id_estado'],
      descripcion: map['descripcion'],
      fechaCreacion: DateTime.parse(map['fecha_creacion']),
      fechaActualizacion: DateTime.parse(map['fecha_actualizacion']),
    );
  }
}
