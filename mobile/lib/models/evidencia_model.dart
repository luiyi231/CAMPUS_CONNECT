class Evidencia {
  final int idEvidencia;
  final int idSolicitud;
  final String archivo; // Path, Base64 o URL de la foto/imagen
  final DateTime fechaSubida;

  Evidencia({
    required this.idEvidencia,
    required this.idSolicitud,
    required this.archivo,
    required this.fechaSubida,
  });

  Map<String, dynamic> toMap() {
    return {
      'id_evidencia': idEvidencia,
      'id_solicitud': idSolicitud,
      'archivo': archivo,
      'fecha_subida': fechaSubida.toIso8601String(),
    };
  }

  factory Evidencia.fromMap(Map<String, dynamic> map) {
    return Evidencia(
      idEvidencia: map['id_evidencia'],
      idSolicitud: map['id_solicitud'],
      archivo: map['archivo'],
      fechaSubida: DateTime.parse(map['fecha_subida']),
    );
  }
}
