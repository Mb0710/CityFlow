<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter Catégories</title>
  <link rel="stylesheet" href="{{ asset('css/Categorie.css') }}">
</head>

<body>

  <!--  Logo du site qui permet aussi de revenir au dashboard et titre nuageux-->
  <div class="titre-container">
    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Catégories</div>
  </div>
  <div class="logo-container logo-left">
    <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>

  <!-- Conteneur principal unique -->
  <div class="form-container">

    <!-- Formulaire unique -->
    <div class="card-section">
      <h2>Ajouter une catégorie</h2>
      <form id="formCategorie">
        <input type="text" id="categorieInput" name="Categorie" placeholder="Nom de la catégorie" required />
        <input type="text" id="uniteInput" name="nom_attribut" placeholder="Nom de l'attribut " required />
        <input type="text" id="uniteInput" name="valeurs" placeholder="Valeurs" required />
        <button type="submit">Ajouter</button>
      </form>

      <!-- Liste affichant les couples catégorie / unité -->
      <ul id="categorieList" class="list"></ul>
    </div>
  </div>

  <!-- Message visuel temporaire -->
  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Élément ajouté avec succès.
  </div>

  <script src="{{ asset('js/Categorie.js') }}"></script>
</body>

</html>