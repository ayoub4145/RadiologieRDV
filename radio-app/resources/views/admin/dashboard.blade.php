<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin dash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

<div class="container mt-4">
    <h1>Bonjour admin</h1>

    <p>Bienvenue sur le tableau de bord.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if($sections->isNotEmpty())
    <form action="{{ route('admin.storeSectionData') }}" method="POST">
        @csrf
        <label for="section">Sélectionner une section :</label>
        <select name="section" id="section" class="form-select" onchange="fetchTypeInfos(this.value)">
            <option value="">Sélectionner une section</option>
            @foreach($sections as $section)
                <option value="{{ $section->id }}">{{ $section->name }}</option>
            @endforeach
        </select>

        <div id="typeInfosContainer" class="mt-3" style="display: none;">
            <p>Champs à remplir :</p>
            <div id="typeInfosCheckboxes"></div>
        </div>

        <div id="dynamicFormContainer" class="mt-3" style="display: none;">
            <p>Formulaire :</p>
            <div id="dynamicForm"></div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Soumettre</button>
    </form>
    @else
        <p>Aucune section disponible.</p><a href="{{ route('sections.create') }}">Ajouter une section</a>

    @endif

    <hr>
    <h4>Rendez-vous urgents :</h4>
    @foreach ($rdvUrgents as $rdv)
    <table class="table table-bordered">
        <tr>
            <th>Nom</th>
            <th>Type</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Date</th>
            <th>Commentaire</th>
        </tr>
        <tr>
            <td>
                {{ $rdv->user->name ?? $rdv->visiteur->name ?? '-' }}
            </td>
            <td>
                {{ $rdv->user ? 'Patient' : ($rdv->visiteur ? 'Visiteur' : '-') }}
            </td>
            <td>
                {{ $rdv->user->email ?? $rdv->visiteur->email ?? '-' }}
            </td>
            <td>
                {{ $rdv->user->phone_number ?? $rdv->visiteur->telephone ?? '-' }}
            </td>
            <td>{{ $rdv->date_heure }}</td>
            <td>{{ $rdv->commentaire }}</td>
        </tr>
    </table>
    @endforeach
</div>


<script>
    function fetchTypeInfos(sectionId) {
        if (!sectionId) {
            document.getElementById('typeInfosContainer').style.display = 'none';
            document.getElementById('dynamicFormContainer').style.display = 'none';
            return;
        }

        fetch(`/admin/sections/${sectionId}/type-infos`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('typeInfosContainer');
                const checkboxesDiv = document.getElementById('typeInfosCheckboxes');
                const formDiv = document.getElementById('dynamicForm');
                const formContainer = document.getElementById('dynamicFormContainer');

                checkboxesDiv.innerHTML = '';
                formDiv.innerHTML = '';

                if (data.attributs && data.attributs.length > 0) {
                    data.attributs.forEach(attr => {
                        const checkbox = document.createElement('div');
                        checkbox.classList.add('form-check');
                        checkbox.innerHTML = `
                            <input class="form-check-input" type="checkbox" id="attr_${attr}" onchange="generateDynamicForm('${attr}')">
                            <label class="form-check-label" for="attr_${attr}">${attr}</label>
                        `;
                        checkboxesDiv.appendChild(checkbox);
                    });
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }

                formContainer.style.display = 'none';
            });
    }

    function generateDynamicForm(attr) {
        const formContainer = document.getElementById('dynamicFormContainer');
        const formDiv = document.getElementById('dynamicForm');
        const isChecked = document.getElementById(`attr_${attr}`).checked;

        if (isChecked) {
            const div = document.createElement('div');
            div.classList.add('mb-3');
            div.id = `formGroup_${attr}`;
            div.innerHTML = `
                <label for="input_${attr}" class="form-label">${attr} :</label>
                <input type="text" class="form-control" name="inputs[${attr}]" id="input_${attr}" placeholder="Entrez ${attr}">
            `;
            formDiv.appendChild(div);
        } else {
            const existing = document.getElementById(`formGroup_${attr}`);
            if (existing) formDiv.removeChild(existing);
        }

        formContainer.style.display = formDiv.children.length > 0 ? 'block' : 'none';
    }
</script>

</body>
</html>
