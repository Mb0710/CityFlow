<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil Utilisateur</title>
  <link rel="stylesheet" href="{{ asset('css/Profil.css') }}">
</head>

<body>

  @php
  use Carbon\Carbon;
  $birthDate = Carbon::parse($user->birth_date);
  $age = $birthDate->age;
  @endphp

  <div class="logo-container logo-left">
    <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
  </div>

  <div class="profile-container">
    <div class="profile-card left">
      <div class="profile-info">


        <div class="avatar-container">
          <button class="edit-btn">✏️ Modifier</button>
          <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Photo de Profil" id="photoProfil"
            class="avatar" />
        </div>


        <div class="info-block">
          <h3>Informations personnelles</h3>
          <div class="info-item"><strong>Pseudonyme : </strong> {{ $user->login}}<span id="pseudo"></span></div>
          <div class="info-item"><strong>Âge :</strong> {{ $age }} ans <span id="age"></span></div>
          <div class="info-item"><strong>Genre : </strong> {{ $user->gender}} <span id="genre"></span></div>
          <div class="info-item"><strong>Date de naissance : </strong>{{ $user->birth_date}} <span
              id="dateNaissance"></span></div>
        </div>


      </div>

      <div class="info-block">
        <h3>Rôle et statut</h3>
        <div class="info-item"><strong>Type de membre : </strong>{{ $user->member_type}} <span id="role"></span></div>
      </div>


      <div class="info-block">
        <h3>Niveau et expérience</h3>
        <label>Niveau actuel :</label>

        <div class="niveau-affichage" id="niveauTexte">{{ $user->level}}</div>

      </div>
    </div>

    <div class="profile-card right">

      <div class="info-block">
        <h3>Privé 🔒</h3>
        <div class="info-item"><strong>Nom : </strong> {{ $user->name}} <span id="nomPrive"></span></div>
        <div class="info-item"><strong>Prénom : </strong>{{ $user->firstname}} <span id="prenomPrive"></span></div>
        <div class="info-item"><strong>Mot de passe :</strong> ********</div>
        <div class="info-item"><strong>Email : </strong>{{ $user->email}} <span id="emailPrive"></span></div>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      fetch('{{ route("user.data") }}')
        .then(response => response.json())
        .then(data => {
          console.log(data.user);

        });
    });
  </script>

</body>



</html>