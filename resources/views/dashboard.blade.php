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

<body>
    <div class="wrapper">
        <div id="container3D"></div>
        <div class="logo-container">
            <img src="./assets/logo.png" alt="City Flow Logo">
            <button>Explorer la map</button>
            <button>Ajouter des elements</button>
            <button>Mon Profile</button>
            <button id="deco">Deconnexion</button>
        </div>

    </div>
    <script type="module" src="{{ asset('js/model3d.js') }}"></script>

</body>

</html>