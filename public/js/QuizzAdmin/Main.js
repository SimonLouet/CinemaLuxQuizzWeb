var _body = document.getElementById('ws-content-receiver');
var ws = new WebSocket('ws://' + wsIp+":"+wsPort);
var partie;
var question;
var colors = ["primary","warning","success","danger","secondary","info","light","muted"];
var modeJeux = null;

ws.onopen = function () {
  AfficherMenuLogin("");


  ws.send(JSON.stringify({
    action : 'InitPartie',
    idPartie : idPartie
  }));
};

ws.onclose = function() {
  Deconnexion("Problème avec le serveur.");
};

ws.onerror = function(event) {
  Deconnexion("Problème avec le serveur." + event);
};

ws.onmessage = function (event) {

  var message = JSON.parse(event.data);
  switch (message.action) {
    case 'LoginAdmin':
    LoginAdmin(message);
    break;
    case 'InitPartie':
    InitPartie(message);
    break;

    case 'RefreshCompteurUser':
    RefreshCompteurUser(message);
    break;

    case 'AfficherQRCode':
    AfficherQRCode(message);
    break;

    default:
    if(modeJeux != null){

      modeJeux.Action(message);
    }
  }
};

//////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le client ////////////////////////////////////////////////////////////////////////////////////


var SendLoginAdmin = function () {
  var mdp = document.getElementById('inputPassword').value;
  ws.send(JSON.stringify({
    action: 'LoginAdmin',
    mdp: mdp
  }));
};

////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le serveur ////////////////////////////////////////////////////////////////////////////////////

function LoginAdmin (message) {
  if(message.valide){
    var idPartie = document.getElementById('idPartie').value;
    ws.send(JSON.stringify({
      action : 'InitPartie',
      idPartie : idPartie
    }));
    _body.innerHTML = "Connexion réussi";
  }else{
    AfficherMenuLogin(message.erreur);
  }
};

function InitPartie (message) {


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

  var header = document.getElementById('header');
  var reg = /[ ,-]/g;

  header.innerHTML +=
'  <link rel="stylesheet"'+
      'href="https://fonts.googleapis.com/css?family='+partie.fontpolice.replace(reg,"+")+'">'+
  '<style>'+
    'body {'+
    '  font-family: "'+partie.fontpolice+'", serif;'+
  '  }'+
  '</style>'+
'  <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">';

  var background = document.getElementById('background');
  background.style.backgroundImage = "url('/QuizzLux/public/uploads/imageFond/"+partie.imagefondname+"')";
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

function RefreshCompteurUser(message){
  var compteur = document.getElementById('compteurjoueur');
  if(compteur){
    compteur.innerHTML = "Nombre de joueur : "+ message.nb;
  }
}

function AfficherQRCode(message){
  _body.innerHTML =
  '<div class="row justify-content-center" style="margin-top :5%;">'+

    '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
      '<h1  style="font-size: '+(partie.fontsize * 2.5)+'px;color:'+partie.colortitre+';" class="text-center">'+partie.nom+' </h1>'+
    '</div>'+
  '</div>'+
  '<div class="row justify-content-center " style="margin-top :5%;">'+
    '<div class="col-md-4">'+
        '<img class="rounded mx-auto d-block" src="/QuizzLux/public/uploads/QrCode/QRcode.png" alt="QR code de la partie">'+
    '</div>'+
    '<div class="col-md-4">'+
        '<img class="rounded mx-auto d-block" src="/QuizzLux/public/uploads/QrCode/QRcode.png" alt="QR code de la partie">'+
    '</div>'+
    '<div class="col-md-4">'+
        '<img class="rounded mx-auto d-block" src="/QuizzLux/public/uploads/QrCode/QRcode.png" alt="QR code de la partie">'+
    '</div>'+

  '</div>'+
  '<div class="row justify-content-center" style="margin-top :5%;">'+
    '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
      '<h2  id="compteurjoueur"  style="font-size: '+(partie.fontsize * 1.75)+'px;color:'+partie.colortitre+';" class="text-center">Nombre de joueur : 0</h2>'+
    '</div>'+
  '</div>';
}

function AfficherMenuLogin(erreur){
  var background = document.getElementById('background');
  background.style.backgroundColor = "#343a40";
  var rendu = "";
  if(erreur != ""){
    rendu +=
    '<div class="alert alert-danger">'+
    '<strong>Erreur !</strong>'+ erreur+
    '</div>';
  }

  rendu +=
  '<div class="card card-login mx-auto mt-5">'+
  '<div class="card-header">Nouvelle partie</div>'+
  '<div class="card-body">'+
  '<div class="alert alert-danger">'+
  '<strong>Attention !</strong>Si une partie a déjà été joué avec se quizz toutes les réponses seront supprimés.'+
  '</div>'+
  '<form onsubmit = "SendLoginAdmin(); return false;" >'+
  '<div class="form-group">'+
  '<div class="form-label-group">'+
  '<input type="password" id="inputPassword" class="form-control" placeholder="Password" required="required">'+
  '<label for="inputPassword">Mot de passe administrateur</label>'+
  '</div>'+
  '</div>'+
  '<button  id="btn-submit" class="btn btn-primary btn-block" >Commencer</button>'+
  '</form>'+
  '</div>'+
  '</div>';


  _body.innerHTML = rendu;
}
