function success(position){
    var latval = position.coords.latitude;
    var lngval = position.coords.longitude;
    console.log([latval, lngval]);
    myLatLng = new google.maps.LatLng(latval,lngval);
    createmap(myLatLng);


    searchTestPoint(latval,lngval);
}
function createMarker(latlng,icn,name){
    var marker = new google.maps.Marker({
        position : latlng,
        map : map,
        icon:icn,
        title:name
    });
}

function searchTestPoint(lat, lng) {
    $.post('/testpoint', {lat: lat, lng: lng}, function(match) {
        $.each(match, function(i, val) {
            var platval = val.lat;
            var plngval = val.lng;
            var pname = val.name;
            var picn = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
            var pLatLng = new google.maps.LatLng(platval, plngval);
            createMarker(pLatLng, picn, pname);
        });
    });
}
let map;
let markers;

function createmap(latlng){ // crée la map comme innit map mais avec en centre le point envoyé
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
            position :  latlng,
            map: map,
        })
}

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