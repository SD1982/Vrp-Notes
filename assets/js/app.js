require('../css/app.css');

var $ = require('jquery');

var app = {

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        app.displayCalendar();
    },

    displayCalendar: function () {
        $('.datepicker').datepicker();
    }

}

//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    app.init();
})