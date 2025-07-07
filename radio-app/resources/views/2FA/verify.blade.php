<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vérification 2FA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('code'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ $errors->first('code') }}
            </div>
        @endif

        <p>Le code a été envoyé. Si vous avez trouvé des problèmes, <a href="{{ route('2fa.resend') }}">Réessayez !</a></p>

        <form method="POST" action="{{ route('2fa.verify.post') }}">
            @csrf
            <input type="text" name="code" placeholder="Entrez le code 2FA" class="border p-2 rounded">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Vérifier</button>
        </form>
    </div>
</x-app-layout>
