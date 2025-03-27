<!DOCTYPE html>
<html>

<head>
  <title>Simple Marker</title>
  <!-- Inclure l'API Google Maps avec la clé et la fonction de rappel 'initMap' -->
  <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=initMap&libraries=maps,marker&v=beta"></script>
  <style>
    /* Toujours définir la hauteur de la carte */
    #map {
      height: 100%;
    }

    /* Optionnel: faire en sorte que la page occupe tout l'espace de la fenêtre */
    html,
    body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
  </style>
</head>

<body>
  <!-- Conteneur de la carte -->
  <div id="map"></div>

  <!-- Script pour initialiser la carte -->
  <script>
    let map;

    function initMap() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 49.03485870361328, lng: 2.070168972015381 },
        minZoom: 14,
        maxZoom: 17,
        zoom: 15,
        restriction: {
          latLngBounds: {
            east: 49.031393,
            north: 2.060780,
            south: 2.069422,
            west: 49.036251,
          },
          strictBounds: true
        },
      });
    }
  </script>
</body>

</html>