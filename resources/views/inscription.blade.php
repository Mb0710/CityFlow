<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Validation des Inscriptions</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/Inscription.css') }}">
</head>

<body>
  <!--  Logo du site qui permet aussi de revenir au dashboard et titre nuageux-->
  <div class="titre-container">
    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Validation des Inscriptions</div>
  </div>
  <div class="logo-container logo-left">
    <a href="/admin"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>
  <div class="device-grid" id="inscriptionGrid">
    <!-- Les inscriptions en attente seront chargées ici -->
  </div>

  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Demande traitée.
  </div>

  <script src="{{ asset('js/Inscription.js') }}"></script>
</body>

</html>