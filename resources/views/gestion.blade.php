<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestion des Appareils</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Feuille de style CSS -->
  <link rel="stylesheet" href="{{ asset('css/Gestion.css') }}">
</head>

<body>

  <div class="titre-container">
    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">
    <div class="cloud-title">Gestion</div>
  </div>


  <!-- === Bouton pour ouvrir le formulaire d'ajout === -->
  <button class="add-device-btn" onclick="toggleDeviceForm()">+</button>

  <!-- === Menu déroulant pour filtrer les appareils par catégorie === -->
  <div class="menu-tri">
    <select id="filtreCategorie" onchange="filtrerCategorie()">
      <option value="tous">Tous</option>
      <option value="lampadaire">Lampadaire</option>
      <option value="capteur_pollution">Capteur pollution</option>
      <option value="borne_bus">Borne de bus</option>
      <option value="panneau_information">Panneau d'information</option>
      <option value="caméra">Caméra</option>
    </select>
  </div>

  <!-- === Formulaire pour ajouter ou modifier un appareil === -->
  <div class="device-form-container" id="deviceForm" style="display: none;">
    <button class="close-form" onclick="closeForm()">✖</button>
    <form class="device-form">
      <h3>Ajouter un appareil</h3>

      <label for="nom">Nom de l'appareil</label>
      <input type="text" id="nom" name="nom" required />

      <label for="batterie">Batterie (%)</label>
      <input type="number" id="batterie" name="batterie" min="0" max="100" value="100" />

      <label for="statut">Statut</label>
      <select id="statut" name="statut" required>
        <option value="en ligne">En ligne</option>
        <option value="hors ligne">Hors ligne</option>
      </select>

      <label for="coordonnees">Coordonnées (lat,lng)</label>
      <input type="text" id="coordonnees" name="coordonnees" required placeholder="49.035,2.065" />

      <label for="categorie">Catégorie</label>
      <select id="categorie" name="categorie" required>
        <option value="">-- Sélectionner --</option>
        <option value="lampadaire">Lampadaire</option>
        <option value="capteur_pollution">Capteur pollution</option>
        <option value="borne_bus">Borne de bus</option>
        <option value="panneau_information">Panneau d'information</option>
        <option value="caméra">Caméra</option>
      </select>

      <div id="attributs-dynamiques" class="attributs-container">

      </div>

      <button type="submit">Enregistrer</button>
    </form>
  </div>

  <!-- === Grille des appareils affichés dynamiquement par JS === -->
  <div class="device-grid"></div>

  <!-- === Message de confirmation temporaire après suppression === -->
  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Message envoyé à l’administrateur pour confirmation de la suppression.
  </div>

  <!-- === Script JS principal === -->
  <script src="{{ asset('js/Gestion.js') }}"></script>
</body>

</html>