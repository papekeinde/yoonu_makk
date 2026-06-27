import 'package:flutter/material.dart';
import '../config/theme.dart';

// ─── CHAMP DE TEXTE RÉUTILISABLE ─────────────────────────────────────────────
class AppInput extends StatelessWidget {
  final String          label;
  final String?         hint;
  final TextEditingController controller;
  final bool            obscure;
  final TextInputType?  keyboardType;
  final String?         Function(String?)? validator;
  final Widget?         suffix;
  final bool            enabled;

  const AppInput({
    super.key,
    required this.label,
    required this.controller,
    this.hint,
    this.obscure = false,
    this.keyboardType,
    this.validator,
    this.suffix,
    this.enabled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label.toUpperCase(),
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w700,
            color: AppColors.ink2,
            letterSpacing: 0.8,
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller:    controller,
          obscureText:   obscure,
          keyboardType:  keyboardType,
          validator:     validator,
          enabled:       enabled,
          decoration: InputDecoration(
            hintText: hint,
            suffixIcon: suffix,
          ),
        ),
      ],
    );
  }
}
