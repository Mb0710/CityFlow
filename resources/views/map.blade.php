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
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCucPKHUjomqAvneDLQlnzZuNZZHktt6_U&callback=initMap"></script>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>// Variables globales
        let map;
        let markers = [];
        let currentMarker = null;



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
                                icon = "https://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
                            } else if (type === "capteur_pollution") {
                                icon = "https://maps.google.com/mapfiles/ms/icons/green-dot.png";
                            } else if (type === "borne_bus") {
                                icon = "https://maps.google.com/mapfiles/ms/icons/blue-dot.png";
                            } else if (type === "panneau_information") {
                                icon = "https://maps.google.com/mapfiles/ms/icons/purple-dot.png";
                            } else if (type === "caméra") {
                                icon = "https://maps.google.com/mapfiles/ms/icons/red-dot.png";
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
        document.addEventListener('DOMContentLoaded', function () {
            // Bouton pour ajouter un marker à la liste
            document.getElementById('add-marker-btn').addEventListener('click', function () {
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
            document.getElementById('save-marker-btn').addEventListener('click', function () {
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
                        .done(function (response) {
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
                        .fail(function (error) {
                            console.error("Erreur lors de la sauvegarde du marker:", error);
                            showStatusMessage(`Erreur: ${error.status} - ${error.statusText}`, "error");
                        });

                    promises.push(promise);
                });

                // Attendre que toutes les sauvegardes soient terminées
                $.when.apply($, promises).then(function () {
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

</body>

</html>