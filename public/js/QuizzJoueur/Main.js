
var background = document.getElementById('background');
background.style.backgroundImage = "";

var _body = document.getElementById('ws-content-receiver');

var ws = new WebSocket('ws://' +  wsIp+":"+wsPort);
var partie;
var question;
var colors = ["primary","warning","success","danger","secondary","info","light","muted"];
var modeJeux = null;


ws.onopen = function () {
  ws.send(JSON.stringify({
    action: 'Connexion'
  }));
  _body.innerHTML =
  '<div class="alert alert-danger">'+
  '<strong>Info !</strong>En attente du serveur ...'
  '</div>';
};

ws.onclose = function() {
  Deconnexion("Problème avec le serveur.");
};

ws.onerror = function() {
  Deconnexion("Problème avec le serveur.");
};

ws.onmessage = function (event) {

  var message = JSON.parse(event.data);
  switch (message.action) {
    case 'LoginUser':
    LoginUser(message);
    break;

    case 'AfficherFirstConnexion':
    AfficherFirstConnexion(message);
    break;

    case 'AfficherMenuLogin':
    AfficherMenuLogin("");
    break;

    case 'TentativeConnexion':
    TentativeConnexion(message);
    break;

    default:
    if(modeJeux != null){

      modeJeux.Action(message);
    }
    break;
  }
};



//////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le client ////////////////////////////////////////////////////////////////////////////////////




var SendLoginUser = function () {
  var mail = document.getElementById('inputEmail').value;
  var mdp = document.getElementById('inputPassword').value;
  ws.send(JSON.stringify({
    action: 'LoginUser',
    mail: mail,
    mdp: mdp
  }));
};

var SendFirstConnexion = function () {
  var pseudonyme = document.getElementById('inputPseudonyme').value;
  var mail = document.getElementById('inputEmail').value;
  var mdp = document.getElementById('inputPassword').value;
  var confirmmdp = document.getElementById('confirmPassword').value;
  if(confirmmdp == mdp){
    ws.send(JSON.stringify({
      action: 'FirstConnexion',
      pseudonyme: pseudonyme,
      mail:mail,
      mdp:mdp
    }));
  }
};



////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le serveur ////////////////////////////////////////////////////////////////////////////////////

function TentativeConnexion (message) {
  ws.send(JSON.stringify({
    action: 'Connexion'
  }));
};

function LoginUser (message) {
  if(message.valide){
    _body.innerHTML = "Connexion réussi";

    partie = message.partie;

    switch (partie.modejeux) {
      case 'TourParTour':
      modeJeux = new ModeTourParTour();
      break;

      case 'MakeyMakey':
      modeJeux = new ModeMakeyMakey();
      break;

      default:
      modeJeux = new ModeTourParTour();
    }
  }else{
    AfficherMenuLogin(message.erreur);
  }
};

function Deconnexion (message) {
  if(message != ""){
    _body.innerHTML =
    '<div class="alert alert-danger">'+
    '<strong>Erreur !</strong>'+ message+
    '</div>';
  }
};


////////////////////////////////////////////////////////////////////////////////////////Fonction de rendu ///////////////////////////////////////////////////////////////////////////////////////////////



function AfficherMenuLogin(erreur){
  var background = document.getElementById('background');
  background.style.backgroundColor = "#343a40";
  var rendu ="";
  if(erreur != ""){
    rendu +=
    '<div class="alert alert-danger">'+
    '<strong>Erreur !</strong>'+ erreur+
    '</div>';
  }

  rendu +=
  '<div class="card card-login mx-auto mt-5">'+
  '<div class="card-header">Connexion</div>'+
  '<div class="card-body">'+
  '<form onsubmit = "SendLoginUser(); return false;" >'+
  '<div class="form-group">'+
  '<div class="form-label-group">'+
  '<input type="email" id="inputEmail" class="form-control" placeholder="Email address" required="required" autofocus="autofocus">'+
  '<label for="inputEmail">E-mail</label>'+
  '</div>'+
  '</div>'+
  '<div class="form-group">'+
  '<div class="form-label-group">'+
  '<input type="password" id="inputPassword" class="form-control" placeholder="Password" required="required">'+
  '<label for="inputPassword">Mot de passe</label>'+
  '</div>'+
  '</div>'+
  '<button  id="btn-submit" class="btn btn-primary btn-block" >Connexion</button>'+
  '</form>'+
  '</div>'+
  '</div>';

  _body.innerHTML = rendu;
}

function AfficherFirstConnexion(message){
  var background = document.getElementById('background');
  background.style.backgroundColor = "#343a40";

  _body.innerHTML = '<div class="card card-register mx-auto mt-5">'+
  '<div class="card-header">Première connexion</div>'+
  '<div class="card-body">'+
  '<form onsubmit = "SendFirstConnexion(); return false;">'+
  '<div class="form-group">'+
  '<div class="form-label-group">'+
  '<input type="text" id="inputPseudonyme" class="form-control" placeholder="First name" required="required" autofocus="autofocus">'+
  '<label for="inputPseudonyme">Pseudonyme</label>'+
  '</div>'+
  '</div>'+
  '<div class="form-group">'+
  '<div class="form-label-group">'+
  '<input type="email" id="inputEmail" value="'+message.mail+'" class="form-control" placeholder="Email address" required="required">'+
  '<label for="inputEmail">E-mail </label>'+
  '</div>'+
  '</div>'+
  '<div class="form-group">'+
  '<div class="form-row">'+
  '<div class="col-md-6">'+
  '<div class="form-label-group">'+
  '<input type="password" id="inputPassword" class="form-control" placeholder="Password" required="required">'+
  '<label for="inputPassword">Mot de passe</label>'+
  '</div>'+
  '</div>'+
  '<div class="col-md-6">'+
  '<div class="form-label-group">'+
  '  <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm password" required="required">'+
  '  <label for="confirmPassword">Confirmer le mot de passe</label>'+
  '</div>'+
  '</div>'+
  '</div>'+
  '</div>'+
  '<button  id="btn-submit" class="btn btn-primary btn-block" >Connexion</button>'+
  '</form>'+
  '</div>'+
  '</div>';
}
