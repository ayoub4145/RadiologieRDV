<x-app-layout>
    <h2>Configuration 2FA</h2>

    <p>Scannez ce QR code avec l'application Google Authenticator :</p>

    <div>{!! $qrCode !!}</div>

    <p>Ou entrez ce code manuellement : <strong>{{ $secret }}</strong></p>

    <form method="POST" action="{{ route('2fa.enable') }}">
        @csrf
        <input type="text" name="code" placeholder="Code à 6 chiffres" required>
        <button type="submit">Activer 2FA</button>
    </form>
</x-app-layout>
