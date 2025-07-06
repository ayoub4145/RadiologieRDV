<style>
/* Zone principale */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f1f5f9;
    color: #1f2937;
}

/* Titres */
h2, h3 {
    color: #1e293b;
}

/* Messages de succès et alerte */
.alert-success {
    color: #047857;
    background-color: #d1fae5;
    border: 1px solid #10b981;
    padding: 10px;
    border-radius: 6px;
}

.alert-warning {
    color: #b45309;
    background-color: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 12px;
    border-radius: 6px;
}

/* Formulaires */
select, input[type="datetime-local"], textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    background-color: #fff;
}

select:focus, input:focus, textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px #bfdbfe;
}

/* Boutons */
button {
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

/* Bouton principal */
button[type="submit"] {
    background-color: #2563eb;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
}

button[type="submit"]:hover {
    background-color: #1d4ed8;
}

/* Bouton "Choisir" */
button.bg-green-500 {
    background-color: #4ade80;
    color: #065f46;
    font-weight: 600;
}

button.bg-green-500:hover {
    background-color: #22c55e;
    color: #064e3b;
}

/* Table des créneaux */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
}

th {
    background-color: #f8fafc;
    font-weight: bold;
}

/* Pagination */
#creneaux-pagination {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
}

#creneaux-pagination button {
    margin: 0 5px;
    padding: 6px 14px;
    border-radius: 6px;
    border: none;
    background-color: #e5e7eb;
    color: #374151;
    font-weight: 600;
}

#creneaux-pagination button.bg-blue-500 {
    background-color: #2563eb;
    color: white;
}

#creneaux-pagination button:hover:not(.bg-blue-500) {
    background-color: #cbd5e1;
    color: #1e293b;
}

/* Champ caché affiché dynamiquement */
.hidden {
    display: none;
}

/* Message de confirmation du créneau choisi */
#choix-message-container div {
    animation: fadeIn 0.3s ease-in-out;
}

/* Animation douce */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-6">
                {{ __('Prise de rendez-vous') }}
            </h2>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 text-green-700 bg-green-100 rounded" style="colo">
                        {{ session('success') }}
                    </div>
                @endif

                @if(isset($services) && count($services) > 0)
                    <form method="POST" action="{{ route('rendezvous.store') }}">
                        @csrf

                        <!-- Service -->
                        <div class="mb-4">
                            <label for="service_id" class="block font-medium text-sm text-gray-700 dark:text-gray-200">
                                Service
                            </label>
                            <select name="service_id" id="service_id" required class="mt-1 block w-full rounded-md shadow-sm">
                                <option value="">-- Choisir un service --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <input type="hidden" name="date_heure" id="date_heure" required>

                        <!-- Créneaux dynamiques -->
                        <div id="creneaux-container" class="mt-6 hidden">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Créneaux disponibles :</h3>
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Date</th>
                                        <th class="px-4 py-2">Heure début</th>
                                        <th class="px-4 py-2">Heure fin</th>
                                        <th class="px-4 py-2 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="creneaux-list">
                                    <!-- AJAX remplira ici -->
                                </tbody>
                            </table>
                        <div id="creneaux-pagination" class="mt-2 flex justify-center"></div>
                        <div id="choix-message-container" class="mt-4"></div>

                        </div>
                        <!-- Urgent -->
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" style="color: red" name="is_urgent" id="is_urgent" class="mr-2"
                                   {{ old('is_urgent') ? 'checked' : '' }}
                                   onchange="document.getElementById('urgent-msg').classList.toggle('hidden', !this.checked);">
                            <label for="is_urgent" class="text-gray-700 dark:text-gray-200">
                                Rendez-vous urgent
                            </label>
                        </div>

                        <!-- Alerte urgence -->
                        <div id="urgent-msg" class="mb-4 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500 p-3 rounded {{ old('is_urgent') ? '' : 'hidden' }}">
                            ⚠️ Les rendez-vous urgents sont sujets à des frais supplémentaires et seront validés manuellement.
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-4">
                            <label for="commentaire" class="block font-medium text-sm text-gray-700 dark:text-gray-200">
                                Commentaire (optionnel)
                            </label>
                            <textarea name="commentaire" id="commentaire" rows="3"
                                      class="mt-1 block w-full rounded-md shadow-sm">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end">
                            <button type="submit" style="color: rgb(13, 1, 1)" name="action" value="prendre"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                Prendre rendez-vous
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-red-600 font-bold">
                        Aucun service disponible, impossible de prendre rendez-vous.
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- Ajout de la pagination côté client pour les créneaux --}}
<script>
    window.choisirCreneau = function(date, time, creneauId) {
    // Remplit l'input caché et le form service
    document.getElementById('date_heure').value = `${date}T${time}`;

    // Gérer input caché creneau_id
    const existingInput = document.querySelector('input[name="creneau_id"]');
    if (existingInput) existingInput.remove();

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'creneau_id';
    input.value = creneauId;
    document.querySelector('form').appendChild(input);

    // Conteneur du message sous la pagination
    const msgContainer = document.getElementById('choix-message-container');

    // Supprime message précédent s’il existe
    msgContainer.innerHTML = '';

    // Crée un message succès
    const msg = document.createElement('div');
    msg.textContent = `✅ Vous avez bien choisi le créneau du ${new Date(date).toLocaleDateString()} à ${time}`;
    msg.className = 'p-3 text-green-800 bg-green-100 border border-green-600 rounded';

    // Insère le message dans le conteneur
    msgContainer.appendChild(msg);

    // Le message disparaît après 5 secondes
    setTimeout(() => {
        msg.remove();
    }, 5000);
};

