document.addEventListener("DOMContentLoaded", () => {
  // Récupérer le token CSRF
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Charger les objets signalés
  loadReportedObjects();

  // Fonction pour charger les objets signalés
  function loadReportedObjects() {
    console.log('Chargement des objets signalés...');
    fetch('/reported')
      .then(response => {
        console.log('Réponse reçue:', response);
        return response.json();
      })
      .then(data => {
        console.log('Données reçues:', data);
        if (data.success) {
          displayReportedObjects(data.data);
        } else {
          console.error('Erreur lors du chargement des objets signalés:', data);
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
      });
  }

  // Afficher les objets signalés dans la grille
  function displayReportedObjects(objects) {
    const grid = document.getElementById("suppressionGrid");
    grid.innerHTML = ''; // Vider la grille

    if (objects.length === 0) {
      grid.innerHTML = '<div class="no-devices">Aucun objet signalé</div>';
      return;
    }

    objects.forEach(object => {
      const card = document.createElement("div");
      card.className = "device-card";
      card.setAttribute("data-id", object.id);

      const creationDate = new Date(object.created_at).toLocaleDateString();

      card.innerHTML = `
        <div class="device-infos">
          <div class="info-item"><strong>Nom:</strong> ${object.name}</div>
          <div class="info-item"><strong>Batterie:</strong> ${object.battery_level}%</div>
          <div class="info-item"><strong>Statut:</strong> ${object.status}</div>
          <div class="info-item"><strong>Zone:</strong> ${object.zone_id || 'Non définie'}</div>
          <div class="info-item"><strong>Identifiant:</strong> ${object.unique_id}</div>
          <div class="info-item"><strong>Création:</strong> ${creationDate}</div>
          <div class="info-item"><strong>Coordonnées:</strong> Lat: ${object.lat}, Lng: ${object.lng}</div>
          <div class="info-item"><strong>Type:</strong> ${object.type}</div>
        </div>
        <div class="validation-zone">
          <button class="valider" title="Confirmer la suppression">✅</button>
          <button class="refuser" title="Annuler le signalement">✖</button>
        </div>
      `;

      // Action pour le bouton "valider" (supprimer l'objet)
      card.querySelector(".valider").addEventListener("click", () => {
        deleteObject(object.id, card);
      });

      // Action pour le bouton "refuser" (annuler le signalement)
      card.querySelector(".refuser").addEventListener("click", () => {
        cancelReport(object.id, card);
      });

      grid.appendChild(card);
    });
  }

  // Fonction pour supprimer un objet
  function deleteObject(id, card) {
    fetch(`/connected-objects/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          afficherMessage("Suppression validée avec succès.");
          card.remove();
        } else {
          afficherMessage("Erreur lors de la suppression.", "error");
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        afficherMessage("Erreur lors de la suppression.", "error");
      });
  }

  // Fonction pour annuler le signalement
  function cancelReport(id, card) {
    fetch(`/connected-objects/${id}/cancel-report`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          afficherMessage("Signalement annulé.");
          card.remove();
        } else {
          afficherMessage("Erreur lors de l'annulation du signalement.", "error");
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        afficherMessage("Erreur lors de l'annulation du signalement.", "error");
      });
  }

  // Amélioration de la fonction d'affichage des messages
  function afficherMessage(texte, type = "success") {
    const message = document.getElementById("confirmation-message");

    if (type === "success") {
      message.textContent = `✅ ${texte}`;
      message.className = "message-confirmation success";
    } else {
      message.textContent = `❌ ${texte}`;
      message.className = "message-confirmation error";
    }

    message.style.display = "block";
    setTimeout(() => {
      message.style.display = "none";
    }, 4000);
  }
});