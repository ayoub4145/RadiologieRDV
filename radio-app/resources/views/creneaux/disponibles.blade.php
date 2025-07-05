<script>
document.getElementById('service_id').addEventListener('change', function () {
    const serviceId = this.value;
    const tableBody = document.getElementById('creneaux-list');

    if (serviceId) {
        fetch(`/api/creneaux/${serviceId}`)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-red-500">Aucun créneau disponible.</td></tr>';
                }

                data.forEach(creneau => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${creneau.service_id}</td>
                            <td>${new Date(creneau.date).toLocaleDateString()}</td>
                            <td>${creneau.start_time.substring(0, 5)}</td>
                            <td>${creneau.end_time.substring(0, 5)}</td>
                        </tr>`;
                });
            });
    } else {
        tableBody.innerHTML = '';
    }
});
</script>
<table class="min-w-full divide-y divide-gray-200">
    <thead>
        <tr>
            <th class="px-4 py-2">Service</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Heure début</th>
            <th class="px-4 py-2">Heure fin</th>
        </tr>
    </thead>
    <tbody id="creneaux-list">
        <!-- AJAX va remplir ici -->
    </tbody>
</table>
