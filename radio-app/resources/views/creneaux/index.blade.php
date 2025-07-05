<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Créneaux disponibles') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-200 text-red-800 p-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Service</th>
                            <th class="px-4 py-2">Jour</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Heure</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creneaux as $creneau)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $creneau->service->nom }}</td>
                                <td class="px-4 py-2">{{ $creneau->day }}</td>
                                <td class="px-4 py-2">{{ $creneau->date }}</td>
                                <td class="px-4 py-2">{{ $creneau->time }}</td>
                                <td class="px-4 py-2">
                                    <form method="POST" action="{{ route('creneaux.reserver') }}">
                                        @csrf
                                        <input type="hidden" name="creneau_id" value="{{ $creneau->id }}">
                                        <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">
                                            Réserver
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">Aucun créneau disponible.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
