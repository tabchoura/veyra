<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50;">Bonjour {{ $user->name }},</h2>
        
        <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Veyra.</p>
        
        <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" 
               style="background-color: #3498db; 
                      color: white; 
                      padding: 12px 30px; 
                      text-decoration: none; 
                      border-radius: 5px; 
                      display: inline-block;
                      font-weight: bold;">
                Réinitialiser mon mot de passe
            </a>
        </div>
        
        <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
        <p style="background-color: #e8e8e8; padding: 10px; border-radius: 3px; word-break: break-all;">
            <a href="{{ $resetUrl }}" style="color: #3498db;">{{ $resetUrl }}</a>
        </p>
        
        <p style="color: #e74c3c; margin-top: 20px;">
            <strong>Ce lien expirera dans 60 minutes.</strong>
        </p>
        
        <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet email.</p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #777;">
            Cordialement,<br>
            L'équipe Veyra
        </p>
    </div>
</body>
</html>