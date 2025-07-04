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
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Prise de rendez-vous') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('rendezvous.store') }}">
                    @csrf

                    <!-- Service -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="service_id">
                            Service
                        </label>
                        <select name="service_id" id="service_id" class="mt-1 block w-full rounded-md shadow-sm">
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->nom }} ({{ $service->duree }} min, {{ $service->tarif }} MAD)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date et heure -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="date_heure">
                            Date et Heure
                        </label>
                        <input type="datetime-local" name="date_heure" id="date_heure"
                               class="mt-1 block w-full rounded-md shadow-sm" required>
                    </div>

                    <!-- Urgent -->
                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="is_urgent" id="is_urgent" class="mr-2">
                        <label for="is_urgent" class="text-gray-700 dark:text-gray-200">Rendez-vous urgent</label>
                    </div>

                    <!-- Commentaire -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="commentaire">
                            Commentaire (optionnel)
                        </label>
                        <textarea name="commentaire" id="commentaire" rows="3"
                                  class="mt-1 block w-full rounded-md shadow-sm"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Prendre rendez-vous
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

