<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le champ {{ $field }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>
<body>
<div class="container mt-4">
    <h3>Modifier le champ : <strong>{{ $field }}</strong> (Section : {{ $section->name }})</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>❌ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('field.update', [$section->id, $field]) }}" enctype="multipart/form-data">
        @csrf

        @if(in_array($field, ['description', 'contenu']))
            <div class="mb-3">
                <label for="value" class="form-label">Contenu :</label>
                <textarea name="value" id="editor" rows="6" class="form-control">{{ old('value', $value) }}</textarea>
            </div>
        @elseif($field === 'image')
            <div class="mb-3">
                <label>Image actuelle :</label><br>
                @if($value)
                    <img src="{{ asset('storage/uploads/' . $value) }}" alt="Image actuelle" style="max-height: 200px;">
                @else
                    <p>Aucune image enregistrée</p>
                @endif
            </div>
            <div class="mb-3">
                <label for="value" class="form-label">Changer l'image :</label>
                <input type="file" name="value" accept="image/*" class="form-control">
            </div>
        @else
            <div class="mb-3">
                <label for="value" class="form-label">Valeur :</label>
                <input type="text" name="value" id="value" class="form-control" value="{{ old('value', $value) }}">
            </div>
        @endif

        <button type="submit" class="btn btn-success">💾 Enregistrer</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">↩️ Retour</a>
    </form>
</div>

<script>
    @if(in_array($field, ['description', 'contenu']))
    CKEDITOR.replace('editor');
    @endif
</script>
</body>
</html>
