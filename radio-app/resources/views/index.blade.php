<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre de Radiologie - Prise de rendez-vous</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e6f0ff, #f7fafd);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .container img {
            width: 120px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: #1a202c;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            background-color: #3182ce;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #2b6cb0;
        }

        .subtitle {
            color: #4a5568;
            font-size: 15px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Remplace le lien ci-dessous par un logo ou une image adaptée -->
        <img src="https://cdn-icons-png.flaticon.com/512/2966/2966485.png" alt="Logo Radiologie">

        <h1>Bienvenue au Centre de Radiologie</h1>
        <p class="subtitle">Prenez facilement rendez-vous en ligne pour vos examens médicaux</p>

        <h2>Créez votre compte pour continuer</h2>
        <a href="{{ route('register') }}">Créer un compte</a>
        <h3>Ou prenez un rendez vous en tant que visiteur</h3>
        <a href="{{ route('visiteur.rendezvous.index') }}">Prendre rendez-vous!</a>
    </div>
</body>
</html>
