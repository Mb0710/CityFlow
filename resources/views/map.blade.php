<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="icon" href="{{ asset('./assets/logo2.png') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Simple Marker</title>
  <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=initMap&libraries=maps,marker&v=beta"></script>

  <style>
    body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    #map {
      border: 1px;
      width: 50vw;
      height: 85vh;
      align-self: center;
      margin: 0 auto;
    }

    .container {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 10px;
    }

    .card {
      padding: 20px;
      border: 1px solid #ccc;
    }

    .card-L {
      background-color: #1c78c9;
    }

    .card-S {
      background-color: #53a137;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card card-L">
      <div id="map"></div>
    </div>
    <div class="card card-S">
      <h3>Nom du Marker :</h3>
        <input type="text" id="marker-name" placeholder="Entrez un nom pour le marker" />
        <button onclick="addMarker()">Ajouter Marker</button>
        <h4>Liste des Markers :</h4>
        <ul id="marker-list"></ul>
    </div>
  </div>

  <!-- Script pour initialiser la carte -->
  <script>
    let map;
    let markers = [];

    // Fonction pour initialiser la carte
    function initMap() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 49.03485870361328, lng: 2.070168972015381 },
        minZoom: 14,
        maxZoom: 19,
        zoom: 15,
        styles: [
          {
            "featureType": "landscape.natural",
            "elementType": "geometry.fill",
            "stylers": [
              {
                "visibility": "on"
              },
              {
                "color": "#e0efef"
              }
            ]
          },
          {
            "featureType": "poi",
            "elementType": "geometry.fill",
            "stylers": [
              {
                "visibility": "on"
              },
              {
                "hue": "#1900ff"
              },
              {
                "color": "#c0e8e8"
              }
            ]
          },
          {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [
              {
                "lightness": 100
              },
              {
                "visibility": "simplified"
              }
            ]
          },
          {
            "featureType": "road",
            "elementType": "labels",
            "stylers": [
              {
                "visibility": "off"
              }
            ]
          },
          {
            "featureType": "transit.line",
            "elementType": "geometry",
            "stylers": [
              {
                "visibility": "on"
              },
              {
                "lightness": 700
              }
            ]
          },
          {
            "featureType": "water",
            "elementType": "all",
            "stylers": [
              {
                "color": "#7dcdcd"
              }
            ]
          },
          {
            featureType: "poi",
            elementType: "labels",
            stylers: [
              { visibility: "off" }
            ]
          }
        ]
      });
    }

    // Fonction pour ajouter un marker
    function addMarker() {
      // Récupérer le nom du marker à partir de l'input
      const markerName = document.getElementById("marker-name").value;

      if (!markerName) {
        alert("Veuillez entrer un nom pour le marker.");
        return;
      }

      // Position du marker (ici, on prend un exemple d'emplacement par défaut)
      const latLng = map.getCenter(); // Prendre la position du centre actuel de la carte
      const lat = latLng.lat();
      const lng = latLng.lng();

      // Créer le marker
      const marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: markerName
      });

      // Ajouter un infobulle pour le marker
      const infowindow = new google.maps.InfoWindow({
        content: `<div><strong>${markerName}</strong><br>Latitude: ${lat}<br>Longitude: ${lng}</div>`
      });

      marker.addListener('click', () => {
        infowindow.open(map, marker);
      });

      // Ajouter le marker et ses informations à notre tableau `markers`
      markers.push({
        name: markerName,
        position: { lat, lng }
      });

      // Mettre à jour la liste des markers dans l'interface
      updateMarkerList();
    }

    // Fonction pour afficher la liste des markers
    function updateMarkerList() {
      const list = document.getElementById("marker-list");
      list.innerHTML = ""; // Réinitialiser la liste

      markers.forEach((marker, index) => {
        const li = document.createElement("li");
        li.textContent = `${marker.name} (Lat: ${marker.position.lat}, Lng: ${marker.position.lng})`;
        list.appendChild(li);
      });
    }
  </script>

</body>

</html>
