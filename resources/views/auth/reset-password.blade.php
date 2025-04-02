<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Document</title>
</head>

<body>
    <div class="wrapper">
        <div class="logo-container">
            <img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo">
        </div>
        <div class="box">
            <h1>Reset your password.</h1>
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
            <form action="{{route('password.update')}}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{$token}}">
                <div class="inputBox">
                    <input type="email" placeholder="Adresse e-mail" name="email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="inputBox">
                    <input type="password" placeholder="Mot de passe" name="password" required>
                    <i class='bx bxs-lock'></i>
                </div>
                <div class="inputBox">
                    <input type="password" placeholder="Confirmer le mot de passe" name="password_confirmation"
                        required>
                    <i class='bx bxs-lock'></i>
                </div>
                <button class="button">Réinitialiser mon mot de passe</button>
            </form>
        </div>
    </div>
</body>

</html>