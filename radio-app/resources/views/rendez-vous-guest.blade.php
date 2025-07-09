<H2>Bonjour guest !</H2>
                @if(isset($services) && count($services) > 0)
                <form method="POST" action="{{ route('visiteur.rendezvous.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="service_id" class="block font-medium text-sm text-gray-700 dark:text-gray-200">
                            Service
                        </label>
                        <select name="service_id" id="service_id" required>
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
                    <td class="px-4 py-2">${new Date(dateStr).toLocaleDateString()}</td>
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