document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const urgentCheckbox = document.getElementById('is_urgent');
    const creneauxContainer = document.getElementById('creneaux-container');
    const creneauxList = document.getElementById('creneaux-list');
    const itemsPerPage = 5;
    let creneauxData = [];
    let currentPage = 1;

    serviceSelect.addEventListener('change', fetchCreneaux);
    if (urgentCheckbox) {
        urgentCheckbox.addEventListener('change', fetchCreneaux);
    }

    function renderPagination(totalPages) {
        let paginationHtml = '';
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<button type="button" class="mx-1 px-2 py-1 rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200'}" onclick="changePage(${i})">${i}</button>`;
        }
        document.getElementById('creneaux-pagination').innerHTML = paginationHtml;
    }

    window.changePage = function(page) {
        currentPage = page;
        renderCreneaux();
    }

    function renderCreneaux() {
        creneauxList.innerHTML = '';
        if (creneauxData.length === 0) {
            creneauxList.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-red-500">Aucun créneau disponible.</td>
                </tr>`;
            document.getElementById('creneaux-pagination').innerHTML = '';
            return;
        }
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = creneauxData.slice(start, end);

        pageData.forEach(c => {
            const dateStr = c.date;
            const startTime = c.time.substring(0, 5);
            const endTime = c.end_time ? c.end_time.substring(0,5) : '-';
            creneauxList.innerHTML += `
                <tr>
                    <td class="px-4 py-2">${new Date(dateStr).toLocaleDateString()}</td>
                    <td class="px-4 py-2">${startTime}</td>
                    <td class="px-4 py-2">${endTime}</td>
                    <td class="px-4 py-2 text-center">
                        <button type="button"
                            style="color: green;background-color: #e0f7e0; border: none; border-radius: 4px;"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded"
                            onclick="remplirDateHeure('${dateStr}', '${startTime}')"
                            onmouseover="this.style.backgroundColor='#d4f0d4';" onmouseout="this.style.backgroundColor='#e0f7e0';"
                            >
                            Choisir
                        </button>
                    </td>
                </tr>`;
        });

        const totalPages = Math.ceil(creneauxData.length / itemsPerPage);
        renderPagination(totalPages);
    }

    function fetchCreneaux() {
        const serviceId = serviceSelect.value;
        const isUrgent = urgentCheckbox && urgentCheckbox.checked ? 1 : 0;
        currentPage = 1;

        if (!serviceId) {
            creneauxContainer.classList.add('hidden');
            creneauxList.innerHTML = '';
            document.getElementById('creneaux-pagination').innerHTML = '';
            return;
        }

        fetch(`/api/creneaux/${serviceId}?urgent=${isUrgent}`)
            .then(response => response.json())
            .then(data => {
                creneauxData = data;
                renderCreneaux();
                creneauxContainer.classList.remove('hidden');
            })
            .catch(error => {
                console.error("Erreur lors de la récupération des créneaux :", error);
            });
    }

    window.remplirDateHeure = function (date, time) {
        document.getElementById('date_heure').value = `${date}T${time}`;
    };

    // Ajout du conteneur de pagination si absent
    if (!document.getElementById('creneaux-pagination')) {
        const pagDiv = document.createElement('div');
        pagDiv.id = 'creneaux-pagination';
        pagDiv.className = 'mt-2 flex justify-center';
        creneauxContainer.parentNode.insertBefore(pagDiv, creneauxContainer.nextSibling);
    }

    // Si un service est déjà sélectionné (ex: après validation échouée), charger les créneaux
    if (serviceSelect.value) {
        fetchCreneaux();
    }
});
</script>
</x-app-layout>
