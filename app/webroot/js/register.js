(function(window){
	$(window).ready(function(evt){
		$('input#UserUsername').blur(function(evt){
			if($(this).val().length >= 1){
				$.ajax(baseURL + "User/checkname/", {'method':'POST', 'data':{'data[User][username]':$(this).val()}, 
					'complete':function(data, success){
						console.log(data);
						var UsernameStatus = $('span#UsernameStatus');
						UsernameStatus.show();
						switch(parseInt(data.responseText)){
							case 1:
								UsernameStatus.removeClass("label-important").addClass("label-success").text("Available");
								$('#UserUsername').css('border-color', '#468847');								
								break;
							case 0:
								UsernameStatus.removeClass("label-success").addClass("label-important").text("Unavailable!");
								$('#UserUsername').css('border-color', '#b94a48');
								break;
						}
					}
				});
			}else{
				UsernameStatus.hide();
			}
		});
	});
}(window));
