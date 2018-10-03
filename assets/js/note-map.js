var $ = require('jquery');

//déclaration de l'objet gMap
var gMap = {
    //déclaration des attributs de l'objet qui pourront etre facilement reutilisé
    map: null,

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        gMap.initMap();
        gMap.noteMarker();
    },

    //cette methode sert a initialiser la map
    initMap: function () {
        gMap.map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 46.52863469527167,
                lng: 2.43896484375
            },
            zoom: 5
        });
    },


    noteMarker: function () {
        var noteCoords = document.querySelector('.note-global-content');
        var lat = parseFloat(noteCoords.dataset.lat);
        var long = parseFloat(noteCoords.dataset.long);
        var myLatLng = {
            lat: lat,
            lng: long
        };
        console.log(myLatLng);
        marker = new google.maps.Marker({
            map: gMap.map,
            position: myLatLng,
            animation: google.maps.Animation.DROP
        });
    },
}

//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    gMap.init();
})