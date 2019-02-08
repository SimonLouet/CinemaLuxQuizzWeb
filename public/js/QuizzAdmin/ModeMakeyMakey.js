
function ModeMakeyMakey () {
  this.bodyQuestion;
  this.reponseAfficher = 0;
  this.affichageReponse = null;
  this.Action = function (message) {
    switch (message.action) {
      case 'AfficherQuestion':

      this.AfficherQuestion(message);
      break;

      case 'AfficherReponse':
      //_body.innerHTML = event.data;
      this.AfficherReponse(message);
      break;

      case 'AfficherFin':
      //_body.innerHTML = event.data;
      this.AfficherFin(message);
      break;

      case 'AfficherReponsePossible':
      //_body.innerHTML = event.data;
      this.AfficherReponsePossible(message);
      break;


    }

  }


  //////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le client ////////////////////////////////////////////////////////////////////////////////////

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === 'Tab') {
      console.log("NextEtape");
      ws.send(JSON.stringify({
        action: 'NextEtape',
        origin: 'Admin'
      }));
    }
  }, false);

  ////////////////////////////////////////////////////////////////////////////////////////Fonction de rendu ///////////////////////////////////////////////////////////////////////////////////////////////

  this.AfficherFin = function (message){
    background.style.backgroundImage = "url('/QuizzLux/public/uploads/imageFond/"+partie.imagefondname+"')";

    var rendu =

    '<div class="row justify-content-center align-items-center" style="height: 100vh;">'+
      '<div class="col-md-5 ">'+
      '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
    '   <table  id="dataTable" width="100%" cellspacing="0">'+
    '     <tbody>';
    var nb = 1;
    for (let r of message.score) {
      if(nb <= 10){
        rendu +='<tr>'+
        '<td style="font-size: '+(partie.fontsize * 3.5)+'px;color:'+partie.colortext+';">'+r.login+'</td>'+
        '<td style="font-size: '+(partie.fontsize * 3.5)+'px;color:'+partie.colortext+';">'+r.score+'</td>'+
        '</tr>';
        nb ++;
      }
    }
    rendu+='</tbody>'+
    '</table>'+

    '</div>'+
    '</div>'+
    '  </div>'+
    '</div>';
    _body.innerHTML = rendu;

  }

  this.AfficherQuestion = function (message){
    this.reponseAfficher = 0;
    background.style.backgroundImage = "url('/QuizzLux/public/uploads/imageFond/"+partie.imagefondname+"')";
    question = message.question;
    var rendu ="";
    if(question.videoyoutube != null){
      rendu +=
      '<div class="row justify-content-center align-items-center" style="min-height: 50vh;">'+
        '<div class="col-md-7 ">'+
          '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
            '<p class="text-center" style="font-size: '+question.fontsize+'px;color:'+partie.colortext+';">'+question.numero+' - '+question.libelle+' </p>'+
          '</div>'+
        '</div>'+

        '<div class="col-md-5" style="height: 48vh;">'+
          '<iframe  width="98%" height="100%" src="https://www.youtube.com/embed/'+message.question.videoyoutube+'?&autoplay=1&loop=1&rel=0&showinfo=0&controls=0&iv_load_policy=3&playlist='+message.question.videoyoutube+'" frameborder="0" allow="modestbranding; accelerometer;showinfo; autoplay; loop; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'+
        '</div>'+
      '</div>';
    }else if(question.piecejointe != "" && question.piecejointe != null){

      var ext = question.piecejointe.split('.').pop();
      if(ext == "jpg" || ext == "jpeg" || ext == "png" || ext == "gif"){
        rendu +=
        '<div class="row justify-content-center align-items-center" style="min-height: 48vh;">'+
          '<div class="col-md-7 ">'+
            '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
              '<p class="text-center" style="font-size: '+question.fontsize+'px;color:'+partie.colortext+';">'+question.numero+' - '+question.libelle+' </p>'+
            '</div>'+
          '</div>'+

          '<div class="col-md-5 " >'+
                '<img class="rounded mx-auto d-block"    width="98%" height="auto" src="/QuizzLux/public/uploads/'+question.piecejointe+'"></img>'+
          '</div>'+
        '</div>';
      }else if(ext == "mp4"){
        rendu +=
        '<div class="row justify-content-center align-items-center" style="min-height: 48vh;">'+
          '<div class="col-md-7 ">'+
            '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
              '<p class="text-center" style="font-size: '+question.fontsize+'px;color:'+partie.colortext+';">'+question.numero+' - '+question.libelle+' </p>'+
            '</div>'+
          '</div>'+
          '<div class="col-md-5 " style="height: 48vh;">'+
            '<video width="100%" height="100%" autoplay loop >'+
            	'<source src="/QuizzLux/public/uploads/'+question.piecejointe+'" type="video/mp4">'+
            '</video>'+
          '</div>'+
        '</div>';
      }

    }else{
      rendu += '<div class="row justify-content-center align-items-center"style="min-height: 48vh;">'+
      '<div class="col-md-10 ">'+
        '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
          '<p class="text-center" style="font-size: '+question.fontsize+'px;color:'+partie.colortext+';">'+question.numero+' - '+question.libelle+' </p>'+
        '</div>'+
      '</div>'+
      '</div>';
    }

    rendu += '<div class="row " style="min-height: 50vh;max-height: 50vh;">';
    var i = 0;
    var hauteur;
    var largeur;
    if(message.reponsepossible.length <= 4 ){
      hauteur = (50 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 2));
      largeur = (100 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 2));;
    }else{
      hauteur = (50 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 3));

      largeur = (100 / ((message.reponsepossible.length + (message.reponsepossible.length %2)) / 2));;
    }
    for (let r of message.reponsepossible) {
      rendu +='<div id="reponse-'+(i+1)+'" class="col "style="visibility:hidden; padding: 10px 10px 10px 10px;min-height: '+hauteur+'vh;max-width: '+largeur+'vw;"><button  style="height: 100%;font-size: '+r.fontsize+'px;white-space: normal;" type="button" class="btn btn-block btn-primary  bg-'+colors[i]+'">'+r.libelle+'</button></div>';
      if(message.reponsepossible.length <= 4 ){
        if(i%2 == 1){
          rendu +='<div class="w-100"style="height: 0vh;"></div>';
        }
      }else{
        if(i-1%3 == 1){
          rendu +='<div class="w-100"style="height: 0vh;"></div>';
        }
      }
      i++;
    }
    rendu +=
    '</div>'+
    '</div>';

    _body.innerHTML = rendu;
  }

  this.AfficherReponse = function (message){
    if(this.affichageReponse != null){
      clearTimeout(this.affichageReponse);
      this.affichageReponse = null;
    }
    if(message.correct){
      var rendu =
      '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
        '<div class="col-md-12 ">'+
          '<div class="row  justify-content-center align-items-center">'+
            '<div class="col-md-12 ">'+
              '<p class="text-center" style="font-size: '+(partie.fontsize * 2.0)+'px;color:white;">'+message.utilisateurlogin+'</p>'+
            '</div>'+
            '<i class="fa fa-check" style="font-size:200px;color:white;"></i>'+
            '<div class="col-md-12 ">'+
              '<p class="text-center" style="font-size: '+(partie.fontsize * 2.0)+'px;color:white;">'+message.reponselibelle+'</p>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "green";
      background.style.backgroundImage = "";
    }else{


      var rendu =
      '<div class="row  justify-content-center align-items-center" style="min-height: 100vh;">'+
        '<div class="col-md-12 ">'+
          '<div class="row  justify-content-center align-items-center">'+
            '<div class="col-md-12 ">'+
              '<p class="text-center" style="font-size: '+(partie.fontsize * 2.0)+'px;color:white;">'+message.utilisateurlogin+'</p>'+
            '</div>'+
            '<i class="fa fa-times" style="font-size:200px;color:white;"></i>'+
            '<div class="col-md-12 ">'+
              '<p class="text-center" style="font-size: '+(partie.fontsize * 2.0)+'px;color:white;">'+message.reponselibelle+'</p>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>';
      var background = document.getElementById('background');
      background.style.backgroundColor = "red";
      background.style.backgroundImage = "";

      this.affichageReponse = setTimeout(function(){this.affichageReponse = null; _body.innerHTML = modeJeux.bodyQuestion;background.style.backgroundImage = "url('/QuizzLux/public/uploads/imageFond/"+partie.imagefondname+"')";}, 4000);
    }
    _body.innerHTML = rendu;
  }

  this.AfficherReponsePossible = function (message){
    this.reponseAfficher += 1;
    var reponse = document.getElementById('reponse-'+ this.reponseAfficher);
    reponse.style.visibility = "visible";

    modeJeux.bodyQuestion = _body.innerHTML;
  }
}
