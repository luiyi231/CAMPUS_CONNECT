import 'package:flutter/material.dart';
import '../../models/usuario_model.dart';
import '../../theme/app_theme.dart';

class AssignResponsibleDialog extends StatefulWidget {
  final List<Usuario> responsables;
  final int? currentResponsableId;
  final Function(int selectedId) onAssign;

  const AssignResponsibleDialog({
    super.key,
    required this.responsables,
    this.currentResponsableId,
    required this.onAssign,
  });

  @override
  State<AssignResponsibleDialog> createState() => _AssignResponsibleDialogState();
}

class _AssignResponsibleDialogState extends State<AssignResponsibleDialog> {
  int? _selectedId;

  @override
  void initState() {
    super.initState();
    _selectedId = widget.currentResponsableId ??
        (widget.responsables.isNotEmpty ? widget.responsables.first.idUsuario : null);
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: const Row(
        children: [
          Icon(Icons.person_add_alt_1_rounded, color: AppTheme.primaryBlue),
          SizedBox(width: 10),
          Text(
            'Asignar Responsable (HU2)',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
        ],
      ),
      content: widget.responsables.isEmpty
          ? const Text('No hay personal encargado registrado.')
          : Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Selecciona el técnico o encargado para la atención de esta solicitud:',
                  style: TextStyle(fontSize: 13, color: Colors.black87),
                ),
                const SizedBox(height: 16),
                ...widget.responsables.map((user) {
                  return RadioListTile<int>(
                    value: user.idUsuario,
                    groupValue: _selectedId,
                    activeColor: AppTheme.primaryBlue,
                    title: Text(
                      user.nombreCompleto,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                    subtitle: Text(
                      '${user.rol} • ${user.correo}',
                      style: const TextStyle(fontSize: 12),
                    ),
                    onChanged: (val) {
                      setState(() {
                        _selectedId = val;
                      });
                    },
                  );
                }),
              ],
            ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('CANCELAR'),
        ),
        ElevatedButton(
          onPressed: _selectedId == null
              ? null
              : () {
                  widget.onAssign(_selectedId!);
                  Navigator.pop(context);
                },
          child: const Text('GUARDAR'),
        ),
      ],
    );
  }
}
