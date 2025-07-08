<x-app-layout>
    <h2>Configuration 2FA</h2>

    <p>Scannez ce QR code avec l'application <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=fr&pli=1" title="Installer Google Authenticate ou autre OTP app pour générer le 2FA code" target="blank" style="font-size: 18pt;color:rgb(42, 35, 35)" >Google Authenticator</a> :</p>

    <div>{!! $qrCode !!}</div>

    <p>Ou entrez ce code manuellement : <strong>{{ $secret }}</strong></p>

    <form method="POST" action="{{ route('2fa.enable') }}">
        @csrf
        <input type="text" name="code" placeholder="Code à 6 chiffres" required>
        <button type="submit">Activer 2FA</button>
    </form>
</x-app-layout>
