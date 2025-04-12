// Variable globale pour suivre la carte en cours d'édition
let carteEnEdition = null;

const attributsParCategorie = {
  'lampadaire': [
    { nom: 'luminosite', label: 'intensite', type: 'select', options: ['faible', 'moyenne', 'forte'] },
  ],
  'capteur_pollution': [
    { nom: 'type_capteur', label: 'Type de capteur', type: 'select', options: ['co2', 'particules', 'nox'] },
  ],
  'borne_bus': [
    { nom: 'ligne', label: 'Ligne de bus', type: 'text' },
  ],
  'panneau_information': [
    { nom: 'type_affichage', label: 'Type d\'affichage', type: 'select', type: 'text' },
  ],
  'caméra': [
    { nom: 'resolution', label: 'Résolution', type: 'select', options: ['720p', '1080p', '4K'] },
  ]
};


// Code exécuté une fois le DOM entièrement chargé
document.addEventListener("DOMContentLoaded", () => {
  const deviceForm = document.getElementById("deviceForm");
  const deviceGrid = document.querySelector(".device-grid");
  const form = document.querySelector(".device-form");

  // Charger tous les appareils au chargement de la page
  chargerAppareils();

  // Gestion de la soumission du formulaire (ajout ou modification)
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    // Récupération des valeurs saisies
    const nom = document.getElementById("nom").value;
    const batterie = document.getElementById("batterie").value;
    const statut = document.getElementById("statut").value;
    const coordonnees = document.getElementById("coordonnees").value;
    const categorie = document.getElementById("categorie").value;


    const attributs = {};

    if (attributsParCategorie[categorie]) {
      attributsParCategorie[categorie].forEach(attribut => {
        const champAttribut = document.getElementById(`attr_${attribut.nom}`);

        if (champAttribut && champAttribut.value) {
          let valeur;

          if (attribut.type === 'checkbox') {
            valeur = champAttribut.checked;
          } else {
            valeur = champAttribut.value;
          }

          attributs[attribut.nom] = valeur;
        }
      });
    }

    const donnees = {
      name: nom,
      battery_level: batterie,
      status: statut === "en_ligne" ? "actif" : "inactif",
      lat: coordonnees.split(',')[0] || null,
      lng: coordonnees.split(',')[1] || null,
      type: categorie,
      // Envoyer les attributs directement comme objet JavaScript, sans les convertir en JSON ici
      attributes: Object.keys(attributs).length > 0 ? attributs : null
    };
    // Si on est en mode modification d'une carte existante
    if (carteEnEdition) {
      const id = carteEnEdition.getAttribute("data-id");
      modifierAppareil(id, donnees, "modification");
      return;
    }

    // Sinon, création d'un nouvel appareil
    creerAppareil(donnees);
  });


  const categorieSelect = document.getElementById("categorie");
  categorieSelect.addEventListener("change", actualiserAttributsDynamiques);
});

