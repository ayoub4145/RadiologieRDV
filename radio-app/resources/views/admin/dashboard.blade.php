<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    .add-admin-btn-img {
    width: 32px;
    height: 32px;
    margin: 0;
    padding: 0;
}
    </style>
</head>
<body>
<a href="{{route('add_admin_form')}}" class="btn btn-primary add-admin-btn">
    <img src="https://cdn-icons-png.flaticon.com/512/921/921359.png" alt="Add_admin_img" class="add-admin-btn-img">
</a>
<div class="container mt-4">
    <h1>Bonjour admin : {{ session('new_admin')->name ?? Auth::user()->name }}</h1>
    <p>Bienvenue sur le tableau de bord.</p>
  <div class="container mt-5">
    <div class="input-container mb-3">
        <input id="sectionSearch" placeholder="Search section..." class="form-control" name="text" type="text" oninput="searchSection(this.value)">
    </div>
 <div id="fieldsTableContainer" style="display:none;">
    <h5>Champs associés à la section : <span id="sectionTitle"></span></h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom du champ</th>
                <th>Valeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="fieldsTableBody"></tbody>
    </table>
    <button id="deleteSectionBtn" class="btn btn-danger" style="display:none;" onclick="deleteSection()">
        Supprimer la section entière
    </button>
</div>

<script>
let currentSectionId = null;

