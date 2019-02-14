
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

      case 'AfficherTelecommande':

      this.AfficherTelecommande(message);
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

  this.SendNextEtape = function (x) {
    ws.send(JSON.stringify({
      action: 'NextEtape',
      origin: "Admin"
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
      '<div class="col-md-12 ">'+
        '<div class="row  justify-content-center align-items-center">'+
          '<i class="fas fa-circle-notch fa-spin" style="font-size:200px;color:'+partie.colortext+';"></i>'+
          '<div class="col-md-12 ">'+
            '<p class="text-center" style="font-size: '+(partie.fontsize * 1.0)+'px;color:'+partie.colortext+';">En attente des autres joueur...</p>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';

    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
      '<i class="fas fa-circle-notch fa-spin" style="font-size:200px;color:white;"></i>'+

      '<div class="col-md-12 ">'+
        '<p class="text-center" style="font-size: '+(partie.fontsize * 2.0)+'px;color:white;">'+message.reponselibelle+'</p>'+
      '</div>'+
    '</div>';
  }


  this.AfficherTelecommande = function (message){
    var rendu =
    '<div class="row " style="min-height: 100vh;">'+
    '<div  class="col "style="padding: 10px 10px 10px 10px;min-height: 100vh;"><button id="nextEtape" style="height: 100%;" type="button"  class="btn btn-block btn-primary">Suivant</button></div>'+
    '</div>';

    _body.innerHTML = rendu;
    var nextEtapeButton = document.getElementById('nextEtape');
    nextEtapeButton.setAttribute("onclick","modeJeux.SendNextEtape();");
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
