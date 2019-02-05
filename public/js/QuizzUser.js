
function ModeTourParTour () {

  this.Action = function (message) {
    switch (message.action) {

      case 'AfficherPresentation':
      this.AfficherPresentation(message);
      break;

      case 'AfficherFin':

      this.AfficherFin(message);
      break;

      case 'AfficherQuestion':

      this.AfficherQuestion(message);
      break;


      case 'AfficherResultat':
      //_body.innerHTML = event.data;
      this.AfficherResultat(message);
      break;

    }

  }

  this.SendReponse = function (x) {
    ws.send(JSON.stringify({
      action: 'RepondreQuestion',
      idreponse: x
    }));
    this.AfficherAttente();
    return false;
  };

  this.AfficherFin = function (message){
    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;
    background.style.backgroundImage = "";

    _body.innerHTML =
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
    '<h1  style="font-size:50px;color:'+partie.colortext+';">Votre score : '+message.score+'</h1>'+
    '</div>';
  }

  this.AfficherPresentation = function (message){
    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;
    background.style.backgroundImage = "";

    _body.innerHTML =
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
    '<i class="fas fa-circle-notch fa-spin" style="font-size:200px;color:white;"></i>'+
    '</div>';
  }

  this.AfficherQuestion = function (message){

    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;

    var haut = (window.innerHeight);
    var rendu ='<div class="row justify-content-center "style="min-height: 5vh;">'+
    '	<div class="col-md-12 ">'+
    '<div class="progress" id="bar-chrono">'+
    '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
    '</div>'+
    '</div>'+
    '</div>'+
    '<div class="row " style="min-height: 95vh;">';
    var i = 0;
    var hauteur = (100 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 2));
    for (let r of message.reponsepossible) {
      rendu +='<div  class="col "style="padding: 10px 10px 10px 10px;min-height: '+hauteur+'vh;"><button id="reponse-'+i+'" style="height: 100%;" type="button"  class="btn btn-block btn-primary bg-'+colors[i]+'"></button></div>';


      if(i%2 == 1){
        rendu +='<div class="w-100"style="height: 0vh;"></div>';
      }
      i++;
    }
    rendu +=
    '</div>';

    _body.innerHTML = rendu;
    chronoStart(message.question.timer,null);
    i = 0;
    for (let r of message.reponsepossible) {
      var reponseButton = document.getElementById('reponse-'+i);
      reponseButton.setAttribute("onclick","modeJeux.SendReponse("+i+");");
      i++;
    }
  }

  this.AfficherResultat = function (message){
    if(message.correct == 1){
      var rendu =
      '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
      '<i class="fas fa-check" style="font-size:200px;color:white;">'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "green";
    }else{
      var rendu =
      '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
      '<i class="fas fa-times" style="font-size:200px;color:white;">'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "red";
    }
    _body.innerHTML = rendu;
  }

  this.AfficherAttente = function (){
    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;

    var rendu =
    '<div class="row justify-content-center "style="min-height: 5vh;">'+
    '	<div class="col-md-12 ">'+
    '<div class="progress" id="bar-chrono">'+
    '<div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
    '</div>'+
    '</div>'+
    '</div>'+
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
    '<i class="fa fa-circle-o-notch fa-spin" style="font-size:200px;color:white;"></i>'+
    '</div>';


    _body.innerHTML = rendu;
  }
}

function ModeMakeyMakey () {
  this.bodyquestion;
  this.timeReponse;


  this.Action = function (message) {
    switch (message.action) {

      case 'AfficherPresentation':
      this.AfficherPresentation(message);
      break;

      case 'AfficherFin':

      this.AfficherFin(message);
      break;

      case 'AfficherQuestion':

      this.AfficherQuestion(message);
      break;


      case 'AfficherResultat':
      this.AfficherResultat(message);
      break;

    }

  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      console.log("NextEtape");
      ws.send(JSON.stringify({
        action: 'NextEtape'
      }));
    }
  }, false);

  this.SendReponse = function (x) {
    ws.send(JSON.stringify({
      action: 'RepondreQuestion',
      idreponse: x
    }));
    return false;
  };

  this.AfficherFin = function (message){
    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;
    background.style.backgroundImage = "";

    _body.innerHTML =
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
    '<h1  style="font-size:50px;color:'+partie.colortext+';">Votre score : '+message.score+'</h1>'+
    '</div>';
  }

  this.AfficherPresentation = function (message){
    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;
    background.style.backgroundImage = "";

    _body.innerHTML =
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
    '<i class="fas fa-circle-notch fa-spin" style="font-size:200px;color:white;"></i>'+
    '</div>';
  }

  this.AfficherQuestion = function (message){

    var background = document.getElementById('background');
    background.style.backgroundColor = partie.colorfenetre;

    var haut = (window.innerHeight);
    var rendu =
    '<div class="row " style="min-height: 100vh;">';
    var i = 0;
    var hauteur = (100 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 2));
    for (let r of message.reponsepossible) {
      rendu +='<div  class="col "style="padding: 10px 10px 10px 10px;min-height: '+hauteur+'vh;"><button id="reponse-'+i+'" style="height: 100%;" type="button"  class="btn btn-block btn-primary bg-'+colors[i]+'"></button></div>';


      if(i%2 == 1){
        rendu +='<div class="w-100"style="height: 0vh;"></div>';
      }
      i++;
    }
    rendu +=
    '</div>';

    _body.innerHTML = rendu;
    i = 0;
    for (let r of message.reponsepossible) {
      var reponseButton = document.getElementById('reponse-'+i);
      reponseButton.setAttribute("onclick","modeJeux.SendReponse("+i+");");

      i++;
    }
    document.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowUp') {
        modeJeux.SendReponse(0);
      }
      if (event.key === 'ArrowLeft') {
        modeJeux.SendReponse(1);
      }
      if (event.key === 'ArrowDown') {
        modeJeux.SendReponse(2);
      }
      if (event.key === 'ArrowRight') {
        modeJeux.SendReponse(3);
      }
    }, false);
  }

  this.AfficherResultat = function (message){
    modeJeux.bodyquestion = ""+_body.innerHTML;
    console.log(modeJeux.bodyquestion);
    if(message.correct == 1){
      var rendu =

      '<div class="row  justify-content-center align-items-center" style="min-height: 98vh;">'+
      ' <i class="fa fa-check" style="font-size:200px;color:white;">'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "green";
      _body.innerHTML = rendu;
    }else{
      var rendu =
      '<div class="row justify-content-center"  style="min-height: 2vh;">'+
      '	<div class="col-md-12 " style="padding: 0px 0px 0px 0px;">'+
        '<div class="progress" id="bar-chrono" style="height: 100%; width:100%;">'+
          '<div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>'+
          '</div>'+
        '</div>'+
      '</div>'+
      '<div class="row  justify-content-center align-items-center" style="min-height: 98vh;">'+
      '<i class="fas fa-times" style="font-size:200px;color:white;">'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "red";
      chronoStart(4000,"");
      setTimeout(function(){ _body.innerHTML = modeJeux.bodyquestion;var background = document.getElementById('background');background.style.backgroundColor = partie.colorfenetre;}, 4000);
    }

    _body.innerHTML = rendu;

  }
}






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
