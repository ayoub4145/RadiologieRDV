<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add admin</title>
</head>
<body>
<h1>Ajouter un nouveau admin</h1>
<form action="{{route('add_admin')}}" method="POST">
    @csrf
    <input type="text" name="admin_name" placeholder="Nom" required><br>
    <input type="text" name="admin_email" placeholder="Email" required><br>
    <input type="text" name="admin_phone" placeholder="Téléphone" required><br>
    <input type="password" name="admin_password" placeholder="Mot de passe" required><br>
    <input type="submit" value="Envoyer">
</form>
</body>
</html>
