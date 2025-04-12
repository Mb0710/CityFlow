<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="icon" href="{{ asset("./assets/logo2.png") }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<!--  on passe le role et le status de l'utilisateur au javascript via la balise body pour ensuite pourvoir gerer les autorisations -->

<body data-user-role="{{ $user->level}}" data-is-admin="{{ $user->is_admin}}">
    <div class="box" id="errorBox">
        <h2>Non ! Vous devez avoir un rôle plus élevé pour accéder à la gestion.</h2>
    </div>
    <div class="box" id="errorBox2">
        <h2>Non ! Vous n'êtes pas admin ! </h2>
    </div>
    <!--  Import du model3d -->
    <div class="wrapper">
        <div id="container3D"></div>
    </div>
    <!--  Utilisation de la methode getUserLvl et getNextPoint pour pouvoir afficher des données lié a la progression de l'utilisateur -->
    <div class="box">
        <h2>Bienvenue {{ $user->login}}, vous avez {{$user->points}} points.</h2>
        @php $levelInfo = $user->getUserLevel(); @endphp
        @if($levelInfo['next'])
            <h2>Plus que {{ $levelInfo['points_needed'] }} points pour atteindre le rang {{ $levelInfo['next'] }}.</h2>
        @else
            <h2>Félicitations, vous avez atteint le rang maximum.</h2>
        @endif
    </div>
    <div class="box right">
        <h2>Vous etes à {{ $user->login_streak}} jours de connexion consécutif ! </h2>
        <h2>Connectez-vous demain pour gagner {{ $user->getNextLoginPoints()}} points. </h2>
    </div>
    <script type="module" src="{{ asset('js/dashboard3d.js') }}"></script>
    <!--  requete à user.data qui via une methode associé nous retourne les données de l'utilisateur dans "data"-->
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route("user.data") }}')
                .then(response => response.json())
                .then(data => {
                    console.log(data.user);

                });
        });
    </script>
</body>



</html>