import * as THREE from "https://cdn.skypack.dev/three@0.129.0/build/three.module.js";
import { GLTFLoader } from "https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js";

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);

let object;
let animationActive = true;

const loader = new GLTFLoader();

loader.load(
    './assets/scene2.glb',
    function (gltf) {
        object = gltf.scene;


        const box = new THREE.Box3().setFromObject(object);
        const center = box.getCenter(new THREE.Vector3());


        object.position.set(0, -20, -20);


        const size = box.getSize(new THREE.Vector3());
        const maxDim = Math.max(size.x, size.y, size.z);
        const scale = 16.0 / maxDim;
        object.scale.set(scale, scale, scale);


        scene.add(object);


        camera.position.set(-1, -10, -10);


        animate();
    }
);


/*loader.load(
    './assets/cloud.glb',
    function (gltf) {
        object = gltf.scene;


        const box = new THREE.Box3().setFromObject(object);
        const center = box.getCenter(new THREE.Vector3());


        object.position.set(15, -0, -20);


        const size = box.getSize(new THREE.Vector3());
        const maxDim = Math.max(size.x, size.y, size.z);
        const scale = 16.0 / maxDim;
        object.scale.set(scale, scale, scale);


        scene.add(object);


        camera.position.set(-1, -10, -10);


        animate();
    }
);*/

const renderer = new THREE.WebGLRenderer({
    alpha: true,
    antialias: true
});
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setClearColor(0x000000, 0);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;

document.getElementById("container3D").appendChild(renderer.domElement);


const mainLight = new THREE.DirectionalLight(0xffffff, 0.2);
mainLight.position.set(5, 3, 5);
mainLight.castShadow = true;
scene.add(mainLight);

const backLight = new THREE.DirectionalLight(0xffffff, 0.5);
backLight.position.set(-5, 2, -5);
scene.add(backLight);


function animate() {
    requestAnimationFrame(animate);

    if (object && animationActive) {

        object.rotation.y += 0.005;
    }

    renderer.render(scene, camera);
}


window.addEventListener("resize", function () {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});




document.addEventListener('DOMContentLoaded', function () {

    attachCreateAccountEvent();

    // Fonction pour attacher l'événement au lien "Créer un compte"
    function attachCreateAccountEvent() {
        const createAccountLink = document.querySelector('.newAcc a');
        if (createAccountLink) {
            // Retirer les anciens événements pour éviter les doublons
            createAccountLink.removeEventListener('click', showSignupForm);
            // Ajouter le nouvel événement
            createAccountLink.addEventListener('click', showSignupForm);
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
                <h2 class="login-title">Créer un compte</h2>
                <p class="login-subtitle">Rejoignez City Flow pour gérer votre ville intelligente</p>

                <form>
                    <div class="inputBox">
                        <input type="text" placeholder="Nom complet" name="fullname" required>
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
                        <input type="password" placeholder="Confirmer le mot de passe" name="confirm_password" required>
                        <i class='bx bxs-lock'></i>
                    </div>

                    <div class="remember-forgot">
                        <label><input type="checkbox" required> J'accepte les conditions</label>
                    </div>

                    <button type="submit" class="button">Créer un compte</button>

                    <div class="login-divider">
                        <span>ou</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="social-btn google"><i class='bx bxl-google'></i> Google</button>
                        <button type="button" class="social-btn linkedin"><i class='bx bxl-linkedin'></i> LinkedIn</button>
                    </div>

                    <div class="newAcc">
                        <p>Vous avez déjà un compte? <a href="#" id="back-to-login">Se connecter</a></p>
                    </div>
                </form>
            `;

            // Remplacer le formulaire
            loginBox.remove();
            wrapper.insertBefore(signupBox, document.querySelector('.city-features'));

            // Animation d'entrée
            setTimeout(() => {
                signupBox.style.opacity = '0.95';
                signupBox.style.transform = 'translateY(0)';
            }, 50);


            document.getElementById('back-to-login').addEventListener('click', showLoginForm);
        }, 500);
    }

    // Fonction pour afficher le formulaire de connexion
    function showLoginForm(e) {
        e.preventDefault();

        // Récupérer le formulaire d'inscription existant
        const signupBox = document.querySelector('.box.signup-box');
        const wrapper = document.querySelector('.wrapper');

        // Animation de sortie
        signupBox.style.opacity = '0';
        signupBox.style.transform = 'translateY(20px)';

        setTimeout(() => {
            // Recréer le formulaire de connexion
            const newLoginBox = document.createElement('div');
            newLoginBox.className = 'box';
            newLoginBox.style.opacity = '0';
            newLoginBox.style.transform = 'translateY(20px)';
            newLoginBox.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            newLoginBox.innerHTML = `
                <h2 class="login-title">Bienvenue sur City Flow</h2>
                <p class="login-subtitle">Connectez-vous pour gérer votre ville intelligente</p>

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




