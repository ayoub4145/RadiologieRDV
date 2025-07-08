<x-app-layout>
    <x-slot name="header">Activer l'authentification à deux facteurs</x-slot>

    <div>
        <p>Scannez ce QR Code avec Google Authenticator :</p>
        <div>{!! $QR_Image !!}</div>

        <form method="POST" action="{{ route('2fa.enable') }}">
            @csrf
            <input type="text" name="code" placeholder="Entrez le code à 6 chiffres" required>
            <button type="submit">Activer</button>
        </form>
    </div>
</x-app-layout>
