document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("inscriptionGrid");

  // Récupération des utilisateurs en attente depuis l'API
  fetch('/admin/users/pending')
    .then(response => response.json())
    .then(users => {
      if (users.length === 0) {
        grid.innerHTML = '<div class="no-data-message">Aucune inscription en attente de validation.</div>';
        return;
      }

      // Création dynamique d'une carte pour chaque utilisateur
      users.forEach(user => {
        const card = document.createElement("div");
        card.className = "device-card";
        card.setAttribute("data-id", user.id);

        // Calcul de l'âge à partir de la date de naissance
        const birthDate = new Date(user.birth_date);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear() -
          (today.getMonth() < birthDate.getMonth() ||
            (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate()));

        // Formatage de la date de naissance
        const formattedDate = new Date(user.birth_date).toLocaleDateString('fr-FR');

        // Affichage des infos utilisateur
        card.innerHTML = `
          <div class="device-infos">
            <div class="info-item"><strong>Nom:</strong> ${user.name}</div>
            <div class="info-item"><strong>Prénom:</strong> ${user.firstname}</div>
            <div class="info-item"><strong>Pseudo:</strong> ${user.login}</div>
            <div class="info-item"><strong>Âge:</strong> ${age}</div>
            <div class="info-item"><strong>Genre:</strong> ${user.gender === 'male' ? 'Homme' : (user.gender === 'female' ? 'Femme' : 'Autre')}</div>
            <div class="info-item"><strong>Date de naissance:</strong> ${formattedDate}</div>
            <div class="info-item"><strong>Type de membre:</strong> ${user.member_type}</div>
            <div class="info-item"><strong>Email:</strong> ${user.email}</div>
          </div>
          <div class="validation-zone">
            <button class="valider" data-id="${user.id}">✅</button>
            <button class="refuser" data-id="${user.id}">✖</button>
          </div>
        `;

        // Ajout des gestionnaires d'événements
        grid.appendChild(card);
      });

      // Ajouter les écouteurs d'événements après avoir créé toutes les cartes
      document.querySelectorAll('.valider').forEach(btn => {
        btn.addEventListener('click', handleApprove);
      });

      document.querySelectorAll('.refuser').forEach(btn => {
        btn.addEventListener('click', handleReject);
      });
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des utilisateurs:', error);
      grid.innerHTML = '<div class="no-data-message">Erreur lors du chargement des données.</div>';
    });
});

// Fonction pour approuver un utilisateur
function handleApprove(event) {
  const userId = event.target.getAttribute('data-id');
  const card = document.querySelector(`.device-card[data-id="${userId}"]`);

  // Envoyer la requête pour approuver l'utilisateur
  fetch(`/admin/users/${userId}/approve`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
    .then(response => response.json())
    .then(data => {
      afficherMessage("Inscription validée.");
      card.remove();
    })
    .catch(error => {
      console.error('Erreur lors de la validation:', error);
      afficherMessage("Erreur lors de la validation.", true);
    });
}

// Fonction pour rejeter un utilisateur
function handleReject(event) {
  const userId = event.target.getAttribute('data-id');
  const card = document.querySelector(`.device-card[data-id="${userId}"]`);

  // Envoyer la requête pour rejeter l'utilisateur
  fetch(`/admin/users/${userId}/reject`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
    .then(response => response.json())
    .then(data => {
      afficherMessage("Inscription refusée.");
      card.remove();
    })
    .catch(error => {
      console.error('Erreur lors du rejet:', error);
      afficherMessage("Erreur lors du rejet.", true);
    });
}

// Fonction utilitaire pour afficher un message temporaire
function afficherMessage(texte, isError = false) {
  const message = document.getElementById("confirmation-message");
  message.textContent = `${isError ? '❌' : '✅'} ${texte}`;
  message.className = `message-confirmation ${isError ? 'error' : ''}`;
  message.style.display = "block";
  setTimeout(() => {
    message.style.display = "none";
  }, 4000);
}