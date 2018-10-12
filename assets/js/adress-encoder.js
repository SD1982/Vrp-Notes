var $ = require('jquery');

//déclaration de l'objet encoder
var encoder = {

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        encoder.encodeAdress();
    },

    // methode qui convertit une adresse en coordonnées gps
    encodeAdress: function () {
        geocoder = new google.maps.Geocoder();
        $('.action-button').click(function () {
            var adress = document.getElementById('note_adress').value;
            var postcode = document.getElementById('note_postcode').value;
            var city = document.getElementById('note_city').value;
            var country = document.getElementById('note_country').value;
            var fullAdress = adress + ", " + postcode + " " + city + ", " + country;
            geocoder.geocode({
                'address': fullAdress
            }, function (results, status) {
                if (status == 'OK') {
                    $('#note_latitude').val(results[0].geometry.location.lat());
                    $('#note_longitude').val(results[0].geometry.location.lng());
                } else {
                    alert('Cette adresse n\'existe pas ');
                }
            });
        });
    },

}


//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    encoder.init();
})