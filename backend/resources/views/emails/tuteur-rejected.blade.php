<!DOCTYPE html>
<html>
<head>
    <title>Statut de Candidature Tuteur</title>
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
            background-color: #f44336;
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
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mise à jour du statut de candidature</h1>
    </div>

    <div class="content">
        <p>Cher(e) {{ $tuteur->fullname }},</p>

        <p>Nous vous remercions de l'intérêt que vous portez à devenir tuteur sur notre plateforme d'e-learning. Nous avons examiné attentivement votre candidature.</p>

        <p>Après une analyse approfondie, nous regrettons de vous informer que nous ne sommes pas en mesure d'accepter votre candidature pour le moment.</p>

        <p>Cette décision est basée sur différents facteurs, notamment nos besoins et exigences actuels. Veuillez noter que cette décision ne remet pas en cause vos capacités personnelles ou votre expertise.</p>

        <p>Nous vous encourageons à :</p>
        <ul>
            <li>Revoir nos exigences et directives pour les tuteurs</li>
            <li>Acquérir plus d'expérience dans votre domaine d'expertise</li>
            <li>Envisager de postuler à nouveau dans le futur</li>
        </ul>

        <p>Nous apprécions votre intérêt pour notre plateforme et vous souhaitons le meilleur pour vos projets futurs.</p>

        <p>Cordialement,<br>L'équipe e-Learning</p>
    </div>

    <div class="footer">
        <p>Ceci est un message automatique, merci de ne pas répondre directement à cet email.</p>
    </div>
</body>
</html>