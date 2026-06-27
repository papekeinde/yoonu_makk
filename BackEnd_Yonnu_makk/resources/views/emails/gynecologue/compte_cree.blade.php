<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #7B3F8C; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333333; }
        .body p { line-height: 1.6; }
        .credentials { background: #f9f0ff; border-left: 4px solid #7B3F8C; padding: 16px 20px; border-radius: 4px; margin: 20px 0; }
        .credentials p { margin: 6px 0; font-size: 15px; }
        .credentials strong { color: #7B3F8C; }
        .warning { font-size: 13px; color: #888; margin-top: 20px; }
        .footer { background: #f4f4f4; text-align: center; padding: 16px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>YOONU MAKK</h1>
        </div>
        <div class="body">
            <p>Bonjour <strong>Dr. {{ $gynecologue->prenom }} {{ $gynecologue->nom }}</strong>,</p>

            <p>
                Votre demande d'adhésion à la plateforme <strong>YOONU MAKK</strong> a été approuvée.
                Votre compte gynécologue est maintenant actif.
            </p>

            <p>Voici vos identifiants de connexion :</p>

            <div class="credentials">
                <p><strong>Email :</strong> {{ $gynecologue->email }}</p>
                <p><strong>Mot de passe temporaire :</strong> {{ $motDePasseTemporaire }}</p>
            </div>

            <p>
                Connectez-vous à l'application et modifiez votre mot de passe dès que possible
                depuis votre espace profil.
            </p>

            <p class="warning">
                Si vous n'êtes pas à l'origine de cette demande, veuillez contacter notre équipe immédiatement.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} YOONU MAKK — Tous droits réservés
        </div>
    </div>
</body>
</html>
