import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

// ─── PALETTE DE COULEURS ────────────────────────────────────────────────────
// Couleurs dérivées directement du logo YOONU MAKK.
class AppColors {
  AppColors._();

  static const primary      = Color(0xFFE91E63); // rose magenta vif (logo)
  static const primaryLight = Color(0xFFF06292); // rose clair
  static const primaryDark  = Color(0xFF8B1A47); // bordeaux (texte logo)
  static const primarySoft  = Color(0xFFFCE4EC); // rose poudré (fonds doux)

  static const green        = Color(0xFF4CAF50); // vert grossesse
  static const greenSoft    = Color(0xFFE8F5E9);

  static const bg           = Color(0xFFFFF5F8); // fond légèrement rosé
  static const surface      = Colors.white;
  static const ink          = Color(0xFF2A0A1A); // texte sombre (teinte bordeaux)
  static const ink2         = Color(0xFF6B4757); // texte secondaire
  static const border       = Color(0xFFF5D6E1); // bordures roses

  static const danger       = Color(0xFFDC2626);
  static const success      = Color(0xFF059669);
  static const warning      = Color(0xFFD97706);
}

// ─── THÈME GLOBAL ────────────────────────────────────────────────────────────
class AppTheme {
  AppTheme._();

  static ThemeData get theme => ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: AppColors.primary,
      surface: AppColors.bg,
    ),
    scaffoldBackgroundColor: AppColors.bg,
    textTheme: GoogleFonts.poppinsTextTheme().copyWith(
      displayLarge: GoogleFonts.poppins(
        fontSize: 32, fontWeight: FontWeight.w700, color: AppColors.primaryDark,
      ),
      titleLarge: GoogleFonts.poppins(
        fontSize: 22, fontWeight: FontWeight.w700, color: AppColors.primaryDark,
      ),
      titleMedium: GoogleFonts.poppins(
        fontSize: 17, fontWeight: FontWeight.w600, color: AppColors.ink,
      ),
      bodyLarge: GoogleFonts.poppins(
        fontSize: 15, fontWeight: FontWeight.w400, color: AppColors.ink,
      ),
      bodyMedium: GoogleFonts.poppins(
        fontSize: 13, fontWeight: FontWeight.w400, color: AppColors.ink2,
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        minimumSize: const Size(double.infinity, 52),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        textStyle: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600),
        elevation: 0,
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: AppColors.primary,
        minimumSize: const Size(double.infinity, 52),
        side: const BorderSide(color: AppColors.primary, width: 1.5),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        textStyle: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: AppColors.surface,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.primary, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.danger),
      ),
      labelStyle: GoogleFonts.poppins(color: AppColors.ink2, fontSize: 13),
      hintStyle: GoogleFonts.poppins(color: AppColors.ink2, fontSize: 13),
    ),
    appBarTheme: AppBarTheme(
      backgroundColor: AppColors.bg,
      elevation: 0,
      iconTheme: const IconThemeData(color: AppColors.ink),
      titleTextStyle: GoogleFonts.poppins(
        fontSize: 17, fontWeight: FontWeight.w600, color: AppColors.primaryDark,
      ),
    ),
  );
}
