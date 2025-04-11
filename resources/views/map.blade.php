<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Flow - Plateforme de Gestion de Ville Intelligente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('./assets/logo2.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Simple Marker</title>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=initMap&loading=async">
        </script>
</head>

<body>
    <!--  Logo du site qui permet aussi de revenir au dashboard-->
    <div class="logo-container logo-left">
        <a href="/"><img src="{{ asset('./assets/logo.png') }}" alt="City Flow Logo"></a>
    </div>
    <div class="wrapper map-container">
        <div class="map-layout">
            <!--  Division de la page en 2 parties, gauche pour la carte et droite pour les informations associés a chaque point -->
            <div class="box-left">
                <div id="map"></div>
            </div>
            <div class="box-right">
                <div class="box">
                    <form>
                        <div>
                            <label><input type="checkbox" class="filter" value="lampadaire" checked> Lampadaires</label>
                            <label><input type="checkbox" class="filter" value="caméra" checked> Caméra</label>
                            <label><input type="checkbox" class="filter" value="capteur_pollution" checked>
                                Capteur</label>
                            <label><input type="checkbox" class="filter" value="panneau_information" checked>
                                Informations</label>
                            <label><input type="checkbox" class="filter" value="borne_bus" checked> Bus</label>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Nom Objet" name="nomObj" readonly>
                            <i class='bx bxs-info-circle'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Description" name="description" readonly>
                            <i class='bx bx-info-circle'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Attributs" name="attributes" readonly>
                            <i class='bx bx-list-ul'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Batterie" name="batterie" readonly>
                            <i class='bx bx-battery'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Status" name="status" readonly>
                            <i class='bx bx-station'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Zone" name="zoneId" readonly>
                            <i class='bx bx-buildings'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Date Création" name="Création" readonly>
                            <i class='bx bx-calendar'></i>
                        </div>
                        <div class="inputBox">
                            <input type="text" placeholder="Cordonnées" name="coordinates" readonly>
                            <i class='bx bxs-map'></i>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Script pour initialiser la carte -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let map;
        let markers = [];
        let currentMarker = null;

        let markersByCategory = {
            'lampadaire': [],
            'caméra': [],
            'capteur_pollution': [],
            'panneau_information': [],
            'borne_bus': []
        };


        // fonction appliquer different filtres selon le type d'objet qu'on veut afficher
        function applyFilters() {
            const filterCheckboxes = document.querySelectorAll('.filter');

            for (const category in markersByCategory) {
                const checkbox = Array.from(filterCheckboxes).find(cb => cb.value === category);
                const shouldShow = checkbox ? checkbox.checked : true;

                markersByCategory[category].forEach(marker => {
                    marker.setVisible(shouldShow);
                });
            }
        }

        // fonction  qui utilise la fonction precedente en fonction des case cochées
        function setupFilterListeners() {
            const filterCheckboxes = document.querySelectorAll('.filter');
            filterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });
        }


        // fonction qui permet de placer temporaire sur la carte
        function createMarker(latlng, icn, name, type, objData = {}) {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                icon: icn,
                title: name,
                data: objData
            });

            markers.push(marker);


            if (type && markersByCategory[type]) {
                markersByCategory[type].push(marker);
            }


            // Ajouter l'événement  de survol du marqueur
            marker.addListener('mouseover', function () {
                let formattedDate = "";
                if (objData.created_at) {
                    const date = new Date(objData.created_at);
                    formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

                }
                let attributesStr = "";
                if (objData.attributes) {
                    try {

                        let attrs = objData.attributes;
                        if (typeof attrs === 'string') {
                            attrs = JSON.parse(attrs);
                        }


                        if (typeof attrs === 'object') {
                            attributesStr = Object.entries(attrs)
                                .map(([key, value]) => `${key}: ${value}`)
                                .join(', ');
                        } else {
                            attributesStr = attrs.toString();
                        }
                    } catch (e) {
                        console.error("Erreur de parsing des attributs:", e);
                        attributesStr = objData.attributes;
                    }
                }
                document.querySelector('input[name="attributes"]').value = attributesStr;
                document.querySelector('input[name="nomObj"]').value = objData.name || name || "";
                document.querySelector('input[name="description"]').value = objData.description || "";
                document.querySelector('input[name="attributes"]').value = attributesStr;
                document.querySelector('input[name="batterie"]').value = objData.battery_level || "";
                document.querySelector('input[name="status"]').value = objData.status || "";
                document.querySelector('input[name="zoneId"]').value = objData.zone_id || "";
                document.querySelector('input[name="Création"]').value = formattedDate;
                document.querySelector('input[name="coordinates"]').value = latlng.lat().toFixed(6) + ", " + latlng.lng().toFixed(6);
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
                success: function (objects) {
                    console.log("Objets connectés récupérés:", objects);
                    if (Array.isArray(objects)) {
                        objects.forEach(function (obj) {
                            var latval = obj.lat;
                            var lngval = obj.lng;
                            var name = obj.name || "Sans nom";
                            var type = obj.type || "inconnu";

                            // Choisir l'icône en fonction du type d'objet
                            var icon;
                            if (type === "lampadaire") {
                                icon = '/assets/I_Lampadaire.png';
                            } else if (type === "capteur_pollution") {
                                icon = '/assets/I_CapteurPollution.png';
                            } else if (type === "borne_bus") {
                                icon = '/assets/I_BorneBus.png';
                            } else if (type === "panneau_information") {
                                icon = '/assets/I_PanneauInformation.png';
                            } else if (type === "caméra") {
                                icon = '/assets/I_Camera.png';
                            } else {
                                icon = "https://maps.google.com/mapfiles/ms/icons/red-dot.png";
                            }

                            var objLatLng = new google.maps.LatLng(latval, lngval);

                            // Créer le marqueur pour cet objet
                            createMarker(objLatLng, icon, name, type, obj);
                        });
                    } else {
                        console.warn("Format de réponse inattendu:", objects);
                    }
                },
                error: function (error) {
                    console.error("Erreur lors de la recherche des objets:", error);
                    alert("Erreur lors de la récupération des objets connectés.");
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
                error: function (xhr, status, error) {
                    console.error("Détails de l'erreur:", {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText
                    });
                }
            });
        }


        //fonction qui permet de "creer" la carte.
        function createmap(latlng) {
            map = new google.maps.Map(document.getElementById("map"), {
                center: latlng,
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

            // Ajouter l'écouteur pour les clics sur la carte
            map.addListener('click', function (event) {
                const markerName = document.getElementById("marker-name").value;
                if (markerName) {
                    placeTemporaryMarker(event.latLng, markerName);
                } else {
                    showStatusMessage("Veuillez entrer un nom pour le marker.", "error");
                }
            });
        }


        // fonction qui permet d'afficher un message au cas où une erreur survient
        function showStatusMessage(message, type) {
            const statusElement = document.getElementById("status-message");
            statusElement.textContent = message;
            statusElement.className = "status-message " + type;
            statusElement.style.display = "block";


            setTimeout(() => {
                statusElement.style.display = "none";
            }, 3000);
        }
        // fonction qui permet d'initialiser les fonctionnalités de la carte
        function initMap() {

            const centrePersonnalise = {
                lat: 49.035,
                lng: 2.065
            };


            myLatLng = new google.maps.LatLng(centrePersonnalise.lat, centrePersonnalise.lng);
            createmap(myLatLng);


            searchTestPoint(centrePersonnalise.lat, centrePersonnalise.lng);

            setTimeout(setupFilterListeners, 500);
        }


    </script>

</body>

</html>