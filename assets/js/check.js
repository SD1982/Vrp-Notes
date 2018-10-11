var $ = require('jquery');


//déclaration de l'objet verif
var verif = {

    checked_pseudo: 0,

    // cette methode servira a appeler toute les methodes du script en une seule fois au chargement de la page
    init: function () {
        verif.check();
    },

    //methode qui verifie l'identifiant
    checkUsername: function (datas) {
        var pseudo = $('#user_registration_username').val();
        datas.forEach(function (user) {
            if (user.username == pseudo) {
                $('#username-check').text('Cet identifiant est déja utilisé !! ').css("color", "red");
                $('#user_registration_button').fadeOut();
                verif.checked_pseudo = 1;
                clearInterval();
            }
        });
        if (verif.checked_pseudo == 0) {
            clearInterval();
            $('#username-check').text('Cet identifiant est valide !! ').css("color", "green");
            $('#user_registration_button').fadeIn();
        }
    },

    //methode qui recupere tout les pseudos en bdd et verifie la validité d'un pseudo
    check: function () {
        setInterval(function () {
            verif.checked_pseudo = 0;
            var pseudo = $('#user_registration_username').val();
            if (pseudo == "") {
                $('#username-check').text('');
            }
            var pseudoRegex = /^[A-Z][a-z]{5,5}[0-9]{3,3}$/;
            if (pseudo != "") {
                if (pseudoRegex.test(pseudo)) {
                    $.ajax({
                        url: '/admin/add/user',
                        type: 'GET',
                        async: true,
                        success: function (datas, status) {
                            verif.checkUsername(datas);
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.log('Ajax request failed.');
                        }
                    });
                } else {
                    $('#username-check').text('L\'identifiant doit comporter 6 lettres et 3 chiffres la première lettre doit être en majuscule').css("color", "orange");
                }
            }
        }, 900);
    },
}

//au chargement de la page on fait appel a la méthode init qui permet de faire appel a d'autre méthodes
$(function () {
    verif.init();
})