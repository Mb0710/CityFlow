<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="icon" href="{{ asset('assets/logo2.png') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  
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
    
    .marker-item {
      margin-bottom: 8px;
      padding: 5px;
      background-color: #f0f0f0;
      border-radius: 4px;
    }
    
    .status-message {
      margin-top: 10px;
      padding: 5px;
    }
    
    .success {
      background-color: #d4edda;
      color: #155724;
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
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
      <button id="add-marker-btn">Ajouter Marker</button>
      <button id="save-marker-btn">Sauvegarder dans la BDD</button>
      <div id="status-message" class="status-message" style="display: none;"></div>
      <h4>Liste des Markers :</h4>
      <ul id="marker-list"></ul>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Variables globales
    let map;
    let markers = [];
    let currentMarker = null;
    
    // Fonction appelée après géolocalisation réussie
    function success(position) {
      var latval = position.coords.latitude;
      var lngval = position.coords.longitude;
      console.log([latval, lngval]);
      myLatLng = new google.maps.LatLng(latval, lngval);
      createmap(myLatLng);
      
      searchTestPoint(latval, lngval);
    }
    
    function createMarker(latlng, icn, name, saved = false) {
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        icon: icn,
        title: name
      });
      
      // Ajouter un infobulle pour le marker
      const infowindow = new google.maps.InfoWindow({
        content: `<div><strong>${name}</strong><br>Latitude: ${latlng.lat()}<br>Longitude: ${latlng.lng()}<br>${saved ? "Enregistré" : "Non enregistré"}</div>`
      });

      marker.addListener('click', () => {
        infowindow.open(map, marker);
      });
      
      return marker;
    }
    
function searchTestPoint(lat, lng) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  $.ajax({
    url: '/testpoint',
    type: 'POST',
    data: {
      _token: csrfToken,
      lat: lat,
      lng: lng
    },
    dataType: 'json',
    success: function(match) {
      console.log("Points récupérés:", match);
      if (Array.isArray(match)) {
        $.each(match, function(i, val) {
          var platval = val.lat;
          var plngval = val.lng;
          var pname = val.name;
          var picn = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
          var pLatLng = new google.maps.LatLng(platval, plngval);
          createMarker(pLatLng, picn, pname, true);
        });
      } else {
        console.warn("Format de réponse inattendu:", match);
      }
    },
    error: function(error) {
      console.error("Erreur lors de la recherche des points:", error);
      showStatusMessage("Erreur lors de la récupération des points existants.", "error");
    }
  });
}
    
