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
            <div class="cloud"><button>Explorer la ville</button></div>
            <div class="cloud"><button>Mon Profile</button></div>
            <div class="cloud"><button>Ameliorer ma ville</button></div>
            <div class="cloud" id="deco"><button>Deconnexion</button></div>
        </div>

    </div>
    <script type="module" src="{{ asset('js/model3d.js') }}"></script>

</body>

</html>