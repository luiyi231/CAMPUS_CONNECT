import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

class StatusBadge extends StatelessWidget {
  final String estadoNombre;
  final bool isLarge;

  const StatusBadge({
    super.key,
    required this.estadoNombre,
    this.isLarge = false,
  });

  @override
  Widget build(BuildContext context) {
    final color = AppTheme.getStatusColor(estadoNombre);
    final icon = AppTheme.getStatusIcon(estadoNombre);

    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: isLarge ? 14 : 10,
        vertical: isLarge ? 8 : 4,
      ),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.4), width: 1.2),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            icon,
            size: isLarge ? 18 : 14,
            color: color,
          ),
          const SizedBox(width: 6),
          Text(
            estadoNombre.toUpperCase(),
            style: TextStyle(
              color: color,
              fontSize: isLarge ? 13 : 11,
              fontWeight: FontWeight.bold,
              letterSpacing: 0.5,
            ),
          ),
        ],
      ),
    );
  }
}
