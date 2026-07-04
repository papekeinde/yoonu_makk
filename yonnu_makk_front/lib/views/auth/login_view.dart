import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../services/auth_service.dart';
import '../../widgets/app_input.dart';

// ─── VUE CONNEXION ────────────────────────────────────────────────────────────
class LoginView extends StatefulWidget {
  const LoginView({super.key});

  @override
  State<LoginView> createState() => _LoginViewState();
}

class _LoginViewState extends State<LoginView> {
  final _formKey       = GlobalKey<FormState>();
  final _identCtrl     = TextEditingController();
  final _passCtrl      = TextEditingController();
  bool  _obscure       = true;

  @override
  void dispose() {
    _identCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }

  Future<void> _connecter() async {
    if (!_formKey.currentState!.validate()) return;

    final ctrl = context.read<AuthController>();
    final ok = await ctrl.connecter(
      identifiant: _identCtrl.text.trim(),
      motDePasse:  _passCtrl.text,
    );

    if (!mounted) return;
    if (ok) {
      Navigator.pushReplacementNamed(context, Routes.home);
    }
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = context.watch<AuthController>();

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(),
        title: Row(
          children: [
            // Mini logo dans l'app bar
            Container(
              width: 28, height: 28,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: AppColors.primary, width: 2),
                color: AppColors.primarySoft,
              ),
              child: const Center(
                child: Text('Y', style: TextStyle(
                  fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.primary,
                )),
              ),
            ),
            const SizedBox(width: 8),
            const Text('YOONU JIGEEN'),
          ],
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 8),

              // Titre
              const Text('Bon retour parmi nous',
                style: TextStyle(
                  fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.primaryDark,
                )),
              const SizedBox(height: 6),
              const Text('Connectez-vous à votre espace',
                style: TextStyle(fontSize: 15, color: AppColors.ink2)),

              const SizedBox(height: 36),

              // Email / téléphone
              AppInput(
                label:       'Email ou téléphone',
                hint:        'exemple@email.com',
                controller:  _identCtrl,
                keyboardType: TextInputType.emailAddress,
                validator: (v) {
                  if (v == null || v.trim().isEmpty) return 'Champ requis';
                  return null;
                },
              ),

              const SizedBox(height: 20),

              // Mot de passe
              AppInput(
                label:      'Mot de passe',
                controller: _passCtrl,
                obscure:    _obscure,
                suffix: IconButton(
                  icon: Icon(
                    _obscure ? Icons.visibility_off_rounded : Icons.visibility_rounded,
                    color: AppColors.ink2,
                  ),
                  onPressed: () => setState(() => _obscure = !_obscure),
                ),
                validator: (v) {
                  if (v == null || v.isEmpty) return 'Champ requis';
                  return null;
                },
              ),

              // Mot de passe oublié
              Align(
                alignment: Alignment.centerRight,
                child: TextButton(
                  onPressed: () {},
                  child: const Text('Mot de passe oublié ?',
                    style: TextStyle(color: AppColors.primary)),
                ),
              ),

              // Message d'erreur
              if (ctrl.erreur != null) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppColors.danger.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Row(children: [
                    const Icon(Icons.error_outline, color: AppColors.danger, size: 18),
                    const SizedBox(width: 8),
                    Expanded(child: Text(ctrl.erreur!,
                      style: const TextStyle(color: AppColors.danger, fontSize: 13))),
                  ]),
                ),
                const SizedBox(height: 16),
              ],

              const SizedBox(height: 8),

              // Bouton Se connecter
              ctrl.loading
                  ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                  : ElevatedButton(
                      onPressed: _connecter,
                      child: const Text('Se connecter'),
                    ),

              const SizedBox(height: 24),

              // Lien vers inscription
              Center(
                child: RichText(
                  text: TextSpan(
                    text: 'Pas encore de compte ? ',
                    style: const TextStyle(color: AppColors.ink2, fontSize: 14),
                    children: [
                      WidgetSpan(
                        child: GestureDetector(
                          onTap: () => Navigator.pushReplacementNamed(context, Routes.register),
                          child: const Text('Créer un compte',
                            style: TextStyle(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                              fontSize: 14,
                            )),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
