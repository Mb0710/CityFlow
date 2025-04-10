<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Validation des Inscriptions</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/Inscription.css') }}">
  <style>
    .no-data-message {
      text-align: center;
      padding: 20px;
      font-size: 18px;
      color: #666;
      width: 100%;
    }

    .message-confirmation {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #4CAF50;
      color: white;
      padding: 12px 24px;
      border-radius: 4px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      z-index: 1000;
    }

    .message-confirmation.error {
      background-color: #f44336;
    }

    .device-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin: 10px;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
    }

    .device-infos {
      margin-bottom: 15px;
    }

    .info-item {
      margin-bottom: 5px;
    }

    .validation-zone {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .valider,
    .refuser {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    .valider {
      background-color: #4CAF50;
      color: white;
    }

    .refuser {
      background-color: #f44336;
      color: white;
    }

    .device-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <div class="titre-container">
    <div class="logo-site">
      <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo" class="logo-responsive"></a>
    </div>

    <img src="{{ asset('./assets/nuage.png') }}" alt="Nuage" class="nuage-img">

    <div class="cloud-title">Validation des Inscriptions</div>
  </div>

  <div class="device-grid" id="inscriptionGrid">
    <!-- Les inscriptions en attente seront chargées ici -->
  </div>

  <div id="confirmation-message" class="message-confirmation" style="display: none;">
    ✅ Demande traitée.
  </div>

  <script src="{{ asset('js/Inscription.js') }}"></script>
</body>

</html>