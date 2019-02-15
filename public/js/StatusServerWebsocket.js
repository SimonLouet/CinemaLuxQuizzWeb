


var serverStatus = document.getElementById('StatusServeur');
var ws;
var connexionValide = false;


OuvertureConnexion = function() {
  serverStatus.innerHTML =
  '<span>Status serveur jeux :</span>'+
  '<i class="fas fa-circle fa-1.5x fa-fw " style="color:green"></i>'+
  '<button id="EndServeur"  type="button"  class="btn btn-block btn-primary ">Deconnecter le serveur</button>';
  var endServeurButton = document.getElementById('EndServeur');
  endServeurButton.setAttribute("onclick","DeconnecterServeur();");
  connexionValide = true;
};

FermetureConnexion = function() {
  serverStatus.innerHTML =
  '<span>Status serveur jeux :</span>'+
  '<i  class="fas fa-circle fa-1.5x fa-fw " style="color:red"></i>'+
  '<button id="StartServeur"  type="button"  class="btn btn-block btn-primary ">Lancer le serveur</button>';

  var startServeurButton = document.getElementById('StartServeur');
  startServeurButton.setAttribute("onclick","StartServeur();");
  connexionValide = false;
};









StartServeur = function (message){
  httpRequest = new XMLHttpRequest();
  httpRequest.open('GET', "http://"+wsIp+"/QuizzLux/public/PlayAdmin/WebSocket", true);
  httpRequest.send();
  return false;
}


DeconnecterServeur = function (message){
  ws.send(JSON.stringify({
    action: 'DeconnexionServeur',
    mdp : "5qsef14qf68qsfe518qs45qs8gf4qg6sr6g"
  }));
  return false;
}



TentativeConnexion = function (message){
  if(!connexionValide){
    ws = new WebSocket('ws://' + wsIp+":"+wsPort);
    ws.onclose =FermetureConnexion;
    ws.onopen =OuvertureConnexion;

  }
  setTimeout(TentativeConnexion, 3000);
  return false;
}

TentativeConnexion();
