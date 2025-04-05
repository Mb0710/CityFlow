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
</head>

<body>
    <div class="logo-container logo-left">
        <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
    </div>
    <div class="wrapper map-container">
        <div class="map-layout">
            <div class="box-left">
                <div id="map"></div>
            </div>
            <div class="box-right">
                <div class="box">
                    <h1>Informations</h1>
                    <form>
                        <div>
                            <label><input type="checkbox" class="filter" value="lampadaire" checked> Lampadaires</label>
                            <label><input type="checkbox" class="filter" value="parc" checked> Parcs</label>
                            <label><input type="checkbox" class="filter" value="immeuble" checked> Immeubles</label>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Nom Objet" name="nomObj" required>
                            <i class='bx bxs-info-circle'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Batterie" name="batterie" required>
                            <i class='bx bx-battery'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Status" name="status" required>
                            <i class='bx bx-station'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Zone" name="zoneId" required>
                            <i class='bx bx-buildings'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Dernier Utilisateur" name="Interaction" required>
                            <i class='bx bxs-user'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Date Création" name="Création" required>
                            <i class='bx bx-calendar'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Cordonnées" name="coordinates" required>
                            <i class='bx bxs-map'></i>
                        </div>
                    </form>
                </div>
            </div>
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
                streetViewControl: false,
                mapTypeControl: false,
                styles: [
                    {
                        "featureType": "all",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "all",
                        "elementType": "labels.text",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "all",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#38692d"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape.man_made",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#989898"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape.man_made",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#000000"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape.natural",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#386c28"
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
                                "color": "#ffffff"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.attraction",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.business",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.government",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.medical",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#fcfcfc"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.medical",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#788c40"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.place_of_worship",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "invert_lightness": true
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.school",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.sports_complex",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#000000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#000000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#000000"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "weight": "0.01"
                            },
                            {
                                "saturation": "-33"
                            },
                            {
                                "visibility": "on"
                            },
                            {
                                "hue": "#ff0000"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "labels.icon",
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
                                "color": "#000000"
                            },
                            {
                                "weight": "0.01"
                            }
                        ]
                    },
                    {
                        "featureType": "transit.line",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ff0000"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#7088b0"
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