document.addEventListener("DOMContentLoaded", () => {
  const formType = document.getElementById("formType");
  const typeList = document.getElementById("typeList");
  const confirmationMessage = document.getElementById("confirmation-message");
  const attributesList = document.getElementById("attributesList");
  const addAttributeBtn = document.getElementById("addAttributeBtn");
  const attributeTemplate = document.getElementById("attributeTemplate");

  // Récupérer le jeton CSRF pour les requêtes
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Compteur pour les attributs
  let attributeCounter = 0;

  // Fonction pour afficher un message
  function showMessage(text, isError = false) {
    confirmationMessage.textContent = isError ? `❌ ${text}` : `✅ ${text}`;
    confirmationMessage.style.backgroundColor = isError ? "#ffdddd" : "#ddffdd";
    confirmationMessage.style.display = "block";
    setTimeout(() => {
      confirmationMessage.style.display = "none";
    }, 4000);
  }

  // Charger les types d'objets existants
  async function loadObjectTypes() {
    try {
      const response = await fetch('/admin/object-types');
      const result = await response.json();

      if (result.success) {
        displayObjectTypes(result.data);
      } else {
        showMessage("Impossible de charger les types d'objets", true);
      }
    } catch (error) {
      console.error("Erreur lors du chargement des types:", error);
      showMessage("Erreur de connexion au serveur", true);
    }
  }

  // Afficher les types d'objets dans la liste
  function displayObjectTypes(types) {
    typeList.innerHTML = '';

    if (types.length === 0) {
      const emptyItem = document.createElement('li');
      emptyItem.textContent = "Aucun type d'objet disponible";
      emptyItem.className = "empty-list";
      typeList.appendChild(emptyItem);
      return;
    }

    types.forEach(type => {
      const li = document.createElement('li');
      li.className = "type-item";

      // Informations générales
      const typeInfo = document.createElement('div');
      typeInfo.className = "type-info";
      typeInfo.innerHTML = `<h3>${type.name}</h3><p>${type.description || 'Sans description'}</p>`;

      // Liste des attributs
      const attributesContainer = document.createElement('div');
      attributesContainer.className = "type-attributes";

      if (type.attributes && type.attributes.length > 0) {
        const attributesList = document.createElement('ul');
        type.attributes.forEach(attr => {
          const attrItem = document.createElement('li');
          attrItem.innerHTML = `<strong>${attr.label}</strong> (${attr.nom}): ${attr.type}`;

          if (attr.type === 'select' && attr.options && attr.options.length > 0) {
            attrItem.innerHTML += ` [${attr.options.join(', ')}]`;
          }

          attributesList.appendChild(attrItem);
        });
        attributesContainer.appendChild(attributesList);
      } else {
        const noAttrs = document.createElement('p');
        noAttrs.textContent = "Aucun attribut";
        noAttrs.className = "no-attributes";
        attributesContainer.appendChild(noAttrs);
      }

      // Bouton de suppression
      const deleteBtn = document.createElement('button');
      deleteBtn.className = "delete-btn";
      deleteBtn.textContent = "Supprimer";
      deleteBtn.dataset.id = type.id;
      deleteBtn.addEventListener('click', deleteObjectType);

      // Assembler l'élément de liste
      li.appendChild(typeInfo);
      li.appendChild(attributesContainer);
      li.appendChild(deleteBtn);
      typeList.appendChild(li);
    });
  }

  // Ajouter un nouvel attribut au formulaire
  function addAttribute() {

    const attributeItems = attributesList.querySelectorAll('.attribute-item');
    if (attributeItems.length >= 1) {
      showMessage("Un seul attribut est autorisé par type d'objet", true);
      return;
    }
    const newAttribute = document.importNode(attributeTemplate.content, true);

    // Mettre à jour l'index
    const inputs = newAttribute.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      if (input.name) {
        input.name = input.name.replace('INDEX', attributeCounter);
      }
    });

    // Configuration du type d'attribut (afficher/masquer les options)
    const typeSelect = newAttribute.querySelector('.attribute-type');
    const optionsContainer = newAttribute.querySelector('.options-container');

    typeSelect.addEventListener('change', function () {
      optionsContainer.style.display = this.value === 'select' ? 'block' : 'none';
    });



    attributesList.appendChild(newAttribute);
    attributeCounter++;

    addAttributeBtn.disabled = true;
    addAttributeBtn.textContent = "Maximum d'attributs atteint";
  }

  // Ajouter un nouveau type d'objet
  async function addObjectType(event) {
    event.preventDefault();

    const attributeItems = attributesList.querySelectorAll('.attribute-item');
    if (attributeItems.length === 0) {
      showMessage("Un attribut est requis pour chaque type d'objet", true);
      return;
    } else if (attributeItems.length > 1) {
      showMessage("Un seul attribut est autorisé par type d'objet", true);
      return;
    }

    // Collecter les données du formulaire
    const formData = new FormData(formType);
    const typeName = formData.get('name');
    const typeDescription = formData.get('description');

    // Collecter les attributs
    const attributes = [];


    attributeItems.forEach((item, index) => {
      const nom = formData.get(`attributes[${index}].nom`);
      const label = formData.get(`attributes[${index}].label`);
      const type = formData.get(`attributes[${index}].type`);

      const attribute = {
        nom: nom,
        label: label,
        type: type
      };

      // Traiter les options pour les listes déroulantes
      if (type === 'select') {
        const optionsText = formData.get(`attributes[${index}].options_text`);
        const options = optionsText.split('\n')
          .map(option => option.trim())
          .filter(option => option.length > 0);

        attribute.options = options;
      }

      attributes.push(attribute);
    });

    // Envoyer les données au serveur
    try {
      const response = await fetch('/admin/object-types', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          name: typeName,
          description: typeDescription,
          attributes: attributes
        })
      });

      const result = await response.json();

      if (result.success) {
        showMessage(`Type d'objet "${typeName}" ajouté avec succès`);
        formType.reset();
        attributesList.innerHTML = ''; // Vider la liste d'attributs
        attributeCounter = 0;
        loadObjectTypes();
      } else {
        const errorMsg = result.errors ? Object.values(result.errors).flat().join(', ') : "Erreur lors de l'ajout";
        showMessage(errorMsg, true);
      }
    } catch (error) {
      console.error("Erreur lors de l'ajout du type:", error);
      showMessage("Erreur de connexion au serveur", true);
    }
  }

  // Supprimer un type d'objet
  async function deleteObjectType(event) {
    const typeId = event.target.dataset.id;
    const typeName = event.target.parentElement.querySelector('.type-info h3').textContent;

    if (!confirm(`Êtes-vous sûr de vouloir supprimer le type "${typeName}" ?`)) {
      return;
    }

    try {
      const response = await fetch(`/admin/object-types/${typeId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken
        }
      });

      const result = await response.json();

      if (result.success) {
        showMessage(`Type d'objet "${typeName}" supprimé avec succès`);
        loadObjectTypes();
      } else {
        showMessage(result.message, true);
      }
    } catch (error) {
      console.error("Erreur lors de la suppression du type:", error);
      showMessage("Erreur de connexion au serveur", true);
    }
  }

  // Configurer les écouteurs d'événements
  formType.addEventListener('submit', addObjectType);
  addAttributeBtn.addEventListener('click', addAttribute);

  // Charger les types initiaux
  loadObjectTypes();
});