<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <title>Admin dash</title>
</head>
<body>


<H1>Bonjour admin</H1>
<p>Bienvenue sur le tableau de bord de l'administrateur.</p>
<p>Vous pouvez gérer les rendez-vous, les utilisateurs et d'autres paramètres du système.</p>
@if(isset($sections) && $sections->isNotEmpty())
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
            <p>Veuillez cocher les informations associées :</p>
            <div id="typeInfosCheckboxes">
                <!-- Les cases à cocher seront ajoutées dynamiquement ici -->
            </div>
        </div>

        <div id="dynamicFormContainer" class="mt-3" style="display: none;">
            <p>Formulaire dynamique :</p>
            <div id="dynamicForm">
                <!-- Le formulaire dynamique sera ajouté ici -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Soumettre</button>
    </form>
@else
    <p>Aucune section disponible. <a href="{{ route('sections.create') }}">Ajouter une section</a></p>
@endif

<script>
    function fetchTypeInfos(sectionId) {
        if (!sectionId) {
            document.getElementById('typeInfosContainer').style.display = 'none';
            document.getElementById('dynamicFormContainer').style.display = 'none';
            return;
        }

        fetch(`/admin/sections/${sectionId}/type-infos`)
            .then(response => response.json())
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
            const formGroup = document.createElement('div');
            formGroup.classList.add('mb-3');
            formGroup.id = `formGroup_${attr}`;
            formGroup.innerHTML = `
                <label for="input_${attr}" class="form-label">${attr} :</label>
                <input type="text" class="form-control" id="input_${attr}" name="inputs[${attr}]" placeholder="Entrez ${attr}">
            `;
            formDiv.appendChild(formGroup);
        } else {
            const group = document.getElementById(`formGroup_${attr}`);
            if (group) formDiv.removeChild(group);
        }

        formContainer.style.display = formDiv.children.length > 0 ? 'block' : 'none';
    }
</script>

@foreach ($rdvUrgents as $rdvUrgent)
    <div >
        <table class="table">
            <thead>
                <tr>
                    <th>Nom complet du patient</th>
                    <th>Patient ou Visiteur</th>
                    <th>Email</th>
                    <th>Numéro de téléphone</th>
                    <th>Date et heure du rendez-vous</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->name }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            Patient
                        @elseif($rdvUrgent->visiteur)
                            Visiteur
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->email }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->email }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->phone_number }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->telephone }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $rdvUrgent->date_heure }}</td>
                    <td>
                       {{$rdvUrgent->commentaire}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endforeach

</body>
</html>