// Fonction pour actualiser les attributs selon la catégorie sélectionnée
function actualiserAttributsDynamiques() {
  const categorieSelectionnee = document.getElementById("categorie").value;
  const conteneurAttributs = document.getElementById("attributs-dynamiques");

  // Vider le conteneur d'attributs
  conteneurAttributs.innerHTML = '';

  // Si aucune catégorie n'est sélectionnée ou si elle n'a pas d'attributs définis
  if (!categorieSelectionnee || !attributsParCategorie[categorieSelectionnee]) {
    return;
  }

  // Ajouter un titre pour la section attributs
  const titreAttributs = document.createElement("h4");
  titreAttributs.textContent = "Attributs spécifiques";
  conteneurAttributs.appendChild(titreAttributs);

  // Créer les champs pour chaque attribut de la catégorie
  attributsParCategorie[categorieSelectionnee].forEach(attribut => {
    const divAttribut = document.createElement("div");
    divAttribut.classList.add("attribut-item");

    // Créer le label
    const label = document.createElement("label");
    label.setAttribute("for", `attr_${attribut.nom}`);
    label.textContent = attribut.label;
    divAttribut.appendChild(label);

    // Créer le champ selon le type
    let champInput;

    switch (attribut.type) {
      case 'select':
        champInput = document.createElement("select");
        champInput.id = `attr_${attribut.nom}`;
        champInput.name = `attr_${attribut.nom}`;

        // Ajouter les options
        attribut.options.forEach(option => {
          const optionElement = document.createElement("option");
          optionElement.value = option;
          optionElement.textContent = option.charAt(0).toUpperCase() + option.slice(1);
          champInput.appendChild(optionElement);
        });
        break;

      case 'checkbox':
        champInput = document.createElement("input");
        champInput.type = "checkbox";
        champInput.id = `attr_${attribut.nom}`;
        champInput.name = `attr_${attribut.nom}`;
        break;

      case 'range':
        champInput = document.createElement("input");
        champInput.type = "range";
        champInput.id = `attr_${attribut.nom}`;
        champInput.name = `attr_${attribut.nom}`;
        champInput.min = attribut.min || 0;
        champInput.max = attribut.max || 100;
        champInput.value = attribut.default || attribut.min || 0;

        // Ajouter un affichage de la valeur
        const valeurAffichee = document.createElement("span");
        valeurAffichee.id = `${attribut.nom}_valeur`;
        valeurAffichee.textContent = champInput.value;
        champInput.addEventListener("input", () => {
          valeurAffichee.textContent = champInput.value;
        });
        divAttribut.appendChild(champInput);
        divAttribut.appendChild(valeurAffichee);
        break;

      default: // text, number, etc.
        champInput = document.createElement("input");
        champInput.type = attribut.type;
        champInput.id = `attr_${attribut.nom}`;
        champInput.name = `attr_${attribut.nom}`;

        if (attribut.min !== undefined) champInput.min = attribut.min;
        if (attribut.max !== undefined) champInput.max = attribut.max;
        if (attribut.default !== undefined) champInput.value = attribut.default;
    }


    if (attribut.type !== 'range') {
      divAttribut.appendChild(champInput);
    }

    conteneurAttributs.appendChild(divAttribut);
  });
}


// Fonction pour charger tous les appareils depuis l'API
function chargerAppareils() {
  fetch('/connected-objects')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Vider la grille
        const deviceGrid = document.querySelector(".device-grid");
        deviceGrid.innerHTML = '';

        // Ajouter chaque appareil
        data.data.forEach(appareil => {
          ajouterCarteAppareil(appareil);
        });

        // Appliquer le filtre actuel
        filtrerCategorie();
      }
    })
    .catch(error => console.error('Erreur lors du chargement des appareils:', error));
}

// Fonction pour créer un appareil via l'API
function creerAppareil(donnees) {
  // Ajout d'un unique_id requis par votre API
  donnees.unique_id = 'DEV_' + Date.now();

  if (donnees.zone_id) {
    delete donnees.zone_id;
  }

  if (donnees.attributes) {
    donnees.attributes = JSON.stringify(donnees.attributes);
  }

  console.log("Attributs avant envoi:", donnees);

  fetch('/connected-objects', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(donnees)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        ajouterCarteAppareil(data.data);
        closeForm();
        document.querySelector(".device-form").reset();
        filtrerCategorie();
      } else {
        alert('Erreur: ' + JSON.stringify(data.errors));
      }
    })
    .catch(error => console.error('Erreur lors de la création:', error));
}

