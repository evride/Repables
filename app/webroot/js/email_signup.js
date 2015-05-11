$(window).ready(function(){
	$('#emailForm').submit(function(evt){
		console.log(evt);
		
		var email = $('#email').val();
		if(email.match(/^[-0-9A-Za-z!#$%&'*+\/=?^_`{|}~.]+@[-0-9A-Za-z!#$%&'*+\/=?^_`{|}~.]+/)){		
			$('input[type="submit"]').attr('disabled','disabled');
			$.ajax({ type:'POST', dataType: "text", url:$('#emailForm').attr('action'), data:{'email':email, 'j':true}, success:dataReturn});
			$('#email').css('border:1px solid #CCCCCC');
		}else{
			$('#email').css('border:1px solid #DD0000');
		}
		
		return false;
	});
});
function dataReturn(data){
	$('input[type="submit"]').removeAttr('disabled');
	console.log(data);
	
	
	$('#formDiv').hide();
	if(data == "success"){
		$('#responseMessage').html("<p class=\"text-success\">Thank you! We'll email you when it's ready.</p>");
	}else if(data == "exists"){
		$('#responseMessage').html("<p class=\"text-info\">The email submitted already exists in our database. Thank you!</p>");
	}else if(data == "error"){
		$('#responseMessage').html("<p class=\"text-error\">An error was experienced when entering the email address.</p>");
	}
	setTimeout(function(){reset();}, 5000);
}
function reset(){
	$('#email').val("");
	$('#responseMessage').html("");
	$('#formDiv').show();
	
}