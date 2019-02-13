
function ModeMakeyMakey () {
    this.lastInput = 0;
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
    var d = new Date();
    if ((event.key === 'Enter' || event.key === 'AudioVolumeUp') && this.lastInput + 700 <=  d.getTime()) {
      this.lastInput = d.getTime();

      console.log("NextEtape");
      ws.send(JSON.stringify({
        action: 'NextEtape',
        origin: 'Admin'
      }));
    }else if (event.key === 'AudioVolumeDown' && this.lastInput + 700 <=  d.getTime()) {
      this.lastInput = d.getTime();

      console.log("PreviousEtape");
      ws.send(JSON.stringify({
        action: 'PreviousEtape',
        origin: 'Admin'
      }));
    }
  }, false);

  this.SendReponse = function (x,equipe) {
    ws.send(JSON.stringify({
      action: 'RepondreQuestion',
      equipe: equipe,
      idreponse: x
    }));
    _body.innerHTML =
    '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;width: 100vw;margin:0px;">'+
      '<div class="col-md-12 ">'+
        '<div class="row  justify-content-center align-items-center">'+
          '<div class="col-md-12 ">'+
            '<p  class="text-center" style="color:'+partie.colortext+'; font-size: '+(partie.fontsize * 5.0)+'px;">Equipe '+equipe+' réponse : '+ x+'</p>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';
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

    document.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowUp') {
        modeJeux.SendReponse(0,1);
      }
      if (event.key === 'ArrowLeft') {
        modeJeux.SendReponse(1,1);
      }
      if (event.key === 'ArrowDown') {
        modeJeux.SendReponse(2,1);
      }
      if (event.key === 'ArrowRight') {
        modeJeux.SendReponse(3,1);
      }
      if (event.key === 'q') {
        modeJeux.SendReponse(0,2);
      }
      if (event.key === 'z') {
        modeJeux.SendReponse(1,2);
      }
      if (event.key === 'd') {
        modeJeux.SendReponse(2,2);
      }
      if (event.key === 's') {
        modeJeux.SendReponse(3,2);
      }
    }, false);
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
    }

    _body.innerHTML = rendu;

  }
}
