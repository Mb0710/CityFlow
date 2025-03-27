
document.addEventListener('DOMContentLoaded', function () {

    attachCreateAccountEvent();


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


                 <form  enctype="multipart/form-data">
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
                        <select name="status" required >
                            <option value="" disabled selected>Status</option>
                            <option value="resident">Resident</option>
                            <option value="female">Travailleur</option>
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
                        <p>Vous avez déjà un compte? <a href="#" id="back-to-login">Se connecter</a></p>
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
            }, 50);


            document.getElementById('back-to-login').addEventListener('click', showLoginForm);
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
                <h2 class="login-title">Reinitialiser votre mot de passe</h2>
                <form>
                    <div class="inputBox">
                        <input type="email" placeholder="Adresse e-mail" name="email" required>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <button type="submit" class="button">Reinitialiser</button>
                    <div class="newAcc">
                        <p>Vous vous souvenez de votre mot de passe ?  <a href="#" id="back-to-login">Se connecter</a></p>
                    </div>
                </form>
            `;

            loginBox.remove();
            wrapper.insertBefore(signupBox, document.querySelector('.city-features'));


            setTimeout(() => {
                signupBox.style.opacity = '0.95';
                signupBox.style.transform = 'translateY(0)';
            }, 50);


            document.getElementById('back-to-login').addEventListener('click', showLoginForm);
        }, 500);
    }


    function showLoginForm(e) {
        e.preventDefault();


        const signupBox = document.querySelector('.box.signup-box');
        const wrapper = document.querySelector('.wrapper');


        signupBox.style.opacity = '0';
        signupBox.style.transform = 'translateY(20px)';

        setTimeout(() => {

            const newLoginBox = document.createElement('div');
            newLoginBox.className = 'box';
            newLoginBox.style.opacity = '0';
            newLoginBox.style.transform = 'translateY(20px)';
            newLoginBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            newLoginBox.innerHTML = `
                

                <form>
                    <div class="inputBox">
                        <input type="text" placeholder="Nom d'utilisateur" name="username" required>
                        <i class='bx bxs-user-circle'></i>
                    </div>
                    <div class="inputBox">
                        <input type="password" placeholder="Mot de passe" name="password" required>
                        <i class='bx bxs-lock'></i>
                    </div>

                    <div class="remember-forgot">
                        <label><input type="checkbox"> Se souvenir de moi</label>
                        <a href="#" class="forgot-link">Mot de passe oublié?</a>
                    </div>

                    <button type="submit" class="button">Se connecter</button>

                    <div class="login-divider">
                        <span>ou</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="social-btn google"><i class='bx bxl-google'></i> Google</button>
                        <button type="button" class="social-btn linkedin"><i class='bx bxl-linkedin'></i> LinkedIn</button>
                    </div>

                    <div class="newAcc">
                        <p>Vous n'avez pas de compte? <a href="#">Créer un compte</a></p>
                    </div>
                </form>
            `;


            signupBox.remove();
            wrapper.insertBefore(newLoginBox, document.querySelector('.city-features'));


            setTimeout(() => {
                newLoginBox.style.opacity = '0.95';
                newLoginBox.style.transform = 'translateY(0)';


                attachCreateAccountEvent();
            }, 50);
        }, 500);
    }
});




