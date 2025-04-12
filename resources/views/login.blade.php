<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset("./assets/logo2.png") }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!--  Page de connexion avec un model3d réalisé via l'editeur three.js editor et manipulé par three.js-->
    <div class="wrapper">
        <div id="container3D"></div>
        <div class="logo-container">
            <img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo">
        </div>
        <div class="box">
            <h2 class="login-title">Bienvenue sur City Flow</h2>
            <p class="login-subtitle">Connectez-vous pour gérer votre ville intelligente</p>
            <!-- Simple moyen de recuperer les quelconques erreur liée a la connexion ( compte inexistant, mot de passe incorrect...)-->
            @if ($errors->any())
                <div class="error-container">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif
            <!-- Envoi des données de l'utilisateur à login.submit qui via la methode login va verifier que l'utilisateur peut se connecter-->
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="inputBox">
                    <input type="text" placeholder="Nom d'utilisateur" name="login" required>
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


                <div class="newAcc">
                    <p>Vous n'avez pas de compte? <a href="#">Créer un compte</a></p>
                </div>
            </form>
        </div>
        <!-- elements visuels pour le free tour -->
        <div class="city-features">
            <div class="feature">
                <i class='bx bx-building-house'></i>
                <span>Gestion Urbaine</span>
            </div>
            <div class="feature">
                <i class='bx bx-line-chart'></i>
                <span>Analyse en Temps Réel</span>
            </div>
            <div class="feature">
                <i class='bx bx-leaf'></i>
                <span>Développement Durable</span>
            </div>
        </div>
    </div>
    <script type="module" src="{{ asset('js/main.js') }}"></script>
    <script type="module" src="{{ asset('js/model3d.js') }}"></script>
</body>

</html>