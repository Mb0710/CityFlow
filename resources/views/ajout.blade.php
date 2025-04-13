<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestion des types d'objets</title>
  <link rel="stylesheet" href="{{ asset('css/Categorie.css') }}">
</head>

<body>

  <div class="logo-container logo-left">
    <a href="/admin"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>

  <div class="form-container">
    <!-- Section d'ajout -->
    <div class="card-section">
      <h2>Ajouter un type d'objet</h2>
      <form id="formType">
        <div class="form-group">
          <label for="typeInput">Nom du type:</label>
          <input type="text" id="typeInput" name="name" placeholder="Nom du type d'objet" required />
        </div>

        <div class="form-group">
          <label for="descriptionInput">Description:</label>
          <input type="text" id="descriptionInput" name="description" placeholder="Description du type" />
        </div>

        <div id="attributesContainer">
          <h3>Attributs</h3>
          <div class="attributes-list" id="attributesList">
            <!-- Les attributs seront ajoutés ici dynamiquement -->
          </div>
          <button type="button" id="addAttributeBtn" class="secondary-btn">+ Ajouter un attribut</button>
        </div>

        <button type="submit" class="primary-btn">Sauvegarder le type d'objet</button>
      </form>
    </div>

    <!-- Section de liste -->
    <div class="card-section">
      <h2>Types d'objets existants</h2>
      <ul id="typeList" class="list"></ul>
    </div>
  </div>

  <!-- Template pour un nouvel attribut -->
  <template id="attributeTemplate">
    <div class="attribute-item">
      <div class="attribute-header">
        <h4>Attribut</h4>

      </div>

      <div class="form-group">
        <label>Nom technique:</label>
        <input type="text" name="attributes[INDEX].nom" placeholder="Nom technique" required />
      </div>

      <div class="form-group">
        <label>Libellé:</label>
        <input type="text" name="attributes[INDEX].label" placeholder="Libellé affiché" required />
      </div>

      <div class="form-group">
        <label>Type:</label>
        <select name="attributes[INDEX].type" class="attribute-type" required>
          <option value="text">Texte</option>
          <option value="number">Nombre</option>
          <option value="select">Liste déroulante</option>
        </select>
      </div>

      <div class="options-container" style="display: none;">
        <label>Options (une par ligne):</label>
        <textarea name="attributes[INDEX].options_text" placeholder="Une option par ligne" rows="3"></textarea>
      </div>
    </div>
  </template>

  <!-- Message visuel temporaire -->
  <div id="confirmation-message" class="message-confirmation" style="display: none;"></div>

  <script src="{{ asset('js/categorie.js') }}"></script>
</body>

</html>