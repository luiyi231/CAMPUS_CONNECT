import 'package:flutter/material.dart';

class AppTheme {
  // Primary colors
  static const Color primaryBlue = Color(0xFF1E3A8A); // Deep Campus Blue
  static const Color primaryIndigo = Color(0xFF3B82F6); // Accent Indigo
  static const Color backgroundLight = Color(0xFFF8FAFC);
  static const Color surfaceWhite = Color(0xFFFFFFFF);
  static const Color cardBorder = Color(0xFFE2E8F0);
  
  // Status Colors (RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA)
  static const Color statusRecibida = Color(0xFF0284C7); // Sky Blue
  static const Color statusPendiente = Color(0xFFF59E0B); // Amber/Orange
  static const Color statusEnProceso = Color(0xFF8B5CF6); // Purple/Violet
  static const Color statusCompletada = Color(0xFF10B981); // Emerald Green

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryBlue,
        primary: primaryBlue,
        secondary: primaryIndigo,
        surface: surfaceWhite,
      ),
      scaffoldBackgroundColor: backgroundLight,
      appBarTheme: const AppBarTheme(
        backgroundColor: primaryBlue,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        titleTextStyle: TextStyle(
          fontSize: 19,
          fontWeight: FontWeight.bold,
          color: Colors.white,
          letterSpacing: 0.3,
        ),
      ),
      cardTheme: CardThemeData(
        color: surfaceWhite,
        elevation: 2,
        shadowColor: Colors.black.withOpacity(0.05),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: cardBorder, width: 1),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryBlue,
          foregroundColor: Colors.white,
          elevation: 2,
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: cardBorder),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: cardBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryIndigo, width: 2),
        ),
      ),
      floatingActionButtonTheme: const FloatingActionButtonThemeData(
        backgroundColor: primaryBlue,
        foregroundColor: Colors.white,
        elevation: 4,
      ),
    );
  }

  static Color getStatusColor(String estadoNombre) {
    switch (estadoNombre.toUpperCase()) {
      case 'RECIBIDA':
        return statusRecibida;
      case 'PENDIENTE':
        return statusPendiente;
      case 'EN-PROCESO':
      case 'EN_PROCESO':
      case 'EN PROCESO':
        return statusEnProceso;
      case 'COMPLETADA':
        return statusCompletada;
      default:
        return Colors.grey;
    }
  }

  static IconData getStatusIcon(String estadoNombre) {
    switch (estadoNombre.toUpperCase()) {
      case 'RECIBIDA':
        return Icons.inbox_rounded;
      case 'PENDIENTE':
        return Icons.hourglass_top_rounded;
      case 'EN-PROCESO':
      case 'EN_PROCESO':
      case 'EN PROCESO':
        return Icons.sync_rounded;
      case 'COMPLETADA':
        return Icons.check_circle_rounded;
      default:
        return Icons.help_outline_rounded;
    }
  }
}
