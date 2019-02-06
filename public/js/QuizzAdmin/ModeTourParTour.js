
function ModeTourParTour () {
  this.colorsDonuts = ['#007BFF','#FFC107','#28A745','#DC3545','#6C757D','#17A2B8'];
  this.DonutReponseData;
  this.donutOptions = {
    cutoutPercentage: 30,
    legend: {position:'bottom', padding:30, labels: {pointStyle:'circle', usePointStyle:true}}
  };

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
    }

  }


  //////////////////////////////////////////////////////////////////////////////////////////Fonction appeller par le client ////////////////////////////////////////////////////////////////////////////////////

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      console.log("NestEtape");
      ws.send(JSON.stringify({
        action: 'NextEtape',
        origin: "Admin"
      }));
    }
  }, false);

  ////////////////////////////////////////////////////////////////////////////////////////Fonction de rendu ///////////////////////////////////////////////////////////////////////////////////////////////

  this.AfficherFin = function (message){


    var rendu =

    '<div class="row justify-content-center" style="height: 100vh;">'+
      '<div class="col-md-5 ">'+
      '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
    '   <table  id="dataTable" width="100%" cellspacing="0">'+
    '     <thead>'+
            '<tr>'+
            '<th style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">n°</th>'+
            '<th style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">Nom</th>'+
            '<th style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">Score</th>'+
            '</tr>'+
    '     </thead>'+
    '     <tbody>';
    var nb = 1;
    for (let r of message.score) {
      if(nb <= 10){
        rendu +='<tr>'+
        '<td style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">'+nb+'</td>'+
        '<td style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">'+r.login+'</td>'+
        '<td style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">'+r.score+'</td>'+
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
    question = message.question;
    var rendu =
    '<div class="row justify-content-center"  style="min-height: 2vh;">'+
    '	<div class="col-md-12 " style="padding: 0px 0px 0px 0px;">'+
      '<div class="progress" id="bar-chrono" style="height: 100%; width:100%;">'+
        '<div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>'+
        '</div>'+
      '</div>'+
    '</div>';
    if(question.videoyoutube != null){
      rendu +=
      '<div class="row justify-content-center align-items-center" style="min-height: 48vh;">'+
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
      rendu +='<div class="col "style="padding: 10px 10px 10px 10px;min-height: '+hauteur+'vh;max-width: '+largeur+'vw;"><button  style="height: 100%;font-size: '+r.fontsize+'px;white-space: normal;" type="button" class="btn btn-block btn-primary  bg-'+colors[i]+'">'+r.libelle+'</button></div>';
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
    chronoStart(message.question.timer,JSON.stringify({
      action: 'NextEtape',
      origin: "Chrono"
    }));
  }

  this.AfficherReponse = function (message){
    var rendu =
    '<div class="row " style="height: 100vh;">'+
    '	<div class="col-md-6 ">'+
    '<div class="row " style="height: 100vh;">'+
    '	<div class="col-md-12 ">'+
    '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
    '<p style="font-size: '+question.fontsize+'px;color:'+partie.colortext+';">'+question.numero+' - '+question.libelle+' </p>'+
  '</div>'+
    '</div>';
    var i = 0;
    for (let r of message.reponsepossible) {
      rendu +='<div class="col col-md-12"style="padding: 0px 10px 0px 10px;"><button  style="height: 100%;font-size: '+(r.fontsize * 0.75)+'px;white-space: normal;" type="button" class="btn btn-block btn-primary  bg-'+colors[i]+'">'+r.libelle+'</button></div>';

      rendu +='<div class="w-100"style="height: 0vh;"></div>';
      i++;
    }

    rendu +='</div>'+
    '</div>'+
    '<div class="col-md-6 ">'+

    '<div class="row justify-content-center" style="height: 100vh;">'+
    '<div class="col-md-12 align-self-center">'+
    '<canvas id="DonutReponse"  ></canvas>'+
    '  </div>'+
    '<div class="col-md-5 ">'+
    '<div class="card" style="background-color:'+partie.colorfenetre+';">'+
    '   <table  id="dataTable" width="100%" cellspacing="0">'+
    '     <thead>'+
    '<tr>'+
    '<th style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">n°</th>'+
    '<th style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">Nom</th>'+
    '</tr>'+
    '     </thead>'+
    '     <tbody>';
    var nb = 1;
    for (let r of message.usertimer) {
      if(nb <= 5 && r.correct == 1){
        rendu +='<tr>'+
        '<td style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">'+nb+'</td>'+
        '<td style="font-size: '+partie.fontsize+'px;color:'+partie.colortext+';">'+r.login+'</td>'+
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


    this.DonutReponseData = {
      datasets: [
        {
          backgroundColor: this.colorsDonuts.slice(0,message.reponsepossible.length),
          borderWidth: 0,
          data: message.reponsepossiblevote
        }
      ]
    };

    var DonutReponse = document.getElementById("DonutReponse");
    if (DonutReponse) {
      new Chart(DonutReponse, {
        type: 'pie',
        data: this.DonutReponseData,
        options: this.donutOptions
      });
    }
  }
}
