document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('user-search');
    const searchButton = document.getElementById('search-button');
    const searchResults = document.getElementById('search-results');
    let typingTimer;
    const doneTypingInterval = 500; // Délai après la saisie en ms

    // Fonction pour effectuer la recherche
    function performSearch() {
        const query = searchInput.value.trim();
        // on demarre la recherche uniquement si l'utilisateur a saisi au moins 2 caractères sinon ca peut afficher trop de resultats
        // et ca peut faire laguer le site si y a trop trop d'utilisateurs.
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }


        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/search-users?query=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';

                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="no-results">Aucun utilisateur trouvé</div>';
                } else {
                    data.forEach(user => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'user-result';

                        // Construire le chemin complet pour l'image de profil
                        const profilePicUrl = user.profile_picture
                            ? `/storage/${user.profile_picture}`
                            : '/assets/default-avatar.png'; // Image par défaut si nécessaire

                        resultItem.innerHTML = `
                        <img src="${profilePicUrl}" alt="${user.login}" onerror="this.src='/assets/default-avatar.png'">
                        <div class="user-info">${user.login}</div>
                    `;

                        // Ajouter l'événement de clic pour rediriger vers le profil
                        resultItem.addEventListener('click', function () {
                            window.location.href = `/profil/${user.login}`;
                        });

                        searchResults.appendChild(resultItem);
                    });
                }

                searchResults.style.display = 'block';
            })
            .catch(error => {
                console.error('Erreur de recherche:', error);
                searchResults.innerHTML = '<div class="no-results">Une erreur est survenue</div>';
                searchResults.style.display = 'block';
            });
    }

    // Événement pour le bouton de recherche
    searchButton.addEventListener('click', performSearch);

    // Recherche avec délai pendant la saisie
    searchInput.addEventListener('keyup', function (e) {
        clearTimeout(typingTimer);

        // Si l'utilisateur appuie sur Entrée, rechercher immédiatement
        if (e.key === 'Enter') {
            performSearch();
            return;
        }

        // Sinon, attendre que l'utilisateur arrête de taper
        typingTimer = setTimeout(performSearch, doneTypingInterval);
    });

    // Annuler le timer si l'utilisateur continue à taper
    searchInput.addEventListener('keydown', function () {
        clearTimeout(typingTimer);
    });

    // Fermer les résultats si on clique ailleurs
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchButton.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});