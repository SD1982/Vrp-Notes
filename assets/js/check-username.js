var $ = require('jquery');


//déclaration de l'objet verif
var verif = {

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        verif.usernameCheck();
    },

    //methode qui recupere tout les pseudos en bdd
    usernameCheck: function () {
        setInterval(function () {
            $.ajax({
                url: '/admin/add/user',
                type: 'GET',
                async: true,
                success: function (datas, status) {
                    datas.forEach(function (user) {
                        console.log(user);
                    });
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log('Ajax request failed.');
                }
            });
        }, 2000);
    },
}

//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    verif.init();
})