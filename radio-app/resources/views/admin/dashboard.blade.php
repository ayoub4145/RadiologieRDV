<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <title>Admin dash</title>
</head>
<body>


<H1>Bonjour admin</H1>
<p>Bienvenue sur le tableau de bord de l'administrateur.</p>
<p>Vous pouvez gérer les rendez-vous, les utilisateurs et d'autres paramètres du système.</p>
@foreach ($rdvUrgents as $rdvUrgent)
    <div class="alert alert-warning">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom complet du patient</th>
                    <th>Patient ou Visiteur</th>
                    <th>Email</th>
                    <th>Numéro de téléphone</th>
                    <th>Date et heure du rendez-vous</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->name }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            Patient
                        @elseif($rdvUrgent->visiteur)
                            Visiteur
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->email }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->email }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rdvUrgent->user)
                            {{ $rdvUrgent->user->phone_number }}
                        @elseif($rdvUrgent->visiteur)
                            {{ $rdvUrgent->visiteur->telephone }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $rdvUrgent->date_heure }}</td>
                    <td>
                       {{$rdvUrgent->commentaire}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endforeach

</body>
</html>
