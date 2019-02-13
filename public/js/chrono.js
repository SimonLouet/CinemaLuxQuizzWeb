var start = 0
var end = 0
var diff = 0
var tempEnd = 0
var timerID = 0
var json = null

function chrono(){
	end = new Date();
	diff = end - start;

	var pourcent = (diff / tempEnd) * 100;
	var barchrono = document.getElementById("bar-chrono");
	if(barchrono != null){
		barchrono.innerHTML = '<div class="progress-bar" role="progressbar" style="width: '+pourcent+'%" aria-valuenow="'+pourcent+'" aria-valuemin="0" aria-valuemax="100">'+Math.ceil((tempEnd-diff) / 1000)+'s</div>';
	}
	var compteurchrono = document.getElementById("compteur-chrono");
	if(compteurchrono != null){
		compteurchrono.innerHTML = Math.ceil((tempEnd-diff) / 1000);
	}
	if(pourcent < 100){
		timerID = setTimeout("chrono()", 10)
	}else{
		if(compteurchrono != null){
			compteurchrono.innerHTML = "GO";
		}

		if(json != null){
			ws.send(json);
		}

	}
}
function chronoStart(temp,sendjson){
	tempEnd = temp;
	json = sendjson;
	start = new Date()
	chrono()
}
