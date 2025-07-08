<x-app-layout>
    <h2>Vérification 2FA</h2>

    @if ($errors->any())
        <div style="color: red">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <input type="text" name="code" placeholder="Code à 6 chiffres" required>
        <button type="submit">Vérifier</button>
    </form>
</x-app-layout>
