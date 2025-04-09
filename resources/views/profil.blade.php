<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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


  <div class="titre-container flottant">

    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Profil</div>
  </div>

  <form id="profile-form" method="POST" action="{{ route('update.profile') }}" enctype="multipart/form-data">
    @csrf
    <div class="profile-container @if($user->id !== $currentUser->id) centered @endif">
      <div class="profile-card left">
        <div class="profile-info">

          <div class="avatar-container">
            @if($user->id === $currentUser->id)
        <button type="button" class="edit-btn">✏️ Modifier</button>
      @endif
            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Photo de Profil" class="avatar" />
            <input type="file" id="profile-picture-input" name="profile_picture" style="display: none;">
          </div>

          <div class="info-block">
            <h3>Informations personnelles</h3>
            <div class="info-item"><strong>Pseudonyme : </strong> <input name="login" value="{{ $user->login }}"
                readonly></div>
            <div class="info-item"><strong>Âge :</strong> {{ $age }} ans</div>
            <div class="info-item">
              <strong>Genre : </strong>
              <select name="gender" disabled>
                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Homme</option>
                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Femme</option>
                <option value="others" {{ $user->gender == 'others' ? 'selected' : '' }}>Autre</option>
              </select>
            </div>
            <div class="info-item">
              <strong>Date de naissance : </strong>
              <input type="date" name="birth_date" value="{{ Carbon::parse($user->birth_date)->format('Y-m-d') }}"
                max="2007-12-31" min="{{ now()->subYears(120)->format('Y-m-d') }}" readonly>
            </div>
          </div>
        </div>

        <div class="info-block">
          <h3>Rôle et statut</h3>
          <div class="info-item">
            <strong>Type de membre : </strong>
            <select name="member_type" disabled>
              <option value="resident" {{ $user->member_type == 'resident' ? 'selected' : '' }}>Resident</option>
              <option value="visitor" {{ $user->member_type == 'visitor' ? 'selected' : '' }}>Visiteur</option>
              <option value="worker" {{ $user->member_type == 'worker' ? 'selected' : '' }}>Travailleur</option>
              <option value="official" {{ $user->member_type == 'official' ? 'selected' : '' }}>Officiel</option>
            </select>
          </div>
        </div>

        <div class="info-block">
          <h3>Niveau et expérience</h3>
          <label>Niveau actuel :</label>
          <div class="niveau-affichage" id="niveauTexte">{{ $user->level }}</div>
        </div>
      </div>

      @if($user->id === $currentUser->id)
      <div class="profile-card right">
      <div class="info-block">
        <h3>Privé 🔒</h3>
        <div class="info-item"><strong>Nom : </strong> <input name="name" value="{{ $user->name }}" readonly></div>
        <div class="info-item"><strong>Prénom : </strong><input name="firstname" value="{{ $user->firstname }}"
          readonly></div>
        <div class="info-item"><strong>Email : </strong><input type="email" name="email" value="{{ $user->email }}"
          readonly></div>
        <div class="info-item">
        <strong>Mot de passe : </strong>
        <input type="password" name="password" autocomplete="new-password" readonly>
        </div>
        <div class="info-item">
        <strong>Confirmer le mot de passe : </strong>
        <input type="password" name="password_confirmation" autocomplete="new-password" readonly>
        </div>
      </div>
      </div>
    @endif
    </div>
  </form>

  <script src="{{ asset('js/profilUpdate.js') }}"></script>
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