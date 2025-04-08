document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.querySelector('.edit-btn');
    const inputs = document.querySelectorAll('input[readonly]');
    const selects = document.querySelectorAll('select[disabled]');
    const profileForm = document.getElementById('profile-form');
    const feedbackElements = {};
    let editMode = false;

    // Bouton pour basculer en mode édition
    editButton.addEventListener('click', function () {
        if (!editMode) {
            // Passer en mode édition
            editMode = true;
            inputs.forEach(input => {
                if (input.type !== "email") {
                    input.removeAttribute('readonly');
                }
            });
            selects.forEach(select => {
                select.removeAttribute('disabled');
            });
            editButton.textContent = "✓ Enregistrer";
            editButton.style.backgroundColor = "#4CAF50";
        } else {
            // Soumettre le formulaire
            profileForm.dispatchEvent(new Event('submit'));
        }
    });

    // Gestion de la soumission du formulaire
    profileForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Nettoyer les messages d'erreur précédents
        Object.values(feedbackElements).forEach(el => {
            if (el) el.textContent = '';
        });

        // Créer FormData pour l'envoi
        const formData = new FormData(this);

        // Envoyer les données
        fetch('/update-profile', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    // Afficher les erreurs
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            // Créer ou récupérer l'élément de feedback
                            if (!feedbackElements[field]) {
                                feedbackElements[field] = document.createElement('div');
                                feedbackElements[field].className = 'error-feedback';
                                feedbackElements[field].style.color = 'red';
                                feedbackElements[field].style.fontSize = '12px';
                                feedbackElements[field].style.marginTop = '5px';
                                input.parentNode.appendChild(feedbackElements[field]);
                            }
                            feedbackElements[field].textContent = messages[0];
                        }
                    }
                } else {
                    // Succès : retourner en mode lecture seule
                    editMode = false;
                    inputs.forEach(input => {
                        input.setAttribute('readonly', true);
                    });
                    selects.forEach(select => {
                        select.setAttribute('disabled', true);
                    });
                    editButton.textContent = "✏️ Modifier";
                    editButton.style.backgroundColor = "#ffca28";

                    // Afficher un message de succès
                    const successMsg = document.createElement('div');
                    successMsg.textContent = data.message;
                    successMsg.className = 'success-message';
                    successMsg.style.backgroundColor = '#dff0d8';
                    successMsg.style.color = '#3c763d';
                    successMsg.style.padding = '10px';
                    successMsg.style.borderRadius = '5px';
                    successMsg.style.marginTop = '15px';
                    successMsg.style.textAlign = 'center';

                    profileForm.appendChild(successMsg);

                    // Faire disparaître le message après quelques secondes
                    setTimeout(() => {
                        successMsg.style.opacity = '0';
                        successMsg.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => successMsg.remove(), 500);
                    }, 3000);

                    // Si la page doit être rechargée pour refléter les changements
                    setTimeout(() => {
                        window.location.reload();
                    }, 3500);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Afficher une erreur générale
                const errorMsg = document.createElement('div');
                errorMsg.textContent = 'Une erreur est survenue lors de la mise à jour du profil.';
                errorMsg.className = 'error-message';
                errorMsg.style.backgroundColor = '#f8d7da';
                errorMsg.style.color = '#721c24';
                errorMsg.style.padding = '10px';
                errorMsg.style.borderRadius = '5px';
                errorMsg.style.marginTop = '15px';
                errorMsg.style.textAlign = 'center';

                profileForm.appendChild(errorMsg);

                setTimeout(() => {
                    errorMsg.style.opacity = '0';
                    errorMsg.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => errorMsg.remove(), 500);
                }, 3000);
            });
    });

    // Gestion de l'upload de photo de profil
    const profilePicInput = document.getElementById('profile-picture-input');
    const profilePicImg = document.querySelector('.avatar');

    if (profilePicInput && profilePicImg) {
        profilePicInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file && ['image/png', 'image/jpeg', 'image/gif'].includes(file.type)) {
                const reader = new FileReader();
                reader.onloadend = function () {
                    profilePicImg.src = reader.result;
                };
                reader.readAsDataURL(file);
            } else if (file) {
                alert('Format d\'image non supporté. Utilisez PNG, JPEG ou GIF');
            }
        });

        // Option: Cliquer sur l'avatar ouvre le sélecteur de fichier
        profilePicImg.addEventListener('click', function () {
            if (editMode) {
                profilePicInput.click();
            }
        });
    }
});