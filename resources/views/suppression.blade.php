<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Suppression</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/suppression.css') }}">
</head>

<body>


  <div class="titre-container">
    <div class="logo-site">
      <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo" class="logo-responsive"></a>
    </div>

    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Suppression</div>
  </div>

  <!-- Grille des demandes de suppression -->
  <div class="device-grid" id="suppressionGrid"></div>

  <!-- Message visuel de confirmation -->
  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Suppression validée avec succès.
  </div>

  <script src="{{ asset('js/suppression.js') }}"></script>
</body>

</html>