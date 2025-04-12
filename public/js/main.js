
document.addEventListener('DOMContentLoaded', function () {


    attachCreateAccountEvent();
    attachFeatureTourEvents();
    const loginForm = document.querySelector('.box form');



    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();


            const oldErrorContainer = document.querySelector('.error-container');
            if (oldErrorContainer) {
                oldErrorContainer.remove();
            }

            const formData = new FormData(this);

            fetch('/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.errors) {

                        const errorContainer = document.createElement('div');
                        errorContainer.className = 'error-container';


                        const errorList = document.createElement('ul');


                        for (const field in data.errors) {
                            const errorItem = document.createElement('li');
                            errorItem.textContent = data.errors[field];
                            errorList.appendChild(errorItem);
                        }

                        errorContainer.appendChild(errorList);


                        const loginTitle = document.querySelector('.login-title');
                        loginTitle.parentNode.insertBefore(errorContainer, loginTitle.nextSibling);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);

                    const errorContainer = document.createElement('div');
                    errorContainer.className = 'error-container';
                    errorContainer.innerHTML = '<ul><li>Une erreur s\'est produite. Veuillez réessayer.</li></ul>';

                    const loginTitle = document.querySelector('.login-title');
                    loginTitle.parentNode.insertBefore(errorContainer, loginTitle.nextSibling);
                });
        });
    }

    // Gestion du formulaire d'inscription


    function attachCreateAccountEvent() {
        const createAccountLink = document.querySelector('.newAcc a');
        const resetPasswordLink = document.querySelector('.forgot-link');
        if (createAccountLink) {

            createAccountLink.removeEventListener('click', showSignupForm);

            createAccountLink.addEventListener('click', showSignupForm);
        }
        if (resetPasswordLink) {
            resetPasswordLink.removeEventListener('click', showResetForm);

            resetPasswordLink.addEventListener('click', showResetForm);
        }
    }

    function attachFeatureTourEvents() {
        const features = document.querySelectorAll('.feature');

        features.forEach((feature, index) => {
            feature.addEventListener('click', () => {
                switch (index) {
                    case 0:
                        showUrbanManagementTour();
                        break;
                    case 1:
                        showRealTimeAnalysisTour();
                        break;
                    case 2:
                        showSustainableDevelopmentTour();
                        break;
                }
            });
        });
    }

    function showUrbanManagementTour() {
        const tourOverlay = createTourOverlay(
            "Gestion Urbaine",
            "Explorez notre système intelligent de gestion urbaine. Visualisez et gérez les infrastructures, les services publics et les ressources de votre ville en temps réel.",
            [
                "Cartographie détaillée des infrastructures",
                "Suivi des projets urbains",
                "Gestion des bâtiments et des espaces publics",
                "Planification urbaine stratégique"
            ]
        );
        document.body.appendChild(tourOverlay);
    }

    function showRealTimeAnalysisTour() {
        const tourOverlay = createTourOverlay(
            "Analyse en Temps Réel",
            "Découvrez notre plateforme d'analyse en temps réel qui transforme les données urbaines en insights stratégiques.",
            [
                "Tableaux de bord dynamiques",
                "Indicateurs de performance clés",
                "Rapports détaillés et personnalisables",
                "Alertes et notifications instantanées"
            ]
        );
        document.body.appendChild(tourOverlay);
    }

    function showSustainableDevelopmentTour() {
        const tourOverlay = createTourOverlay(
            "Développement Durable",
            "Engagez-vous dans une approche écologique avec nos outils de développement durable. Surveillez et améliorez l'empreinte environnementale de votre ville.",
            [
                "Mesure des émissions de carbone",
                "Gestion des ressources durables",
                "Initiatives écologiques",
                "Rapport d'impact environnemental"
            ]
        );
        document.body.appendChild(tourOverlay);
    }

    function createTourOverlay(title, description, features) {
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
        overlay.style.zIndex = '1000';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
        overlay.style.color = 'white';
        overlay.style.padding = '20px';
        overlay.style.boxSizing = 'border-box';
        overlay.style.textAlign = 'center';

        const content = document.createElement('div');
        content.style.maxWidth = '600px';
        content.style.background = 'rgba(255,255,255,0.1)';
        content.style.padding = '40px';
        content.style.borderRadius = '15px';
        content.style.backdropFilter = 'blur(10px)';

        content.innerHTML = `
            <h2 style="color: #1976d2; margin-bottom: 20px;">${title}</h2>
            <p style="margin-bottom: 20px; line-height: 1.6;">${description}</p>
            <h3 style="color: #1976d2; margin-bottom: 15px;">Fonctionnalités Clés:</h3>
            <ul style="list-style-type: none; padding: 0;">
                ${features.map(feature => `
                    <li style="margin-bottom: 10px; display: flex; align-items: center; justify-content: center;">
                        <span style="margin-right: 10px; color: #1976d2;">•</span>${feature}
                    </li>
                `).join('')}
            </ul>
            <button id="closeTourOverlay" style="
                margin-top: 20px; 
                background-color: #1976d2; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 5px; 
                cursor: pointer;
                transition: background-color 0.3s;
            ">Fermer</button>
        `;

        const closeButton = content.querySelector('#closeTourOverlay');
        closeButton.addEventListener('click', () => {
            document.body.removeChild(overlay);
        });

        overlay.appendChild(content);
        return overlay;
    }


    function setupRealtimeValidation() {
        // Récupération des éléments du formulaire
        const loginInput = document.querySelector('input[name="login"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[type="password"][placeholder="Confirmer le mot de passe"]');
        const birthDateInput = document.querySelector('input[name="birth_date"]');
        const emailInput = document.querySelector('input[name="email"]');
        const imageUpload = document.getElementById('imageUpload');
        const submitButton = document.querySelector('.signup-box .button');

        // États de validation
        let isLoginValid = false;
        let isPasswordValid = false;
        let isConfirmPasswordValid = false;
        let isBirthDateValid = false;
        let isEmailValid = false;
        let isProfilePictureValid = false;

        // Création des éléments de feedback
        const loginFeedback = document.createElement('div');
        loginFeedback.className = 'validation-feedback';
        loginInput.parentNode.appendChild(loginFeedback);

        const passwordFeedback = document.createElement('div');
        passwordFeedback.className = 'validation-feedback';
        passwordInput.parentNode.appendChild(passwordFeedback);

        const confirmPasswordFeedback = document.createElement('div');
        confirmPasswordFeedback.className = 'validation-feedback';
        confirmPasswordInput.parentNode.appendChild(confirmPasswordFeedback);

        const birthDateFeedback = document.createElement('div');
        birthDateFeedback.className = 'validation-feedback';
        birthDateInput.parentNode.appendChild(birthDateFeedback);

        const emailFeedback = document.createElement('div');
        emailFeedback.className = 'validation-feedback';
        emailInput.parentNode.appendChild(emailFeedback);

        const profilePictureFeedback = document.createElement('div');
        profilePictureFeedback.className = 'validation-feedback';
        profilePictureFeedback.style.textAlign = 'center';
        imageUpload.parentNode.parentNode.insertBefore(profilePictureFeedback, imageUpload.parentNode.nextSibling);

        let loginTimer;
        let emailTimer;

        // Fonction pour mettre à jour l'état du bouton en fonction des validations
        function updateButtonState() {
            submitButton.disabled = !(isLoginValid && isPasswordValid && isConfirmPasswordValid &&
                isBirthDateValid && isEmailValid && isProfilePictureValid);
            submitButton.style.opacity = submitButton.disabled ? '0.5' : '1';
            submitButton.style.cursor = submitButton.disabled ? 'not-allowed' : 'pointer';
        }

        // Initialisation de l'état du bouton
        submitButton.disabled = true;
        submitButton.style.opacity = '0.5';
        submitButton.style.cursor = 'not-allowed';

        // Validation du login
        loginInput.addEventListener('input', function () {
            clearTimeout(loginTimer);
            const login = this.value.trim();

            if (login.length < 3) {
                loginFeedback.textContent = 'Le pseudo doit contenir au moins 3 caractères';
                loginFeedback.style.color = 'orange';
                isLoginValid = false;
                updateButtonState();
                return;
            }

            loginFeedback.textContent = 'Vérification...';
            loginFeedback.style.color = 'gray';

            loginTimer = setTimeout(() => {
                fetch(`/check-login?login=${encodeURIComponent(login)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            loginFeedback.textContent = 'Pseudo disponible ✓';
                            loginFeedback.style.color = 'green';
                            isLoginValid = true;
                        } else {
                            loginFeedback.textContent = 'Ce pseudo est déjà pris';
                            loginFeedback.style.color = 'red';
                            isLoginValid = false;
                        }
                        updateButtonState();
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        loginFeedback.textContent = '';
                        isLoginValid = false;
                        updateButtonState();
                    });
            }, 500);
        });

        emailInput.addEventListener('input', function () {
            clearTimeout(emailTimer);
            const email = this.value.trim();

            // Validation de format d'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailFeedback.textContent = 'Format d\'email invalide';
                emailFeedback.style.color = 'red';
                isEmailValid = false;
                updateButtonState();
                return;
            }

            emailFeedback.textContent = 'Vérification...';
            emailFeedback.style.color = 'gray';

            emailTimer = setTimeout(() => {
                fetch(`/check-email?email=${encodeURIComponent(email)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            emailFeedback.textContent = 'Email disponible ✓';
                            emailFeedback.style.color = 'green';
                            isEmailValid = true;
                        } else {
                            emailFeedback.textContent = 'Cet email est déjà utilisé';
                            emailFeedback.style.color = 'red';
                            isEmailValid = false;
                        }
                        updateButtonState();
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        emailFeedback.textContent = 'Format d\'email valide';
                        emailFeedback.style.color = 'green';
                        // En cas d'erreur avec le serveur, on considère valide si le format est correct
                        isEmailValid = true;
                        updateButtonState();
                    });
            }, 500);
        });

        // Validation du mot de passe avec critères renforcés
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            const hasMinLength = password.length >= 8;

            let feedback = '';
            let isValid = true;

            if (!hasMinLength) {
                feedback += '• Minimum 8 caractères<br>';
                isValid = false;
            }


            if (isValid) {
                passwordFeedback.innerHTML = 'Mot de passe valide ✓';
                passwordFeedback.style.color = 'green';
                isPasswordValid = true;
            } else {
                passwordFeedback.innerHTML = 'Le mot de passe doit contenir :<br>' + feedback;
                passwordFeedback.style.color = 'red';
                isPasswordValid = false;
            }

            // Vérifier à nouveau la correspondance si le champ de confirmation est déjà rempli
            if (confirmPasswordInput.value) {
                validateConfirmPassword();
            }

            updateButtonState();
        });

        // Fonction pour valider la confirmation du mot de passe
        function validateConfirmPassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword === '') {
                confirmPasswordFeedback.textContent = '';
                isConfirmPasswordValid = false;
            } else if (password === confirmPassword) {
                confirmPasswordFeedback.textContent = 'Les mots de passe correspondent ✓';
                confirmPasswordFeedback.style.color = 'green';
                isConfirmPasswordValid = true;
            } else {
                confirmPasswordFeedback.textContent = 'Les mots de passe ne correspondent pas';
                confirmPasswordFeedback.style.color = 'red';
                isConfirmPasswordValid = false;
            }

            updateButtonState();
        }

        // Validation de la confirmation du mot de passe
        confirmPasswordInput.addEventListener('input', validateConfirmPassword);

        // Validation de la date de naissance
        birthDateInput.addEventListener('input', function () {
            const birthDate = new Date(this.value);
            const today = new Date();
            const minAge = 13;
            const maxAge = 100;


            const ageDiff = today.getFullYear() - birthDate.getFullYear();
            const isMonthGreater = today.getMonth() < birthDate.getMonth();
            const isMonthEqual = today.getMonth() === birthDate.getMonth();
            const isDayLess = today.getDate() < birthDate.getDate();



            const age = isMonthGreater || (isMonthEqual && isDayLess) ? ageDiff - 1 : ageDiff;


            if (isNaN(birthDate.getTime())) {
                birthDateFeedback.textContent = 'Date de naissance invalide';
                birthDateFeedback.style.color = 'red';
                isBirthDateValid = false;
            } else if (birthDate > today) {
                birthDateFeedback.textContent = 'La date ne peut pas être dans le futur';
                birthDateFeedback.style.color = 'red';
                isBirthDateValid = false;
            } else if (age < minAge) {
                birthDateFeedback.textContent = `Vous devez avoir au moins ${minAge} ans`;
                birthDateFeedback.style.color = 'red';
                isBirthDateValid = false;
            } else if (age > maxAge) {
                birthDateFeedback.textContent = 'Âge peu probable, veuillez vérifier';
                birthDateFeedback.style.color = 'orange';
                isBirthDateValid = false;
            } else {
                birthDateFeedback.textContent = 'Date de naissance valide ✓';
                birthDateFeedback.style.color = 'green';
                isBirthDateValid = true;
            }

            updateButtonState();
        });

        imageUpload.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const img = document.querySelector('.round-image');

            if (file && ['image/png', 'image/jpeg', 'image/gif'].includes(file.type)) {
                const reader = new FileReader();
                reader.onloadend = function () {
                    img.src = reader.result;
                    profilePictureFeedback.textContent = 'Photo de profil chargée ✓';
                    profilePictureFeedback.style.color = 'green';
                    isProfilePictureValid = true;
                    updateButtonState();
                };
                reader.readAsDataURL(file);
            } else if (file) {
                profilePictureFeedback.textContent = 'Format d\'image non supporté. Utilisez PNG, JPEG ou GIF';
                profilePictureFeedback.style.color = 'red';
                isProfilePictureValid = false;
                updateButtonState();
            } else {
                profilePictureFeedback.textContent = 'Veuillez choisir une photo de profil';
                profilePictureFeedback.style.color = 'orange';
                isProfilePictureValid = false;
                updateButtonState();
            }
        });

        // Vérification initiale de la photo de profil
        if (imageUpload.files.length === 0) {
            profilePictureFeedback.textContent = 'Veuillez choisir une photo de profil';
            profilePictureFeedback.style.color = 'orange';
            isProfilePictureValid = false;
        }
    }



    function showSignupForm(e) {
        e.preventDefault();


        const loginBox = document.querySelector('.box');
        const wrapper = document.querySelector('.wrapper');


        loginBox.style.opacity = '0';
        loginBox.style.transform = 'translateY(20px)';
        loginBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

        setTimeout(() => {

            const signupBox = document.createElement('div');
            signupBox.className = 'box signup-box';
            signupBox.style.opacity = '0';
            signupBox.style.transform = 'translateY(20px)';
            signupBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            signupBox.innerHTML = `


                 <form method="post"  action="/register" enctype="multipart/form-data">
                 <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="file" id="imageUpload" name="profilPicture" accept=".png, .jpg, .jpeg, .gif"
                    style="display:none">
                    <label for="imageUpload">
                        <img src="assets/profilPic.png" class="round-image" alt="">
                    </label>
                    <div class="inputBox">
                        <input type="text" placeholder="Pseudonyme" name="login" required>
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="inputBox">
                        <input type="text" placeholder="Nom" name="name" required>
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="inputBox">
                        <input type="text" placeholder="Prenom" name="firstname" required>
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="inputBox">
                        <input type="email" placeholder="Adresse e-mail" name="email" required>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="inputBox">
                        <input type="password" placeholder="Mot de passe" name="password" required>
                        <i class='bx bxs-lock'></i>
                    </div>
                    <div class="inputBox">
                        <input type="password" placeholder="Confirmer le mot de passe"  required>
                        <i class='bx bxs-lock'></i>
                    </div>
                    <div class="inputBox">
                        <input type="date" placeholder="Date de naissance" name="birth_date" required>
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="inputBox">
                        <select name="gender" required >
                            <option value="" disabled selected>Genre</option>
                            <option value="male">Homme</option>
                            <option value="female">Femme</option>
                            <option value="others">Autre</option>
                        </select>
                        <i class='bx bx-male-female'></i>
                    </div>
                    <div class="inputBox">
                        <select name="member_type" required >
                            <option value="" disabled selected>Status</option>
                            <option value="resident">Resident</option>
                            <option value="worker">Travailleur</option>
                            <option value="visitor">Visiteur</option>
                            <option value="official">Officiel</option>
                        </select>
                    <i class='bx bx-briefcase'></i>
                    </div>
                    
                    <div class="remember-forgot">
                        <label><input type="checkbox" required> J'accepte les conditions</label>
                    </div>

                    <button type="submit" class="button">Créer un compte</button>

                    <div class="newAcc">
                        <p>Vous avez déjà un compte? <a href="/">Se connecter</a></p>
                    </div>
                </form>
            `;



            loginBox.remove();
            wrapper.insertBefore(signupBox, document.querySelector('.city-features'));

            // Ajoute un gestionnaire d'événements pour le changement de fichier sur l'élément avec l'ID 'imageUpload'.
            document.getElementById('imageUpload').addEventListener('change', function (e) {
                // Sélectionne l'image qui sera mise à jour.
                const img = document.querySelector('.round-image');
                // Récupère le premier fichier sélectionné par l'utilisateur.
                const file = e.target.files[0];
                // Crée un nouvel objet FileReader pour lire le contenu du fichier.
                const reader = new FileReader();

                // Vérifie si un fichier a été sélectionné et si son type est autorisé (png, jpeg, gif).
                if (file && ['image/png', 'image/jpeg', 'image/gif'].includes(file.type)) {
                    // Définir ce qui se passe lorsque la lecture du fichier est terminée.
                    reader.onloadend = function () {
                        // Met à jour la source de l'image avec le contenu du fichier lu.
                        img.src = reader.result;
                    }
                    // Commence à lire le contenu du fichier sous forme de Data URL.
                    reader.readAsDataURL(file);
                } else {
                    // Affiche une alerte si le fichier n'est pas du type autorisé.
                    alert('Please upload a jpg, a jpeg, a png, or a gif file!');
                }
            });
            setTimeout(() => {
                signupBox.style.opacity = '0.95';
                signupBox.style.transform = 'translateY(0)';
                setupRealtimeValidation();
            }, 50);



        }, 500);
    }


    function showResetForm(e) {
        e.preventDefault();


        const loginBox = document.querySelector('.box');
        const wrapper = document.querySelector('.wrapper');


        loginBox.style.opacity = '0';
        loginBox.style.transform = 'translateY(20px)';
        loginBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

        setTimeout(() => {

            const signupBox = document.createElement('div');
            signupBox.className = 'box signup-box';
            signupBox.style.opacity = '0';
            signupBox.style.transform = 'translateY(20px)';
            signupBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            signupBox.innerHTML = `
                <h2 class="login-title">Reintialiser votre mot de passe</h2>
                <form method="post"  action="/forgot-password">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <div class="inputBox">
                        <input type="email" placeholder="Adresse e-mail" name="email" required>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <button type="submit" class="button">Réinitialiser le mot de passe</button>
                    <div class="newAcc">
                        <p>Vous vous souvenez de votre mot de passe ?  <a href="/">Se connecter</a></p>
                    </div>
                </form>
            `;

            loginBox.remove();
            wrapper.insertBefore(signupBox, document.querySelector('.city-features'));


            setTimeout(() => {
                signupBox.style.opacity = '0.95';
                signupBox.style.transform = 'translateY(0)';
            }, 50);



        }, 500);
    }



});




