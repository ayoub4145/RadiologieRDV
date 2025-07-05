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
                    <div class="mb-4 p-3 text-green-700 bg-green-100 rounded">
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

                        <!-- Date et Heure -->
                        <div class="mb-4">
                            <label for="date_heure" class="block font-medium text-sm text-gray-700 dark:text-gray-200">
                                Date et Heure
                            </label>
                            <input type="datetime-local" name="date_heure" id="date_heure"
                                   value="{{ old('date_heure') }}"
                                   class="mt-1 block w-full rounded-md shadow-sm" required>
                            @error('date_heure')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
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
                            <button type="submit" style="color: green"
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
</x-app-layout>
