var $ = require('jquery');

//déclaration de l'objet gMap
var gMap = {
    //déclaration des attributs de l'objet qui pourront etre facilement reutilisé
    map: null,

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        gMap.initMap();
        gMap.notesMarkers();
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

    //methode qui reinitialise les infos de la note sur un nouveau marqueur
    clearNoteInfos: function () {
        $('#montant').empty();
        $('#type').empty();
        $('#statut').empty();
        $('#address').empty();
        $('#postcode').empty();
        $('#city').empty();
        $('#country').empty();
        $('#description').empty();
    },

    //methode qui sauvegarde les infos de la note  les infos sont stockées en memoire sur le naviguateur
    initStorage: function (note) {
        if (jQuery.isPlainObject(note)) {
            sessionStorage.setItem("montant", note.montant);
            sessionStorage.setItem("type", note.type);
            sessionStorage.setItem("statut", note.statut);
            sessionStorage.setItem("adresse", note.address);
            sessionStorage.setItem("postcode", note.postcode);
            sessionStorage.setItem("city", note.city);
            sessionStorage.setItem("country", note.country);
            sessionStorage.setItem("description", note.description);
        }
    },

    //methode qui reinitialise les infos de la note qui sont stockées en memoire sur le naviguateur
    clearStorage: function (note) {
        sessionStorage.removeItem("montant");
        sessionStorage.removeItem("type");
        sessionStorage.removeItem("statut");
        sessionStorage.removeItem("adresse");
        sessionStorage.removeItem("postcode");
        sessionStorage.removeItem("city");
        sessionStorage.removeItem("country");
        sessionStorage.removeItem("description");
    },

    //cette methode affiche les infos de la note du marqueur clicker
    displayStationInfos: function (note) {
        // on fait appel a la methode qui reinitialise le session storage
        gMap.clearStorage(note);
        // on fait appel a la methode qui rentre les donnée de la note en session storage
        gMap.initStorage(note);
        // on fait appel a la methode qui reinitialise les infos de la note
        gMap.clearNoteInfos();
        //insertion des données de la note
        $('#montant').append("montant de la note : " + note.montant + "€");
        $('#type').append("Type de note : " + note.type);
        $('#statut').append("Statut de la note : " + note.statut);
        $('#address').append("Adresse : " + note.address + "," + " " + note.postcode + "," + " " + note.city + "," + " " + note.country);
        //apparition du cadre contenant les infos de la note
        $("#note-infos").fadeIn();
    },

    //cette methode sert a mettre les markers relatif a l'utilisateur
    notesMarkers: function () {
        $.ajax({
            url: '/user/map',
            type: 'GET',
            async: true,
            success: function (datas, status) {
                var marker;
                datas.forEach(function (note) {
                    var lat = parseFloat(note.lat);
                    var long = parseFloat(note.lng);
                    var myLatLng = {
                        lat: lat,
                        lng: long
                    };
                    marker = new google.maps.Marker({
                        map: gMap.map,
                        position: myLatLng,
                        title: note.type,
                        animation: google.maps.Animation.DROP
                    });
                    marker.addListener('click', function () {
                        gMap.displayStationInfos(note);
                    });
                });
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('Ajax request failed.');
            }
        });
    },

}


//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    gMap.init();
})