function saveMarkerToDatabase(marker) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  const data = {
    _token: csrfToken,
    name: marker.name,
    lat: marker.position.lat,
    lng: marker.position.lng
  };
  
  console.log("Envoi des données pour sauvegarde:", data);
  
  return $.ajax({
    url: '/testpoint/store',
    type: 'POST',
    data: data,
    dataType: 'json',
    error: function(xhr, status, error) {
      console.error("Détails de l'erreur:", {
        status: xhr.status,
        statusText: xhr.statusText,
        responseText: xhr.responseText
      });
    }
  });
}
    
    function createmap(latlng) {
      map = new google.maps.Map(document.getElementById("map"), {
        center: latlng,
        minZoom: 14,
        maxZoom: 19,
        zoom: 15,
        streetViewControl: false,
        mapTypeControl: false,
        styles: [/* Vos styles ici */]
      });
      
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
      });
      
      // Définir les limites strictes après création de la carte
      var strictBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(49.025, 2.055),
        new google.maps.LatLng(49.045, 2.075)
      );

      // Écouter l'événement de fin de déplacement de la carte
      google.maps.event.addListener(map, 'dragend', function() {
        if (strictBounds.contains(map.getCenter())) return;

        // La carte est en dehors des limites - Replacer la carte dans les limites
        var c = map.getCenter(),
            x = c.lng(),
            y = c.lat(),
            maxX = strictBounds.getNorthEast().lng(),
            maxY = strictBounds.getNorthEast().lat(),
            minX = strictBounds.getSouthWest().lng(),
            minY = strictBounds.getSouthWest().lat();

        if (x < minX) x = minX;
        if (x > maxX) x = maxX;
        if (y < minY) y = minY;
        if (y > maxY) y = maxY;

        // Recentrer la carte dans les limites
        map.setCenter(new google.maps.LatLng(y, x));
      });
      
      // Ajouter l'écouteur pour les clics sur la carte
      map.addListener('click', function(event) {
        const markerName = document.getElementById("marker-name").value;
        if (markerName) {
          placeTemporaryMarker(event.latLng, markerName);
        } else {
          showStatusMessage("Veuillez entrer un nom pour le marker.", "error");
        }
      });
    }
    
    function placeTemporaryMarker(latLng, name) {
      // Supprimer le marqueur temporaire précédent s'il existe
      if (currentMarker) {
        currentMarker.setMap(null);
      }
      
      // Créer un nouveau marqueur temporaire
      currentMarker = new google.maps.Marker({
        position: latLng,
        map: map,
        title: name,
        animation: google.maps.Animation.DROP,
        draggable: true
      });
      
      // Ajouter un infobulle
      const infowindow = new google.maps.InfoWindow({
        content: `<div><strong>${name}</strong><br>Latitude: ${latLng.lat()}<br>Longitude: ${latLng.lng()}<br>Cliquez sur 'Ajouter Marker' pour confirmer</div>`
      });
      
      currentMarker.addListener('click', () => {
        infowindow.open(map, currentMarker);
      });
      
      // Ouvrir l'infobulle automatiquement
      infowindow.open(map, currentMarker);
    }
    
    function showStatusMessage(message, type) {
      const statusElement = document.getElementById("status-message");
      statusElement.textContent = message;
      statusElement.className = "status-message " + type;
      statusElement.style.display = "block";
      
      // Effacer le message après 3 secondes
      setTimeout(() => {
        statusElement.style.display = "none";
      }, 3000);
    }

    function initMap() {
  // Définir les coordonnées du centre souhaité
  const centrePersonnalise = {
    lat: 49.035, // Latitude de votre choix
    lng: 2.065   // Longitude de votre choix
  };
  
  // Créer la carte directement avec ces coordonnées
  myLatLng = new google.maps.LatLng(centrePersonnalise.lat, centrePersonnalise.lng);
  createmap(myLatLng);
  
  // Rechercher les points existants autour de ce centre
  searchTestPoint(centrePersonnalise.lat, centrePersonnalise.lng);
}
    
    // Attacher les fonctions aux boutons une fois que le DOM est chargé
    document.addEventListener('DOMContentLoaded', function() {
      // Bouton pour ajouter un marker à la liste
      document.getElementById('add-marker-btn').addEventListener('click', function() {
        if (!currentMarker) {
          showStatusMessage("Veuillez d'abord cliquer sur la carte pour placer un marker.", "error");
          return;
        }
        
        const markerName = document.getElementById("marker-name").value;
        if (!markerName) {
          showStatusMessage("Veuillez entrer un nom pour le marker.", "error");
          return;
        }
        
        // Position du marker
        const position = currentMarker.getPosition();
        const lat = position.lat();
        const lng = position.lng();
        
        // Ajouter le marker à notre tableau
        markers.push({
          name: markerName,
          position: { lat, lng },
          googleMarker: currentMarker,
          saved: false
        });
        
        // Ne pas supprimer le marker courant car il fait maintenant partie de notre liste
        currentMarker = null;
        
        // Mettre à jour la liste des markers
        updateMarkerList();
        
        // Réinitialiser le champ de nom
        document.getElementById("marker-name").value = "";
        
        showStatusMessage("Marker ajouté à la liste. Cliquez sur 'Sauvegarder dans la BDD' pour enregistrer.", "success");
      });
      
      // Bouton pour sauvegarder tous les markers dans la BDD
      document.getElementById('save-marker-btn').addEventListener('click', function() {
        const unsavedMarkers = markers.filter(marker => !marker.saved);
        
        if (unsavedMarkers.length === 0) {
          showStatusMessage("Aucun nouveau marker à sauvegarder.", "error");
          return;
        }
        
        let savedCount = 0;
        let promises = [];
        
        // Sauvegarder chaque marker non sauvegardé
        unsavedMarkers.forEach((marker) => {
          const promise = saveMarkerToDatabase(marker)
            .done(function(response) {
              console.log("Réponse du serveur:", response);
              // Marquer le marker comme sauvegardé
              const index = markers.findIndex(m => 
                m.name === marker.name && 
                m.position.lat === marker.position.lat && 
                m.position.lng === marker.position.lng
              );
              
              if (index !== -1) {
                markers[index].saved = true;
                savedCount++;
                
                // Changer l'icône pour indiquer qu'il est sauvegardé
                if (markers[index].googleMarker) {
                  markers[index].googleMarker.setIcon("https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png");
                }
              }
            })
            .fail(function(error) {
              console.error("Erreur lors de la sauvegarde du marker:", error);
              showStatusMessage(`Erreur: ${error.status} - ${error.statusText}`, "error");
            });
          
          promises.push(promise);
        });
        
        // Attendre que toutes les sauvegardes soient terminées
        $.when.apply($, promises).then(function() {
          updateMarkerList();
          if (savedCount > 0) {
            showStatusMessage(`${savedCount} marker(s) sauvegardé(s) avec succès!`, "success");
          } else {
            showStatusMessage("Aucun marker n'a pu être sauvegardé.", "error");
          }
        });
      });
    });
    
    function updateMarkerList() {
      const list = document.getElementById("marker-list");
      list.innerHTML = "";

      markers.forEach((marker) => {
        const li = document.createElement("li");
        li.className = "marker-item";
        li.innerHTML = `
          <strong>${marker.name}</strong> 
          (Lat: ${marker.position.lat.toFixed(4)}, Lng: ${marker.position.lng.toFixed(4)})
          <span style="color: ${marker.saved ? 'green' : 'red'}">
            ${marker.saved ? '✓ Enregistré' : '⨯ Non enregistré'}
          </span>
        `;
        list.appendChild(li);
      });
    }
  </script>
  <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=initMap"></script>
</body>
</html>