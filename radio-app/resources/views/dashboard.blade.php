<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #1f2937;
        }
        h2, h3 {
            color: #1e293b;
        }
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
        button {
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
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
        button.bg-green-500 {
            background-color: #4ade80;
            color: #065f46;
            font-weight: 600;
        }
        button.bg-green-500:hover {
            background-color: #22c55e;
            color: #064e3b;
        }
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
        #creneaux-pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
        .hidden {
            display: none;
        }
        #choix-message-container div {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

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
                    <div id="success-message" class="mb-4 alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                     <script>
        setTimeout(() => {
            const msg = document.getElementById('success-message');
            if (!msg) return;

            // Ajout transition pour opacité
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';

            // Après la transition, on enlève le message du DOM
            setTimeout(() => {
                msg.remove();
            }, 500); // correspond à la durée de la transition
        }, 5000); // 5000ms = 5 secondes avant disparition
    </script>
                @endif

                @if(isset($services) && count($services) > 0)
                <form method="POST" action="{{ route('rendezvous.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="service_id" class="block font-medium text-sm text-gray-700 dark:text-gray-200">
                            Service
                        </label>
                        <select name="service_id" id="service_id" required>
                            <option value="">-- Choisir un service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->service_name }} {{$service->tarif ? $service->tarif .' MAD' : '' }}
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="date_heure" id="date_heure" required>
                    @error('date_heure')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if(old('creneau_id'))
                        <input type="hidden" name="creneau_id" value="{{ old('creneau_id') }}">
                    @endif

                    <div id="creneaux-container" class="mt-6 hidden">
                        <h3 class="text-lg font-semibold mb-2">Créneaux disponibles :</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure début</th>
                                    <th>Heure fin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="creneaux-list"></tbody>
                        </table>
                        <div id="creneaux-pagination" class="mt-2"></div>
                        <div id="choix-message-container" class="mt-4"></div>
                    </div>

                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="is_urgent" id="is_urgent" class="mr-2"
                               {{ old('is_urgent') ? 'checked' : '' }}
                               onchange="document.getElementById('urgent-msg').classList.toggle('hidden', !this.checked);">
                        <label for="is_urgent">Rendez-vous urgent</label>
                    </div>

                    <div id="urgent-msg" class="mb-4 alert-warning {{ old('is_urgent') ? '' : 'hidden' }}">
                        ⚠️ Les rendez-vous urgents sont sujets à des frais supplémentaires.
                    </div>

                    <div class="mb-4">
                        <label for="commentaire" class="block font-medium text-sm">Commentaire (optionnel)</label>
                        <textarea name="commentaire" id="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" id="btn-voir-rdv" style="background-color:cyan;" class="text-black px-4 py-2 rounded font-bold">
                            Voir mes rendez-vous
                        </button>
                        <button type="submit" name="action" value="prendre" style="background-color:pink;" class="text-black px-4 py-2 rounded font-bold">
                            Prendre rendez-vous
                        </button>
                    </div>

                    <div id="rdv-list-section" class="mt-6 hidden">
                        <h3 class="text-lg font-semibold mb-2">Mes rendez-vous :</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Service</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="rdv-list-body"></tbody>
                        </table>
                    </div>
                </form>
                @else
                    <div class="text-red-600 font-bold">Aucun service disponible.</div>
                @endif
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const urgentCheckbox = document.getElementById('is_urgent');
    const creneauxContainer = document.getElementById('creneaux-container');
    const creneauxList = document.getElementById('creneaux-list');
    const itemsPerPage = 5;
    let creneauxData = [];
    let currentPage = 1;

    serviceSelect.addEventListener('change', fetchCreneaux);
    urgentCheckbox.addEventListener('change', fetchCreneaux);


























    function fetchCreneaux() {
        const serviceId = serviceSelect.value;
        const isUrgent = urgentCheckbox.checked ? 1 : 0;
        currentPage = 1;

        if (!serviceId) {
            creneauxContainer.classList.add('hidden');
            creneauxList.innerHTML = '';
            document.getElementById('creneaux-pagination').innerHTML = '';
            return;
        }

        fetch(`/creneaux/${serviceId}?urgent=${isUrgent}`, { headers: { 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(data => {
                creneauxData = data;
                renderCreneaux();
                creneauxContainer.classList.remove('hidden');
            })
            .catch(error => console.error("Erreur :", error));
    }

    function renderCreneaux() {
        creneauxList.innerHTML = '';
        if (creneauxData.length === 0) {
            creneauxList.innerHTML = `<tr><td colspan="4">Aucun créneau disponible.</td></tr>`;
            document.getElementById('creneaux-pagination').innerHTML = '';
            return;
        }

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = creneauxData.slice(start, end);

        pageData.forEach(c => {
            const dateStr = c.date;
            const startTime = c.time.substring(0, 5);
            const endTime = c.end_time ? c.end_time.substring(0, 5) : '-';
            creneauxList.innerHTML += `
                <tr>
                    <td class="px-4 py-2">${new Date(dateStr).toLocaleDateString('fr-FR', { weekday: 'long' }).toUpperCase()} : ${new Date(dateStr).toLocaleDateString()}</td>
                    <td class="px-4 py-2">${startTime}</td>
                    <td class="px-4 py-2">${endTime}</td>
                    <td class="px-4 py-2 text-center">
                        <button type="button"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded"
                            onclick="choisirCreneau('${dateStr}', '${startTime}', ${c.id})">
                            Choisir
                        </button>
                    </td>
                </tr>`;
        });

        renderPagination(Math.ceil(creneauxData.length / itemsPerPage));
    }

    window.choisirCreneau = function(date, time, id) {
        document.getElementById('date_heure').value = `${date}T${time}`;

        const existingInput = document.querySelector('input[name="creneau_id"]');
        if (existingInput) existingInput.remove();

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'creneau_id';
        input.value = id;
        document.querySelector('form').appendChild(input);

        const msg = document.createElement('div');
        msg.textContent = `✅ Créneau choisi : ${new Date(date).toLocaleDateString()} à ${time}`;
        msg.className = 'p-3 bg-green-100 border border-green-600 rounded';
        const container = document.getElementById('choix-message-container');
        container.innerHTML = '';
        container.appendChild(msg);

        setTimeout(() => msg.remove(), 5000);
    };

    function renderPagination(totalPages) {
        const pag = document.getElementById('creneaux-pagination');
        pag.innerHTML = `
            <button type="button" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">&laquo; Précédent</button>
            <button type="button" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Suivant &raquo;</button>
        `;
    }

    window.changePage = function(page) {
        if (page < 1 || page > Math.ceil(creneauxData.length / itemsPerPage)) return;
        currentPage = page;
        renderCreneaux();
    };

   document.getElementById('btn-voir-rdv').addEventListener('click', function () {
    fetch('/mes-rendezvous', { headers: { 'Accept': 'application/json' } })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Réponse serveur non JSON:", text);
                alert("Erreur côté serveur : réponse invalide.");
                throw new Error("Réponse non-JSON reçue");
            }
        })
        .then(data => {
            if (!Array.isArray(data)) {
                alert("Erreur : la réponse du serveur n'est pas un tableau.");
                console.error(data);
                return;
            }

            const tbody = document.getElementById('rdv-list-body');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4">Aucun rendez-vous trouvé.</td></tr>`;
            } else {
                data.forEach(r => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${r.date}</td>
                            <td>${r.time}</td>
                            <td>${r.service_name}</td>
                            <td>
                                <button style="color:red" onclick="annulerRDV(${r.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    Annuler
                                </button>
                            </td>
                        </tr>`;
                });
            }
            document.getElementById('rdv-list-section').classList.remove('hidden');
        })
        .catch(error => {
            alert("Erreur lors du chargement des rendez-vous");
            console.error(error);
        });
});


    window.annulerRDV = function(id) {
        if (!confirm("Annuler ce rendez-vous ?")) return;
        fetch(`/annuler-rendezvous/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Erreur lors de l\'annulation');
            return res.json();
        })
        .then(() => {
            alert("✅ Rendez-vous annulé !");
            document.getElementById('btn-voir-rdv').click();
        })
        .catch(error => {
            alert("Erreur : " + error.message);
            console.error(error);
        });
    };

    document.querySelector('form').addEventListener('submit', function(e) {
        const action = document.activeElement.value;
        const creneau = document.querySelector('input[name="creneau_id"]');
        if (action === 'prendre' && !creneau) {
            e.preventDefault();
            alert("❌ Veuillez sélectionner un créneau.");
        }
    });

    if (serviceSelect.value) {
        fetchCreneaux();
    }
});
</script>
</x-app-layout>
