<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Historique des utilisateurs</title>
  <link rel="stylesheet" href="{{ asset('css/historique.css') }}">
</head>

<body>
  <!--  Logo du site qui permet aussi de revenir au dashboard et titre nuageux-->
  <div class="titre-container">
    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Inspections</div>
  </div>
  <div class="logo-container logo-left">
    <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>

  <div class="menu-tri">
    <select id="filtreCategorie" onchange="filtrerCategorie()">
      <option value="tous">Tous</option>
      <option value="Recharge">Recharge</option>
      <option value="Modification">Modification</option>
      <option value="Ajout">Ajout</option>
      <option value="Signalement">Signalement</option>
    </select>
  </div>


  <div class="actions-grid" id="actionsContainer"></div>


  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Demande traitée.
  </div>

  <script src="{{ asset('js/historique.js') }}"></script>
</body>

</html>