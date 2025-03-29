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
    /* Optional: Makes the sample page fill the window. */
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
      partie info
    </div>
  </div>

  <!-- Script pour initialiser la carte -->
  <script>
    let map;

    // S'assurer que la fonction initMap est appelée une fois que le DOM est entièrement chargé
    function initMap() {
      console.log("La fonction initMap est appelée !");  // Vérification dans la console

      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 49.03485870361328, lng: 2.070168972015381 },
        minZoom: 14,
        maxZoom: 19,
        zoom: 15,
        mapTypeControl: false,
        streetViewControl: false,
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

      var strictBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(49.025, 2.055),
        new google.maps.LatLng(49.045, 2.075)
      );

      // Écouter l'événement de fin de déplacement de la carte
      google.maps.event.addListener(map, 'dragend', function () {
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
    }
  </script>

</body>

</html>
