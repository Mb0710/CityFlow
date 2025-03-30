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
            <h1>Please Verify your email through the email we've sent you.</h1>

            <p>Didn't get the email ?</p>
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button class="button">Send Again</button>
            </form>
        </div>
    </div>
</body>

</html>