/*https://zguyun.com/blog/how-to-disable-back-button-in-browser-using-javascript/
 Ca nous permet de desactiver le bouton de retour du navigateur et donc d'eviter certains bugs , pas sur qu'on en ai besoin 
 si nos routes sont sécurisées.
 */

window.onload = function () {
    disableBackBtn();
}

function disableBackBtn() {
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
}