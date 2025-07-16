<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Section</title>
</head>
<body>
    <H1> Créer une section</H1>

    <form action="{{ route('sections.store') }}" method="POST">
        @csrf
        <input type="text" name="section_name" id="section_id" placeholder="Nom de la section" class="form-control" required><br><br>
        <input type="text" name="section_description" id="section_description" placeholder="Description de la section" class="form-control"><br><br>
        <button type="submit">Créer votre section</button>
    </form>
</body>
</html>
