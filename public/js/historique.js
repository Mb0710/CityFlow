document.addEventListener("DOMContentLoaded", () => {
  fetchUserActions();
});

function fetchUserActions() {
  fetch('/admin/user-actions')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayUserActions(data.data);
      } else {
        console.error('Erreur lors du chargement des actions utilisateurs');
      }
    })
    .catch(error => {
      console.error('Erreur:', error);
    });
}

function displayUserActions(actions) {
  const container = document.getElementById('actionsContainer');

  if (actions.length === 0) {
    container.innerHTML = '<p class="no-data">Aucune action utilisateur trouvée.</p>';
    return;
  }

  // Création d'un tableau pour que ce soit plus lisible
  let html = `
      <table class="actions-table">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Utilisateur</th>
                  <th>Type d'action</th>
                  <th>Objet</th>
                  <th>Description</th>
                  <th>Points</th>
                  <th>Date</th>
              </tr>
          </thead>
          <tbody>
  `;

  // on formate de maniere a ce que soit a chaque fois une ligne de la table SQL = 1 ligne du tableau
  actions.forEach(action => {
    const typeClass = `type-${action.action_type.toLowerCase()}`;
    const pointsClass = action.points >= 0 ? 'points-positive' : 'points-negative';
    const formattedDate = new Date(action.created_at).toLocaleString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });

    // On a fait en sorte de relier l'utilisateur et l'objet à l'action dans le backend,
    //  donc on peut les utiliser directement ici
    const userName = action.user ? action.user.login || action.user.name || `Utilisateur #${action.user_id}` : `Utilisateur #${action.user_id}`;
    const objectName = action.connected_object ? action.connected_object.name || `Objet #${action.object_id}` : (action.object_id ? `Objet #${action.object_id}` : '-');
    const actionType = action.action_type.charAt(0).toUpperCase() + action.action_type.slice(1);

    html += `
          <tr>
              <td>${action.id}</td>
              <td><span class="user-badge">${userName}</span></td>
              <td><span class="action-type ${typeClass}">${actionType}</span></td>
              <td>${action.object_id ? `<span class="object-badge">${objectName}</span>` : '-'}</td>
              <td>${action.description || '-'}</td>
              <td class="${pointsClass}">${action.points > 0 ? '+' : ''}${action.points}</td>
              <td class="action-date">${formattedDate}</td>
          </tr>
      `;
  });

  html += `
          </tbody>
      </table>
  `;

  container.innerHTML = html;
}