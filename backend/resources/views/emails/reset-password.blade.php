<!DOCTYPE html>
<html>
<body>
    <h2>Réinitialisation de mot de passe</h2>
    <p>Votre code de vérification : <strong>{{ $code }}</strong></p>
    <p>Cliquez sur ce lien pour réinitialiser votre mot de passe :</p>
    <a href="{{ $resetUrl }}" 
       style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
       Réinitialiser le mot de passe
    </a>
    <p>Ce lien expire dans 30 minutes</p>
</body>
</html>