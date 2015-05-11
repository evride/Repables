(function(window){
	var currentImage = 0;
	var t = 0;
	var view3dLoaded = false;
	var view3dLoadCount = -1;
	var view3dScripts = ['view3d/three.min.js', 'view3d/STLLoader.js', 'view3d/TrackballControls.js', 'view3d/view3d.js'];
	var curr3dModelID = 0;
	var viewer;
	var thumbsTotalWidth = 0;
	var thumbsX = 0;
	$(window).ready(function(){
		$('#ItemCommentForm').submit(function(evt){
			evt.preventDefault();
			var formData = {};		
			$.each($('#ItemCommentForm').serializeArray(), function(i, field) {
				formData[field.name] = field.value;
			});
			$.ajax($('#ItemCommentForm').attr('action'),{'data':formData, 'type':"POST", 'dataType':'json', complete:itemCommentResult});
			return false;
		});
		$('.DeleteComment a').click(function(evt){
			evt.preventDefault();
			$.ajax($(this).attr('href'),{'dataType':'text', complete:deleteCommentResult});
			return false;
		});
		
		if($('div.GalleryImage').length >= 2){
			t = setTimeout(nextImage, 6000);
			$('div.ThumbnailImage').click(function(evt){
				clearTimeout(t);
				t = setTimeout(nextImage, 6000);
				var selectedThumb = $(evt.currentTarget);
				var selectedIndex = selectedThumb.index();
				currentImage = selectedIndex;			
				showImage(currentImage);
			});
		}
		if(Modernizr.webgl){
			$('div.GalleryImage:eq(0) a.View3dBtn').show();
		}
		thumbsTotalWidth = $('.ThumbnailCont').width();
		$('#DownloadDropdown .btn-dropdown').click(function(evt){
			$('.downloadSection').toggle();
			$(this).children('.caret').toggleClass('caret-up');
		});
		
		$('a.ThumbnailLeftArrow').click(function(evt){
			if(thumbsX <= -550){
				thumbsX += 550; 
				$('.ThumbnailCont').css('margin-left', thumbsX + 'px');
			}
			evt.preventDefault();
		});
		$('a.ThumbnailRightArrow').click(function(evt){
			console.log(thumbsX, thumbsTotalWidth);
			if(thumbsX - 550 >= -thumbsTotalWidth){
				thumbsX -= 550; 
				$('.ThumbnailCont').css('margin-left', thumbsX + 'px');
			}
			evt.preventDefault();
		});
		$('a.rateBtn').click(vote);
		$('.FlagInappropriate a').click(flag);
	});
	function nextImage(){
		var allImages = $('div.GalleryImage');
		currentImage++;
		if(currentImage >= allImages.length){
			currentImage = 0;
		}
		showImage(currentImage);
		t = setTimeout(nextImage, 6000);
	}
	function showImage(num){
		$('#View3DContainer').css('display','none');
		
		$('div.GalleryImage').hide();		
		$('a.View3dBtn').hide();
		
		var gi = $('div.GalleryImage:eq(' + num + ')');
		gi.show();
		if(Modernizr.webgl){
			gi.find('a.View3dBtn').show();
		}
	}
	function deleteCommentResult(data){
		data = $.parseJSON(data.responseText);
		if(data.status == "success"){
			if(data.commentID){
				$('#CommentNum' + data.commentID).detach();
			}
		}
	}
	function itemCommentResult(data, herro){
		
		
		var data = $.parseJSON(data.responseText);
		var htmlStr = '<div class="ItemComment" id="CommentNum' + data.commentID + '"><div class="CommentUserThumbnail"><img src="' + data.profileImage + '"></div><div class="CommentTextCont"><div class="CommentUsername"><a href="' + data.profileURL + '">' + data.username + '</a> - <span class="CommentDate" title="' + data.date + '">Just now</span> - <span class="DeleteComment"><a href="' + data.deleteCommentURL + '">Delete</a></span></div><div class="CommentBody">' + data.comment + '</div></div><div class="clearBoth"></div></div>';
		
		
		$('#ItemComments').prepend(htmlStr);
		$('#CommentText').val("");
		$('#CommentNum' + data.commentID + ' .DeleteComment a').click(function(evt){
			evt.preventDefault();
			$.ajax($(this).attr('href'),{'dataType':'text', complete:deleteCommentResult});
			return false;
		});
	}	
	function view3d(id){
		curr3dModelID = id;
		clearTimeout(t);
		$('#View3DContainer').show();
		$('#ItemDisplay').hide();
		loadNextScript();		
	}
	function loadNextScript(evt){
		$('#View3DContainer div.progress div.bar').css('width', (10 + 90 * ((view3dLoadCount + 1) / (view3dScripts.length))) + "%");
		
		if(view3dLoadCount == view3dScripts.length-1){
			viewModel('/Items/view3d/' + curr3dModelID);			
			$('#View3DContainer button.close').click(viewerClosed);
		}else{
			view3dLoadCount++;
			var addScript = $('<script></script>').appendTo($('head')).attr('type', 'text/javascript').attr('src', baseURL + 'js/' + view3dScripts[view3dLoadCount]).load( loadNextScript );
			
			//$.ajax({url: baseURL + 'js/' + view3dScripts[view3dLoadCount], dataType: "script", cache: true, success: loadNextScript, error:function(evt){console.log("error", evt);}});
		}			
	}
	function viewerClosed(evt){
		close3dView();
		$('#View3DContainer').hide();		
		$('#ItemDisplay').show();		
		if($('div.GalleryImage').length >= 2){
			t = setTimeout(nextImage, 6000);
		}
	}
	function vote(evt){
		
		var itemContent = $('#ItemContent');
		var itemID = itemContent.data('item');
		var revisionID = itemContent.data('revision');
		var point = 0;
		
		var clickedBtn = $(evt.currentTarget);
		if(clickedBtn.hasClass('rate-up')){
			if(!$('.rate-up').hasClass("rate-up-selected")){
				point = 1;
			}
			$('.rate-up').toggleClass("rate-up-selected");
			$('.rate-down').removeClass("rate-down-selected");
		}else if(clickedBtn.hasClass('rate-down')){
			if(!$('.rate-down').hasClass("rate-down-selected")){
				point = -1;
			}
			$('.rate-down').toggleClass("rate-down-selected");
			$('.rate-up').removeClass("rate-up-selected");
		}
		$.ajax(baseURL + "Items/rate/" + itemID + "/" + revisionID, {data:{ 'PointValue':point}, type:"POST", complete:voteResponse});
		evt.preventDefault();
	}
	function voteResponse(req, data){
		data = $.parseJSON(req.responseText);
		if(data.status == "login_required"){
			var itemContent = $('#ItemContent');
			var itemID = itemContent.data('item');
			var revisionID = itemContent.data('revision');
			window.location = baseURL + 'login/r/' + itemID + "/" + revisionID;
		}
	}
	function flag(evt){
		console.log(evt);
		var itemContent = $('#ItemContent');
		var itemID = itemContent.data('item');
		var revisionID = itemContent.data('revision');
		
		$.ajax(baseURL + "Items/flag/" + itemID + "/" + revisionID, {type:"POST", complete:flagged});
		evt.preventDefault();
	}
	function flagged(req, data){
		console.log(req, data);
		$('.FlagInappropriate a').attr('href', '#').css('color', '#FF0000').children('span').text('Flagged');
	}
	window.view3d = view3d;
	
}(window));