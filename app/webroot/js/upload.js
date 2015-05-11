(function(window){
	var files = [];
	var state = 0;
	function checkFilesSelected(){
		var table = $('#PrimaryUploads');
		if(table.find('tbody.fileRow').length >=1){
			table.parent().children('.Dropzone').hide();
		}else{
			table.parent().children('.Dropzone').show();
		}
	}
	function updateFileList(){
		checkFilesSelected();
		$('#PrimaryUploads').tableDnDUpdate();
		var f = [];
		var rows = $('tbody.fileRow');
		for(var i = 0; i < rows.length; i++){
			if($(rows[i]).attr('id')){
				var fID = parseInt($(rows[i]).attr('id').substr(6));
				console.log(fID);
				if(fID >= 1){
					f.push(fID);
				}
			}
		}
	}
	function readFile(){
		
		var reader = new FileReader();
		reader.onload = function(event){
		  var dataURL = event.target.result;
		  //console.log(dataURL);
		  
		};
		reader.readAsText(files[0]);
	}
	window.updateFileList = updateFileList;
	function file_add(e, data){
		console.log('file_add', e, data);
		files.push(data.files[0]);
		
		var fileName = data.files[0].name;
		
		var extStart = fileName.lastIndexOf('.');
		var fileExt = "";
		if(extStart >= 1){
			fileExt = fileName.substr(extStart + 1);
		}
		var fileSize = data.files[0].size?data.files[0].size:(data.files[0].fileSize?data.files[0].fileSize:'');
		var dataRow = createDataRow(fileName, fileSize)			
		data.context = dataRow;
		$('#PrimaryUploads').append(dataRow);
		
		dataRow.find(".btn-danger").click(function(evt){
			data.abort();
			dataRow.remove();
			updateFileList();
		});
		
		updateFileList();
		data.submit();
	}
	function createDataRow(name, fileSize){
		
		var fileRowHTML = '<tbody class="fileRow">';
		fileRowHTML += '	<input class="CompletedUpload" type="hidden" name="data[Upload][uploads][]" value="">';
		fileRowHTML += '	<tr>';
		fileRowHTML += '		<td class="grab"></td><td class="DetailsArrow"><span style="display:none" class="caret caret-right"></span></td><td></td><td class="bytesCell"></td>';
		fileRowHTML += '		<td align="center" width="150px"><img src="' + baseURL + '/img/uploading.gif"><div style="display:none" class="progress progress-striped"><div class="bar" style="width:0%"></div></div></td><td align="right"><a href="#" class="btn btn-danger btn-mini">Remove</a></td>';
		fileRowHTML += '	</tr>';
		fileRowHTML += '	<tr><td class="FileDetails" colspan="7"></td></tr></tbody>';
		
		
		
		var dataBody = $(fileRowHTML);
		dataRow = dataBody.children("tr:eq(0)");
		dataRow.children("td:eq(2)").text(name);
		dataRow.children("td:eq(3)").text(fileSize == ""?"":fileSize + " bytes");
				
		
		//dataBody.draggable({'handle':'td.grab', 'helper':'clone', 'start':function(evt, i){console.log(evt, i); i.helper.css('width', '800px');}, 'end':function(evt, i){console.log(evt, i);}});
		
		return dataBody;
	}
	function deleteUpload(id){
		console.log(id);
		if(typeof id !== "number"){
			id = $(this).parents('tbody.fileRow').children("input.CompletedUpload").val();
		}
		$.ajax(baseURL + 'Uploads/delete/', {'method':'POST', 
			'data':{'uploadID':id, 'hash': $('#UploadSessionHash').val()}, 
			'complete':function(data, status){
				console.log(data);
				var response = $.parseJSON(data.responseText);
				if(response.status == "success"){
					console.log(data);
				}
			}
		});
		$('#upload' + id).remove();
		updateFileList();
		console.log('deleteUpload', id);
	}
	function detailsDropdown(evt){
		$(evt.currentTarget).parents('.fileRow').find('.FileDetails').toggle();	
		$(evt.currentTarget).parents('.fileRow').find('.DetailsArrow span').toggleClass("caret-down");
	}
	function previewImageChanged(evt){
		$('.DefaultPreview').prop('checked', false);
		$(evt.currentTarget).prop('checked', true);
	}
	function filenameChanged(evt){
		var val = $(evt.currentTarget).val();
		val = $.trim(val).replace(/\s+/, ' ');
		if(val == ""){
			val = $(evt.currentTarget).parents('td.FileDetails').find('.OriginalFilename').val();
		}
		$(evt.currentTarget).parents('tbody').find('td.UploadName').text(val + $(evt.currentTarget).parent().children('span').text());
	}
	function filenameBlur(evt){	
		var val = $(evt.currentTarget).val();
		val = $.trim(val).replace(/\s+/g, ' ');
		$(evt.currentTarget).val(val);
		if(val == ""){
			$(evt.currentTarget).val($(evt.currentTarget).parents('td.FileDetails').find('.OriginalFilename').val());
		}
		filenameChanged(evt);
	}
	$(document).ready(function(){
		$("#PrimaryUploads").tableDnD({
			/*onDrop: function(table, row) {
				var debugStr = "Row dropped was "+row.id+".";
				console.log(debugStr);
			},
			onDragStart: function(table, row) {
				console.log("Started dragging row "+row.id);
			},*/
			onDragClass: "dragFileRow",
			dragHandle: ".grab"}
		);
		$('.DetailsArrow').click(detailsDropdown);
		$('tr.fileRow td:eq(2)').dblclick(detailsDropdown);
		$('input.DefaultPreview').change(previewImageChanged);
		$('input.FilenameInput').bind('change keypress keyup', filenameChanged);
		$('input.FilenameInput').blur(filenameBlur);
		if( window.FileReader && Modernizr.draganddrop == true){
			$('.Dropzone').css('border', '4px dashed #CCC');
			$('.Dropzone div').text('No files have been added yet. Drop them here.');
		}
		if($('#PrimaryUploads').find('tr').length >= 2){		
			$('#PrimaryContainer .Dropzone').hide();
		}
		if($('#SecondaryUploads').find('tr').length >= 2){			
			$('#SecondaryContainer .Dropzone').hide();
		}
		
		$('#PrimaryUploads tr .btn-danger').click(deleteUpload);
		$('#SecondaryUploads tr .btn-danger').click(deleteUpload);
		//$('.btn-next').hide();
		//$('.SupportingFilesSection').css('display', 'none');
		//$('.DetailsSection').css('display', 'none');
		$('#fileupload').fileupload({
			'url': baseURL + 'upload/save', /*'http://node.repables.com/',*/
			//'drop':file_drop,
			'add':file_add,
			'done':function(e, data){
			
				console.log(data, data.result);
				uploadData = $.parseJSON(data.result);
				if(data.uploadType){
					files[data.uploadType].push(uploadData.uploadID);
				}
				var dataBody = data.context;
				dataBody.attr('id', 'upload' + uploadData.uploadID);
				dataBody.find('input.CompletedUpload').val(uploadData.uploadID);
				dataBody.find('img').css('display', 'none');
				dataBody.find('.bar').css('width', '100%').text("Upload Complete");
				dataBody.find('.progress').css('visibility','visible');
				dataBody.find('.btn-danger').unbind("click");
				dataBody.find('.btn-danger').click(function(evt){
					deleteUpload(uploadData.uploadID);
				});
				
				
				dataBody.find('.FileDetails').html($('#FileDetailsTemplate').text().replace(/{{uploadID}}/g, uploadData.uploadID));
				dataBody.find('.input-append .add-on').text(uploadData.extension);
				dataBody.find('input.FilenameInput, input.OriginalFilename').val(uploadData.filename);
				
				dataBody.find('.DetailsArrow span').show();
				dataBody.find('.DetailsArrow').click(detailsDropdown);
				dataBody.find('tr.fileRow td:eq(2)').dblclick(detailsDropdown);
				dataBody.find('input.DefaultPreview').change(previewImageChanged);
				dataBody.find('input.FilenameInput').bind('change keypress keyup', filenameChanged);
				dataBody.find('input.FilenameInput').blur(filenameBlur);
				
				updateFileList();
			},
			'progress':function(e,data){
				var done = e.position || e.loaded, total = e.totalSize || e.total;
				if(done > 0){
					data.context.find('img').css('display', 'none');
					data.context.find('.progress').show();
					data.context.find('.bar').css('width', Math.round(100 * done/total) + "%");
				}
			},
			'send': function(e, data){ console.log("send", jQuery.extend(true, {}, e), jQuery.extend(true, {}, data)); }, 
			'submit': function(e, data){ console.log("submit", jQuery.extend(true, {}, e), jQuery.extend(true, {}, data)); }, 
			'start':function(e, data){ console.log("start", jQuery.extend(true, {}, e), jQuery.extend(true, {}, data)); },
			'process':function(e, data){ console.log("process", jQuery.extend(true, {}, e), jQuery.extend(true, {}, data)); },
			'sequentialUploads':true,
			'autoUpload': true,
			'dropZone':$('div.FileTableContainer')
			
			});
		$('#fileupload').submit(function(evt){
			var reqMet = false;
			if($('#UploadName').val().length >= 2){
				var completedUploads = $('.CompletedUpload');
				if(completedUploads.length >= 1){
					for(var i = 0; i < completedUploads.length; i++){
						console.log(completedUploads[i]);
						if($(completedUploads[i]).val() >= 1){
							if(i == completedUploads.length-1){
								reqMet = true;
							}
						}else{
							break;
						}
					}
				}
			}
			if(!reqMet){
				evt.preventDefault();
			}
		})
		$('.fileInput').mouseover(function(evt){
			$(evt.currentTarget).parent().parent().children('a.btn-add-file').addClass('add-file-hover');
		});
		$('.fileInput').mouseout(function(evt){			
			var btn = $(evt.currentTarget).parent().parent().children('a.btn-add-file');
			btn.removeClass('add-file-hover');
			btn.removeClass('btn-primary');
		});
		$('.fileInput').mousedown(function(evt){
			var btn = $(evt.currentTarget).parent().parent().children('a.btn-add-file');
			btn.addClass('btn-primary');
			btn.removeClass('add-file-hover');
		});
		$('.fileInput').mouseup(function(evt){
			var btn = $(evt.currentTarget).parent().parent().children('a.btn-add-file');
			btn.addClass('add-file-hover');
			btn.removeClass('btn-primary');
		});
		
		
		$('#AllowPublicShare').change(function(e){
			if(this.checked == false){
				$('#PublicShareLabel').removeClass('btn-primary');
				$('#PublicShareLabel').addClass('btn-warning');				
			}else{
				$('#PublicShareLabel').removeClass('btn-warning');
				$('#PublicShareLabel').addClass('btn-primary');	
			}
		});
	});
}(window));