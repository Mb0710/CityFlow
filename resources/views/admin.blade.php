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
    <!-- simple menu 3d pour l'interface admin -->
    <div class="wrapper">
        <div id="container3D"></div>
    </div>
    <div class="box">
        <h2>Telecharger le dernier rapport </h2>
        <a href="admin/rapportUtilisateur" class="download-btn">
            <i class='bx bx-download'></i> Télécharger le rapport
        </a>
    </div>
    <script type="module" src="{{ asset('js/adminDashboard3d.js') }}"></script>
</body>



</html>