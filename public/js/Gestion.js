// Variable globale pour suivre la carte en cours d'édition
let carteEnEdition = null;

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
    const zone = document.getElementById("zone").value;
    const utilisateur = document.getElementById("utilisateur").value;
    const creation = document.getElementById("creation").value;
    const coordonnees = document.getElementById("coordonnees").value;
    const categorie = document.getElementById("categorie").value;

    // Préparation des données pour l'API
    const donnees = {
      name: nom,
      battery_level: batterie,
      status: statut === "en_ligne" ? "actif" : "inactif",
      zone_id: zone, // Assurez-vous que c'est un ID valide
      last_user: utilisateur,
      created_at: creation,
      lat: coordonnees.split(',')[0] || null,
      lng: coordonnees.split(',')[1] || null,
      type: categorie
    };

    // Si on est en mode modification d'une carte existante
    if (carteEnEdition) {
      const id = carteEnEdition.getAttribute("data-id");
      modifierAppareil(id, donnees);
      return;
    }

    // Sinon, création d'un nouvel appareil
    creerAppareil(donnees);
  });
});

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
  donnees.description = donnees.name + ' description';

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
function modifierAppareil(id, donnees) {
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
        // Mettre à jour la carte dans l'interface
        carteEnEdition.setAttribute("data-categorie", donnees.type);
        carteEnEdition.querySelector(".device-infos").innerHTML = `
        <div class="info-item"><strong>Nom:</strong> ${donnees.name}</div>
        <div class="info-item"><strong>Batterie:</strong> ${donnees.battery_level}%</div>
        <div class="info-item"><strong>Statut:</strong> ${donnees.status === "actif" ? "En ligne" : "Hors ligne"}</div>
        <div class="info-item"><strong>Zone:</strong> ${donnees.zone_id}</div>
        <div class="info-item"><strong>Utilisateur:</strong> ${donnees.last_user}</div>
        <div class="info-item"><strong>Création:</strong> ${donnees.created_at}</div>
        <div class="info-item"><strong>Coordonnées:</strong> ${donnees.lat},${donnees.lng}</div>
        <div class="info-item"><strong>Catégorie:</strong> ${donnees.type}</div>
      `;

        carteEnEdition = null;
        closeForm();
        document.querySelector(".device-form").reset();
        document.querySelector('button[type="submit"]').textContent = "Enregistrer";
        filtrerCategorie();
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

  deviceCard.innerHTML = `
    <div class="device-content">
      <div class="device-infos">
        <div class="info-item"><strong>Nom:</strong> ${appareil.name}</div>
        <div class="info-item"><strong>Batterie:</strong> ${appareil.battery_level}%</div>
        <div class="info-item"><strong>Statut:</strong> ${appareil.status === "actif" ? "En ligne" : "Hors ligne"}</div>
        <div class="info-item"><strong>Zone:</strong> ${appareil.zone_id}</div>
        <div class="info-item"><strong>Utilisateur:</strong> ${appareil.last_user || 'N/A'}</div>
        <div class="info-item"><strong>Création:</strong> ${appareil.created_at}</div>
        <div class="info-item"><strong>Coordonnées:</strong> ${appareil.lat},${appareil.lng}</div>
        <div class="info-item"><strong>Catégorie:</strong> ${appareil.type}</div>
      </div>
      <div class="device-actions">
        <button class="rapport-btn">Rapport</button>
        <button class="stats-btn">Statistiques</button>
        <div class="toggle-wrapper">
          <label class="toggle-switch">
            <input type="checkbox" class="toggle-checkbox" ${appareil.status === "actif" ? "checked" : ""} />
            <span class="slider"></span>
          </label>
          <span class="toggle-label">${appareil.status === "actif" ? "Activé" : "Désactivé"}</span>
        </div>
        <button class="maj-btn">Mettre à jour</button>
        <button class="modifier-btn">Modifier</button>
        <button class="supprimer-btn">Solliciter la suppression</button>
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
  const btnSupprimer = deviceCard.querySelector(".supprimer-btn");
  btnSupprimer.addEventListener("click", () => {
    const id = deviceCard.getAttribute("data-id");

    if (confirm("Êtes-vous sûr de vouloir demander la suppression de cet appareil ?")) {
      const message = document.getElementById("confirmation-message");
      message.style.display = "block";
      setTimeout(() => {
        message.style.display = "none";
      }, 4000);

      // Option: Supprimer immédiatement ou juste afficher le message
      // supprimerAppareil(id);
    }
  });

  // Gestion du bouton Modifier
  const btnModifier = deviceCard.querySelector(".modifier-btn");
  btnModifier.addEventListener("click", () => {
    carteEnEdition = deviceCard;
    const infos = deviceCard.querySelectorAll(".info-item");

    // Pré-remplissage du formulaire avec les données de la carte
    document.getElementById("nom").value = infos[0].textContent
      .replace("Nom:", "")
      .trim();
    document.getElementById("batterie").value = infos[1].textContent
      .replace("Batterie:", "")
      .replace("%", "")
      .trim();
    document.getElementById("statut").value = infos[2].textContent
      .replace("Statut:", "")
      .trim()
      .toLowerCase() === "en ligne" ? "en_ligne" : "hors_ligne";
    document.getElementById("zone").value = infos[3].textContent
      .replace("Zone:", "")
      .trim();
    document.getElementById("utilisateur").value = infos[4].textContent
      .replace("Utilisateur:", "")
      .trim();
    document.getElementById("creation").value = infos[5].textContent
      .replace("Création:", "")
      .trim();
    document.getElementById("coordonnees").value = infos[6].textContent
      .replace("Coordonnées:", "")
      .trim();
    document.getElementById("categorie").value = infos[7].textContent
      .replace("Catégorie:", "")
      .trim();

    toggleDeviceForm();
    document.querySelector('button[type="submit"]').textContent = "Mettre à jour";
  });

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