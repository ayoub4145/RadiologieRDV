<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <!-- Phone number -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Phone Number (e.g., 0611223344)')" /> {{-- Changé "Email" en "Phone Number" --}}
            <x-text-input
                id="phone_number"                               {{-- Changé "email" en "phone_number" --}}
                class="block mt-1 w-full"
                type="tel"                                      {{-- Changé "email" en "tel" pour le type de l'input --}}
                name="phone_number"                             {{-- Changé "email" en "phone_number" --}}
                :value="old('phone_number')"                    {{-- Changé "email" en "phone_number" --}}
                required                                        {{-- Le numéro de téléphone est souvent requis --}}
                autocomplete="tel"                              {{-- Changé "username" en "tel" pour l'autocomplétion --}}
            />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" /> {{-- Changé "email" en "phone_number" --}}
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