// Fonction pour modifier un appareil via l'API
function modifierAppareil(id, donnees, action_type = null) {

  if (action_type) {
    donnees.action_type = action_type;
  }

  if (donnees.attributes) {
    donnees.attributes = JSON.stringify(donnees.attributes);
  }

  fetch(`/connected-objects/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(donnees)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Charger tous les appareils pour s'assurer que les données sont à jour
        chargerAppareils();

        // Réinitialiser le formulaire
        carteEnEdition = null;
        closeForm();
        document.querySelector(".device-form").reset();
        document.querySelector('button[type="submit"]').textContent = "Enregistrer";
      } else {
        alert('Erreur: ' + JSON.stringify(data.errors));
      }
    })
    .catch(error => console.error('Erreur lors de la modification:', error));
}



// Fonction pour ajouter une carte d'appareil à l'interface
function ajouterCarteAppareil(appareil) {
  const deviceGrid = document.querySelector(".device-grid");
  const deviceCard = document.createElement("div");
  deviceCard.classList.add("device-card");
  deviceCard.setAttribute("data-categorie", appareil.type);
  deviceCard.setAttribute("data-id", appareil.id);


  let zoneName = "Non définie";
  if (appareil.zone) {
    zoneName = appareil.zone.name;
  }

  if (appareil.reported) {
    deviceCard.classList.add("reported");
  }

  const bodyElement = document.body;
  const isExpert = bodyElement.dataset.isExpert === 'true';
  const isAdvanced = bodyElement.dataset.isAdvanced === 'true';
  const isIntermediaire = bodyElement.dataset.isIntermediate === 'true';

  console.log("Rôles récupérés du DOM:", { isExpert, isIntermediaire });


  const showDeleteButton = isExpert;
  const showModifyButton = isIntermediaire || isAdvanced || isExpert;

  console.log("showModifyButton:", showModifyButton);

  console.log("isExpert:", isExpert);

  const suppressionBtnText = appareil.reported ? "Signalé ⚠️" : "Solliciter la suppression";
  const suppressionBtnDisabled = appareil.reported ? "disabled" : "";

  const dateCreation = appareil.created_at
    ? new Date(appareil.created_at).toLocaleDateString('fr-FR')
    : 'N/A';

  let attributsText = 'Aucun';
  if (appareil.attributes) {
    try {

      const attributsObj = typeof appareil.attributes === 'string'
        ? JSON.parse(appareil.attributes)
        : appareil.attributes;


      attributsText = Object.entries(attributsObj)
        .map(([key, value]) => `${key} : ${value}`)
        .join(', ');
    } catch (e) {
      console.error("Erreur lors du parsing des attributs:", e);
      attributsText = appareil.attributes;
    }
  }


  deviceCard.innerHTML = `
    <div class="device-content">
      <div class="device-infos">
        <div class="info-item"><strong>Nom:</strong> ${appareil.name}</div>
        <div class="info-item"><strong>Batterie:</strong> ${appareil.battery_level}%</div>
        <div class="info-item"><strong>Attributs:</strong> ${attributsText || 'Aucun'}</div>
        <div class="info-item"><strong>Statut:</strong> ${appareil.status === "actif" ? "En ligne" : "Hors ligne"}</div>
        <div class="info-item"><strong>Zone:</strong> ${appareil.zone_id}</div>
         <div class="info-item"><strong>Dernière interaction:</strong> ${appareil.last_interaction ? new Date(appareil.last_interaction).toLocaleDateString('fr-FR') : 'N/A'}</div>
        <div class="info-item"><strong>Création:</strong> ${new Date(appareil.created_at).toLocaleDateString('fr-FR')}</div>
        <div class="info-item"><strong>Coordonnées:</strong> ${appareil.lat},${appareil.lng}</div>
        <div class="info-item"><strong>Catégorie:</strong> ${appareil.type}</div>
      </div>
      <div class="device-actions">
        <button class="rapport-btn">Rapport</button>
        <button class="stats-btn">Statistiques</button>
        <button class="recharger-btn">Recharger 🔋</button>
        <div class="toggle-wrapper">
          <label class="toggle-switch">
            <input type="checkbox" class="toggle-checkbox" ${appareil.status === "actif" ? "checked" : ""} />
            <span class="slider"></span>
          </label>
          <span class="toggle-label">${appareil.status === "actif" ? "Activé" : "Désactivé"}</span>
        </div>
        <button class="maj-btn">Mettre à jour</button>
        ${showModifyButton ? `<button class="modifier-btn">Modifier</button>` : ''}
        ${showDeleteButton ? `<button class="supprimer-btn" ${suppressionBtnDisabled}>${suppressionBtnText}</button>` : ''}
      </div>
    </div>
  `;

  // Gestion du bouton ON/OFF (toggle)
  const toggle = deviceCard.querySelector(".toggle-checkbox");
  const labelToggle = deviceCard.querySelector(".toggle-label");
  toggle.addEventListener("change", () => {
    const estActif = toggle.checked;
    labelToggle.textContent = estActif ? "Activé" : "Désactivé";

    // Mise à jour du statut dans la base de données
    const id = deviceCard.getAttribute("data-id");
    modifierAppareil(id, { status: estActif ? "actif" : "inactif" });
  });

  // Gestion du bouton Supprimer
  if (showDeleteButton) {
    const btnSupprimer = deviceCard.querySelector(".supprimer-btn");
    btnSupprimer.addEventListener("click", () => {
      const id = deviceCard.getAttribute("data-id");

      if (confirm("Êtes-vous sûr de vouloir signaler cet appareil pour suppression ?")) {
        // Appel à l'API pour signaler l'appareil
        fetch(`/connected-objects/${id}/report`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Afficher le message de confirmation
              const message = document.getElementById("confirmation-message");
              message.style.display = "block";
              setTimeout(() => {
                message.style.display = "none";
              }, 4000);

              // Ajouter une indication visuelle que l'appareil a été signalé
              deviceCard.classList.add("reported");
              btnSupprimer.textContent = "Signalé ⚠️";
              btnSupprimer.disabled = true;
            } else {
              alert('Erreur lors du signalement: ' + data.message);
            }
          })
          .catch(error => console.error('Erreur lors du signalement de l\'appareil:', error));
      }
    });
  }

  const btnRecharger = deviceCard.querySelector(".recharger-btn");
  btnRecharger.addEventListener("click", () => {
    const id = deviceCard.getAttribute("data-id");

    // Mise à jour de l'affichage immédiatement
    const batterieElement = deviceCard.querySelector(".info-item:nth-child(2)");
    batterieElement.innerHTML = "<strong>Batterie:</strong> 100%";
    batterieElement.style.color = "";

    // Animation de recharge (effet visuel)
    btnRecharger.textContent = "Recharge en cours...";
    btnRecharger.disabled = true;

    // Mise à jour dans la base de données
    modifierAppareil(id, {
      battery_level: 100,
      status: "actif"
    });

    // Réinitialisation du bouton après un délai
    setTimeout(() => {
      btnRecharger.textContent = "Recharger 🔋";
      btnRecharger.disabled = false;
    }, 2000);
  });

  // Gestion du bouton Modifier
  if (showModifyButton) {
    const btnModifier = deviceCard.querySelector(".modifier-btn");
    btnModifier.addEventListener("click", () => {
      carteEnEdition = deviceCard;
      const infos = deviceCard.querySelectorAll(".info-item");

      // Pré-remplir le formulaire avec les données existantes
      document.getElementById("nom").value = infos[0].textContent
        .replace("Nom:", "")
        .trim();
      document.getElementById("batterie").value = infos[1].textContent
        .replace("Batterie:", "")
        .replace("%", "")
        .trim();
      document.getElementById("statut").value = infos[3].textContent
        .replace("Statut:", "")
        .trim()
        .toLowerCase() === "en ligne" ? "en_ligne" : "hors_ligne";

      // Pré-remplir les coordonnées
      const coordonneesText = infos[7].textContent
        .replace("Coordonnées:", "")
        .trim();
      document.getElementById("coordonnees").value = coordonneesText;

      // Pré-remplir la catégorie
      const categorieText = infos[8].textContent
        .replace("Catégorie:", "")
        .trim();
      document.getElementById("categorie").value = categorieText;

      document.getElementById("categorie").value = categorieText;
      actualiserAttributsDynamiques();

      // Ensuite récupérer et définir les valeurs des attributs
      try {
        const attributsText = infos[2].textContent
          .replace("Attributs:", "")
          .trim();

        if (attributsText !== 'Aucun') {
          const attributsObj = typeof appareil.attributes === 'string'
            ? JSON.parse(appareil.attributes)
            : appareil.attributes;


          Object.entries(attributsObj).forEach(([key, value]) => {
            const champAttribut = document.getElementById(`attr_${key}`);
            if (champAttribut) {
              if (champAttribut.type === 'checkbox') {
                champAttribut.checked = value === true || value === "true";
              } else {
                champAttribut.value = value;
              }


              if (champAttribut.type === 'range') {
                const valeurAffichee = document.getElementById(`${key}_valeur`);
                if (valeurAffichee) {
                  valeurAffichee.textContent = champAttribut.value;
                }
              }
            }
          });
        }
      } catch (e) {
        console.error("Erreur lors du remplissage des attributs:", e);
      }

      toggleDeviceForm();
      document.querySelector('button[type="submit"]').textContent = "Mettre à jour";
    });
  }

  deviceGrid.appendChild(deviceCard);
}

// Ouvre le formulaire
function toggleDeviceForm() {
  document.getElementById("deviceForm").style.display = "flex";
}

// Ferme le formulaire
function closeForm() {
  document.getElementById("deviceForm").style.display = "none";
}

// Filtre les cartes selon la catégorie sélectionnée
function filtrerCategorie() {
  const filtre = document.getElementById("filtreCategorie").value;
  const cartes = document.querySelectorAll(".device-card");

  cartes.forEach((carte) => {
    const categorie = carte.getAttribute("data-categorie");
    carte.style.display =
      filtre === "tous" || categorie === filtre ? "flex" : "none";
  });
}