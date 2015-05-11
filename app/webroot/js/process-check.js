var processID = $('#processID').val();
var intervalID = setInterval(recheck, 3000);
function recheck(){
	console.log(baseURL +  "upload/status/" + $('#processID').val());
	$.ajax(baseURL +  "upload/status/" + $('#processID').val(), {'complete':statusResult});
}
function statusResult(data){
	console.log(data);
	switch(data.responseText){
		case "complete":
			window.location = baseURL +  "r/" + $('#processID').val();
			break;		
		default:
			
			break;
	}	
}