function searchSection(name) {
    if (!name || name.length < 2) {
        document.getElementById('fieldsTableContainer').style.display = 'none';
        return;
    }

    fetch(`/admin/search-section/${name.toLowerCase()}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('fieldsTableBody');
            const sectionTitle = document.getElementById('sectionTitle');
            const container = document.getElementById('fieldsTableContainer');
            const deleteBtn = document.getElementById('deleteSectionBtn');

            if (!data || data.attributs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="2">Aucun champ trouvé</td></tr>`;
                sectionTitle.textContent = '';
                deleteBtn.style.display = 'none';
                container.style.display = 'block';
                currentSectionId = null;
                return;
            }

            sectionTitle.textContent = data.section;
            tbody.innerHTML = '';
            currentSectionId = data.section_id;

                data.attributs.forEach(attr => {
                    let val = data.values[attr] ?? '-';

                    // Affichage court pour le contenu long
                    if (val && val.length > 80) {
                        val = val.substring(0, 80) + '...';
                    }

                    // Si c’est une image
                    if (attr === 'image' && val !== '-') {
                        val = `<img src="/storage/uploads/${val}" alt="${attr}" style="max-height: 80px;">`;
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${attr}</strong></td>
                            <td>${val}</td>
                            <td>
                                <a href="/admin/sections/${currentSectionId}/fields/${attr}/edit" class="btn btn-sm btn-outline-primary" title="Modifier">✏️</a>
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="deleteField('${attr}')">❌</button>
                            </td>
                        </tr>
                    `;
                });


            deleteBtn.style.display = currentSectionId ? 'inline-block' : 'none';
            container.style.display = 'block';
        });
}

function deleteField(field) {
    if (!currentSectionId) return alert('Section non définie');

    if (!confirm(`Confirmez-vous la suppression du champ "${field}" ?`)) return;

    fetch(`/admin/sections/${currentSectionId}/fields/${field}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(res => {
        if (res.ok) {
            alert(`Champ "${field}" supprimé`);
            searchSection(document.getElementById('sectionSearch').value);
        } else {
            alert('Erreur lors de la suppression');
        }
    });
}

function deleteSection() {
    if (!currentSectionId) return alert('Section non définie');

    if (!confirm('Confirmez-vous la suppression de la section entière ? Cette action est irréversible.')) return;

    fetch(`/admin/sections/${currentSectionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(res => {
        if (res.ok) {
            alert('Section supprimée avec succès');
            document.getElementById('fieldsTableContainer').style.display = 'none';
            document.getElementById('sectionSearch').value = '';
            currentSectionId = null;
        } else {
            alert('Erreur lors de la suppression de la section');
        }
    });
}
</script>


<script>
let currentSectionId = null;

function searchSection(name) {
    if (!name || name.length < 2) {
        document.getElementById('fieldsTableContainer').style.display = 'none';
        return;
    }

    fetch(`/admin/search-section/${name.toLowerCase()}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('fieldsTableBody');
            const sectionTitle = document.getElementById('sectionTitle');
            const container = document.getElementById('fieldsTableContainer');
            const deleteBtn = document.getElementById('deleteSectionBtn');

            if (!data || data.attributs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="2">Aucun champ trouvé</td></tr>`;
                sectionTitle.textContent = '';
                deleteBtn.style.display = 'none';
                container.style.display = 'block';
                currentSectionId = null;
                return;
            }

            sectionTitle.textContent = data.section;
            tbody.innerHTML = '';
            currentSectionId = data.section_id;

            data.attributs.forEach(attr => {
                tbody.innerHTML += `
                    <tr>
                        <td>${attr}</td>
                        <td>
                            <a href="/admin/sections/${currentSectionId}/fields/${attr}/edit" class="btn btn-sm btn-outline-primary" title="Modifier">
                                ✏️
                            </a>
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="deleteField('${attr}')">
                                ❌
                            </button>
                        </td>
                    </tr>
                `;
            });

            deleteBtn.style.display = currentSectionId ? 'inline-block' : 'none';
            container.style.display = 'block';
        });
}

function deleteField(field) {
    if (!currentSectionId) return alert('Section non définie');

    if (!confirm(`Confirmez-vous la suppression du champ "${field}" ?`)) return;

    fetch(`/admin/sections/${currentSectionId}/fields/${field}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(res => {
        if (res.ok) {
            alert(`Champ "${field}" supprimé`);
            searchSection(document.getElementById('sectionSearch').value);
        } else {
            alert('Erreur lors de la suppression');
        }
    });
}

function deleteSection() {
    if (!currentSectionId) return alert('Section non définie');

    if (!confirm('Confirmez-vous la suppression de la section entière ? Cette action est irréversible.')) return;

    fetch(`/admin/sections/${currentSectionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(res => {
        if (res.ok) {
            alert('Section supprimée avec succès');
            document.getElementById('fieldsTableContainer').style.display = 'none';
            document.getElementById('sectionSearch').value = '';
            currentSectionId = null;
        } else {
            alert('Erreur lors de la suppression de la section');
        }
    });
}
</script>

</div>


  @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if($sections->isNotEmpty())
        <form action="{{ route('admin.storeSectionData') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="section">Sélectionner une section :</label>
            <select name="section" id="section" class="form-select" onchange="fetchTypeInfos(this.value)">
                <option value="">-- Choisir une section --</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>

            <div id="dynamicFormContainer" class="mt-4" style="display: none;">
                <h5>Formulaire dynamique :</h5>
                <div id="dynamicForm"></div>
                <button type="submit" class="btn btn-primary mt-3">Soumettre</button>
            </div>
        </form>
    @else
        <p>Aucune section disponible.</p>
    @endif
    <a href="{{ route('sections.create') }}">Ajouter une section</a>

    <hr>
    <h4>Rendez-vous urgents :</h4>
    @foreach ($rdvUrgents as $rdv)
        <table class="table table-bordered mb-3">
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Date</th>
                <th>Commentaire</th>
            </tr>
            <tr>
                <td>{{ $rdv->user->name ?? $rdv->visiteur->name ?? '-' }}</td>
                <td>{{ $rdv->user ? 'Patient' : ($rdv->visiteur ? 'Visiteur' : '-') }}</td>
                <td>{{ $rdv->user->email ?? $rdv->visiteur->email ?? '-' }}</td>
                <td>{{ $rdv->user->phone_number ?? $rdv->visiteur->telephone ?? '-' }}</td>
                <td>{{ $rdv->date_heure }}</td>
                <td>{{ $rdv->commentaire }}</td>
            </tr>
        </table>
    @endforeach
</div>

<script>
function fetchTypeInfos(sectionId) {
    const formDiv = document.getElementById('dynamicForm');
    const formContainer = document.getElementById('dynamicFormContainer');

    formDiv.innerHTML = '';
    formContainer.style.display = 'none';

    if (!sectionId) return;

    fetch(`/admin/sections/${sectionId}/type-infos`)
        .then(response => response.json())
        .then(data => {
            if (!data.attributs || data.attributs.length === 0) return;

            data.attributs.forEach(attr => {
                let field = '';

                switch (attr) {
                        case 'titre':
                        field = `
                            <div class="mb-3">
                                <label for="input_${attr}" class="form-label">${attr} :</label>
                                <input type="text" class="form-control" name="inputs[${attr}]" id="input_${attr}" placeholder="Entrez ${attr}" required>
                            </div>`;
                        break;
                    case 'contenu':
                        field = `
                            <div class="mb-3">
                                <label for="input_${attr}" class="form-label">${attr} :</label>
                                <textarea class="form-control" name="inputs[${attr}]" id="input_${attr}" rows="4" maxlength="1000" required></textarea>
                            </div>`;
                        break;

                    case 'image':
                        field = `
                            <div class="mb-3">
                                <label for="input_${attr}" class="form-label">${attr} :</label>
                                <input type="file" class="form-control" name="inputs[${attr}]" id="input_${attr}" accept=".jpg,.jpeg,.png" required>
                            </div>`;
                        break;
                        case 'adresse':
                        field = `
                            <div class="mb-3">
                                <label for="input_${attr}" class="form-label">${attr} :</label>
                                <textarea class="form-control" name="inputs[${attr}]" id="input_${attr}" rows="2"></textarea>
                            </div>`;
                        break;


                    default:
                        field = `
                            <div class="mb-3">
                                <label for="input_${attr}" class="form-label">${attr} :</label>
                                <input type="text" class="form-control" name="inputs[${attr}]" id="input_${attr}" placeholder="Entrez ${attr}" required>
                            </div>`;
                }

                formDiv.innerHTML += field;
            });

            formContainer.style.display = 'block';

            // Activer CKEditor pour tous les champs textarea (description ou contenu)
            setTimeout(() => {
                ['description', 'contenu'].forEach(id => {
                    if (document.getElementById(`input_${id}`)) {
                        CKEDITOR.replace(`input_${id}`);
                    }
                });
            }, 100);
        });
}


//barre de recherche de section avec type infos

</script>

</body>
</html>
