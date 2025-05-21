<!DOCTYPE html>
<html>
<head>
    <title>Candidature de Tuteur Acceptée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            font-size: 0.9em;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Félicitations !</h1>
    </div>

    <div class="content">
        <p>Cher(e) {{ $tuteur->fullname }},</p>

        <p>Nous avons le plaisir de vous informer que votre candidature pour devenir tuteur sur notre plateforme d'e-learning a été acceptée !</p>

        <p>Votre expertise et vos qualifications nous ont impressionnés, et nous sommes convaincus que vous serez un atout précieux pour notre communauté d'enseignants.</p>

        <p>Vous pouvez maintenant accéder à votre tableau de bord de tuteur et commencer à créer des cours en vous connectant à votre compte.</p>

        <p>Voici ce que vous pouvez faire maintenant :</p>
        <ul>
            <li>Compléter votre profil de tuteur</li>
            <li>Créer votre premier cours</li>
            <li>Interagir avec les étudiants</li>
            <li>Commencer à partager vos connaissances</li>
        </ul>

        <center>
            <a href="{{ http://localhost:5173/signin }}" class="button">Accéder à Votre Tableau de Bord</a>
        </center>

        <p>Si vous avez des questions ou besoin d'aide, n'hésitez pas à contacter notre équipe de support.</p>

        <p>Cordialement,<br>L'équipe e-Learning</p>
    </div>

    <div class="footer">
        <p>Ceci est un message automatique, merci de ne pas répondre directement à cet email.</p>
    </div>
</body>
</html>