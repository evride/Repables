(function(window){
	function file_add(e, data){
		var fileName = data.files[0].name;
		
		var extStart = fileName.lastIndexOf('.');
		var fileExt = "";
		if(extStart >= 1){
			var fileExt = fileName.substr(extStart + 1);
			if(e.currentTarget.id == "UserImage"){
				if(!(fileExt.toLowerCase() == "jpg" || fileExt.toLowerCase() == "jpeg" || fileExt.toLowerCase() == "gif" || fileExt.toLowerCase() == "png" || fileExt.toLowerCase() == "bmp")){
					return false;
				}
			}
		}
		
		console.log('formData' ,data.formData);
		data.submit();
	}
	
	$('#UserSettingsForm').ready(function(){
		var thumbEditor = $('#ThumbEditorContainer').get(0);
		thumbEditor.onselectstart = thumbEditor.onmousedown = function(e){ if(!e){e=window.event;} if(e.preventDefault){e.preventDefault();}return false;};
		
		if( window.FileReader && Modernizr.draganddrop == true){
			$('.Dropzone').css({'border':'4px dashed #CCC', 'min-height':'100px'});
			$('.DropzoneText').html('<strong>Drag and Drop</strong> an image file here or:');
			//$('.Dropzone div').text('No files have been added yet. Drop them here.');
		}
		$('#UserSettingsForm').fileupload({
			'url':baseURL + 'User/image',
			'drop':file_add,
			'add':file_add,
			'done':function(e, data){
				console.log(data, data.result);
				uploadData = $.parseJSON(data.result);
				if(uploadData.status == "success"){
					$('#imageID').val(uploadData.id);
					console.log(uploadData.image);
					var img = $('<img>').attr('src', uploadData.image);
					img.load(function(evt){
						$('#ThumbEditorContainer').css('display', 'block');
						
						console.log(this.width, this.height);
						
						
						var scale = 1;
						var scaledWidth = this.width;
						var scaledHeight = this.height;
						if(this.width > 537){
							scale = 537 / scaledWidth;
							scaledHeight = scale * scaledHeight;
							scaledWidth = 537;
						}
						
						
						var maxThumbSize = scaledWidth >= scaledHeight?scaledHeight-2:scaledWidth-2;
						
						var minThumbSize = maxThumbSize < 40?maxThumbSize:40;
						var startingSize = maxThumbSize < 200?maxThumbSize:200;
						
						var selectorX, selectorY;
						
						if(minThumbSize == maxThumbSize){
							$('#ThumbnailSizeSlider').hide();
						}else{
							$('#ThumbnailSizeSlider').show();
						}
						
						var thumbnailSize = startingSize;
						$('#ThumbnailSelector').css({'width':thumbnailSize + "px", 'height':thumbnailSize+"px"});
						function windowMouseMove(evt){
							console.log(evt);
							var offset = $('#FullProfileImage').offset();
							
							selectorX = evt.pageX - offset.left - thumbnailSize / 2;
							selectorY = evt.pageY - offset.top - thumbnailSize / 2;
							selectorX = selectorX<-1?-1:(selectorX + thumbnailSize>=scaledWidth-1?scaledWidth-thumbnailSize-1:selectorX);
							selectorY = selectorY<-1?-1:(selectorY + thumbnailSize>=scaledHeight-1?scaledHeight-thumbnailSize-1:selectorY);
							
							$('#ThumbnailSelector').css({'margin-left':selectorX + "px", 'margin-top':selectorY + "px"});
							
							console.log($('#ThumbnailSelector').offset(), evt.pageX, evt.pageY);
						}
						function windowMouseUp(evt){							
							$(this).unbind('mousemove', windowMouseMove );
							$(this).unbind('mouseup', windowMouseUp);
							updateFormValues();
						}
							
						$('#FullProfileImage, #ThumbnailSelector').mousedown(function(evt){
						    console.log(minThumbSize, maxThumbSize, scaledWidth, scaledHeight);
							$(window.document).mousemove(windowMouseMove);
							$(window.document).mouseup(windowMouseUp);
						});
						
						$('#FullProfileImage').empty();
						$('#FullProfileImage').append(img);
						
						$("#ThumbnailSizeSlider").slider({
							'min':minThumbSize,
							'max':maxThumbSize,
							'value':startingSize,
							"orientation":"vertical",
							"change":function(evt){
								thumbnailSize = $(this).slider("value");
								if(selectorX + thumbnailSize - 1 > scaledWidth){
									selectorX = scaledWidth - thumbnailSize-1;
								}
								if(selectorY + thumbnailSize - 1 > scaledHeight){
									selectorY = scaledHeight - thumbnailSize-1;
								}
								$('#ThumbnailSelector').css({'margin-left':selectorX + "px", 'margin-top':selectorY + "px", 'width':thumbnailSize + "px", 'height':thumbnailSize+"px"});
								updateFormValues();
							}
						});
						function updateFormValues(){
							$('#thumbnailX').val(Math.round((selectorX + 1) / scale));
							$('#thumbnailY').val(Math.round((selectorY + 1) / scale));
							$('#thumbnailSize').val( Math.round(( thumbnailSize + 2 ) / scale));
								
							
						}
					});
					img.error(function(evt){
						console.log("error");
					});
				
				}
			},
			'progress':function(e,data){
				var done = e.position || e.loaded, total = e.totalSize || e.total;
				if(done > 0){
					//data.context.find('img').css('display', 'none');
					//data.context.find('.progress').show();
					//data.context.find('.bar').css('width', Math.round(100 * done/total) + "%");
				}
				console.log("progress", done);
			},
			'sequentialUploads':true,
			'dropZone':$('div.Dropzone')
			
		});
	});
}(window));