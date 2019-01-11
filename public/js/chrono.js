var lienDirectorie
var start = 0
var end = 0
var diff = 0
var timerID = 0
var chronoIsStart = false
var msecTimer = 0

function chrono(){
	start = new Date()
	diff = end - start
	diff = new Date(diff)

  if(start.getTime() > end.getTime()){
    window.location.replace(lienDirectorie)
  }else{

		var msec = diff.getMilliseconds()
		var sec = diff.getSeconds()
		var min = diff.getMinutes()
		document.getElementById("chronotime").innerHTML =   min + ":" + sec + ":" + msec
		timerID = setTimeout("chrono()", 10)
	}
}

function chronoInit(msec,lien){
	lienDirectorie = lien
	msecTimer = msec

	start = new Date()
	end = new Date(start.getTime()+ msecTimer)

	diff = end - start
	diff = new Date(diff)
	var msec = diff.getMilliseconds()
	var sec = diff.getSeconds()
	var min = diff.getMinutes()
	document.getElementById("chronotime").innerHTML =   min + ":" + sec + ":" + msec
}

function chronoStart(){
	chronoIsStart = true;
	start = new Date()
	end = new Date(start.getTime()+ msecTimer)
	chrono()
}
