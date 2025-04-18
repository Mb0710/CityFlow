<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestion des Appareils</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/Gestion.css') }}">
</head>

<body data-is-expert="{{ Auth::user()->level === 'expert' ? 'true' : 'false' }}" ,
  data-is-Intermediate="{{ Auth::user()->level === 'intermédiaire' ? 'true' : 'false' }}"
  data-is-advanced="{{ Auth::user()->level === 'avancé' ? 'true' : 'false' }}">
  <!--  Logo du site qui permet aussi de revenir au dashboard-->
  <div class="logo-container logo-left">
    <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>
  <!--  Titre avec effet nuage -->
  <div class="titre-container">
    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">
    <div class="cloud-title">Gestion</div>
  </div>


  <!--  Bouton pour ouvrir le formulaire d'ajout uniquement visible pour les utilisateurs avancé et expert  -->
  @if(Auth::check() && in_array(Auth::user()->level, ['avancé', 'expert'])) <button class="add-device-btn"
  onclick="toggleDeviceForm()">+</button>
  @endif

  <!--  Boutons de statistiques et rapport  -->
  @if(Auth::check() && in_array(Auth::user()->level, ['expert']))
    <div class="management-buttons">
    <button class="stats-btn" onclick="window.location.href='/stats'">
      <i class='bx bx-stats'></i> Statistiques
    </button>
    <button class="report-btn" onclick="window.location.href='/rapport'">
      <i class='bx bx-file'></i> Rapport
    </button>
    </div>
  @endif

  <!--  Menu déroulant pour filtrer les appareils par catégorie -->
  <div class="menu-tri">
    <select id="filtreCategorie" onchange="filtrerCategorie()">
      <option value="tous">Tous</option>
      <!-- Les options seront chargées dynamiquement -->
    </select>
  </div>

  <!--  Formulaire pour ajouter ou modifier un appareil  -->
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
      <small class="form-text text-muted">Latitude entre 49.015 et 49.055, Longitude entre 2.02 et 2.11</small>

      <div class="form-group">
        <label for="categorie">Catégorie:</label>
        <select id="categorie" name="categorie" data-refresh="true" required>
          <option value="">Sélectionnez une catégorie</option>
          <!-- Les options seront chargées dynamiquement par JavaScript -->
        </select>
        <button type="button" id="refresh-categories" class="btn-refresh">↻</button>
      </div>

      <div id="attributs-dynamiques" class="attributs-container">

      </div>

      <button type="submit">Enregistrer</button>
    </form>
  </div>

  <!--  Grille des appareils affichés dynamiquement par JS  -->
  <div class="device-grid"></div>

  <!--  Message de confirmation temporaire après suppression -->
  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Message envoyé à l’administrateur pour confirmation de la suppression.
  </div>


  <script src="{{ asset('js/Gestion.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Appeler la route pour mettre à jour les batteries quand la page se charge
      fetch('/api/update-batteries')
        .then(response => response.json())
        .then(data => {
          console.log('Mise à jour des batteries:', data);
        });

  </script>
</body>

</html>