<div align="center">
	<div class="UploadSection">
		
		<?php
			echo $this->Form->create('Upload', array('action'=>'complete', 'id'=>'fileupload'));
			echo $this->Form->input('hash', array('type'=>'hidden', 'value'=>$UploadHash, 'id'=>'UploadSessionHash'));
			if(isset($itemID)){
				echo $this->Form->input('itemID', array('type'=>'hidden', 'value'=>$itemID));
			}
		?>
		
		<div class="PrimaryFilesSection">
			<script type="text/plain" id="FileDetailsTemplate">
				<div>
					<input type="hidden" name="data[FilesData][{{uploadID}}][OriginalFilename]" class="OriginalFilename" value="" id="FilesData{{uploadID}}OriginalFilename">
					<div class="input text input-append">
						<input name="data[FilesData][{{uploadID}}][Filename]" placeholder="Filename" value="" class="FilenameInput" type="text" id="FilesData{{uploadID}}Filename">
						<span class="add-on"></span></div><div class="input checkbox">
						<input type="hidden" name="data[FilesData][{{uploadID}}][PreviewImage]" id="FilesData{{uploadID}}PreviewImage_" value="0">
						<input type="checkbox" name="data[FilesData][{{uploadID}}][PreviewImage]" class="DefaultPreview" value="1" id="FilesData{{uploadID}}PreviewImage">
						<label for="FilesData{{uploadID}}PreviewImage">Default Preview Image</label>
					</div>
					<div class="input checkbox">
						<input type="hidden" name="data[FilesData][{{uploadID}}][Render]" id="FilesData{{uploadID}}Render_" value="0">
						<input type="checkbox" name="data[FilesData][{{uploadID}}][Render]" checked="checked" value="1" id="FilesData{{uploadID}}Render">
						<label for="FilesData{{uploadID}}Render">Render Gallery Image</label></div><div class="input checkbox">
						<input type="hidden" name="data[FilesData][{{uploadID}}][AllowDownload]" id="FilesData{{uploadID}}AllowDownload_" value="0">
						<input type="checkbox" name="data[FilesData][{{uploadID}}][AllowDownload]" checked="checked" value="1" id="FilesData{{uploadID}}AllowDownload">
						<label for="FilesData{{uploadID}}AllowDownload">Allow Downloading</label>
					</div>
					<div class="clearBoth"></div>
				</div>
			</script>
			
			<h4>Files</h4>
			<div id="PrimaryContainer" class="FileTableContainer">			
				<div <?php if(isset($files) && count($files)>=1){ echo 'style="display:none"'; } ?> class="Dropzone"><div>No files have been added yet.</div></div>
				<table id="PrimaryUploads" class="UploadsList" width="100%">
					<thead class="nodrag nodrop"><tr><th width="16px"></th><th width="12px"></th><th></th><th width="120px"></th><th width="150px"></th><th width="80px"></th></tr></thead>
					<?php
						if(isset($files)){
							for($i = 0; $i < count($files); $i++){ 
								
								$uID = $files[$i]['Upload']['UploadID'];
								$filename = substr($files[$i]['Upload']['Filename'], 0, strrpos($files[$i]['Upload']['Filename'], '.'));
								echo '<tbody id="upload' . $uID . '" class="fileRow">';
								echo '	<input class="CompletedUpload" type="hidden" name="data[Upload][uploads][]" value="' . $uID . '">';
								echo '	<tr>';
								echo '		<td class="grab"></td><td class="DetailsArrow"><span class="caret caret-right"></span></td><td class="UploadName">' . $files[$i]['Upload']['Filename'] . '</td><td class="bytesCell">' . $files[$i]['Upload']['Filesize'] . '</td>';
								echo '		<td align="center" width="150px"><div class="progress progress-striped"><div class="bar" style="width:100%">Upload Complete</div></div></td><td align="right"><a href="#" class="btn btn-danger btn-mini">Remove</a></td>';
								echo '	</tr>';
								echo '	<tr><td class="FileDetails" colspan="7"><div>';
								echo $this->Form->hidden('FilesData.' . $uID . '.OriginalFilename', array('class'=>'OriginalFilename', 'value'=>$filename));
								echo $this->Form->input('FilesData.' . $uID . '.Filename', array('type'=>"text", 'placeholder'=>'Filename', 'label'=>false, 'value'=>$filename, 'class'=>'FilenameInput', 'after'=>'<span class="add-on">.' . $files[$i]['Upload']['FileExtension'] . '</span>', 'div'=>array('class'=>'input text input-append')));
								echo $this->Form->input('FilesData.' . $uID . '.PreviewImage', array('type'=>"checkbox", 'label'=>"Default Preview Image", 'class'=>'DefaultPreview'));
								echo $this->Form->input('FilesData.' . $uID . '.Render', array('type'=>"checkbox", 'label'=>"Render Gallery Image", 'checked'=>true));
								echo $this->Form->input('FilesData.' . $uID . '.AllowDownload', array('type'=>"checkbox", 'label'=>"Allow Downloading", 'checked'=>true));
								
								//echo $this->Form->input('PreviewImage', 
								echo '  <div class="clearBoth"></div></div></td></tr>';
								echo '</tbody>';
							}
						}
					?>
				</table>
			</div>
			<div id="PrimaryAddFiles">
			<?php						
				echo $this->Form->input('PrimaryFile', array('type'=>'file', 'label'=>'', 'class'=>'fileInput', 'multiple'=>'', 'id'=>'PrimaryFile'));
				echo $this->Html->tag('a', 'Add File', array('href'=>'#', 'class'=>'btn btn-add-file', 'id'=>'AddPrimaryFileBtn'));
			?>
			</div>
		</div>
		
		
		<div class="DetailsSection">
			<h4>Details</h4>
			<?php
				
				echo $this->Form->input('name', array('type'=>'text', 'value'=>isset($itemData['Name'])?$itemData['Name']:''));
				echo $this->Html->tag('label', 'Description', array('for'=>'DescriptionTextArea'));
				echo $this->Form->textarea('description', array('id'=>'DescriptionTextArea', 'value'=>isset($revisionIndex) && $revisionIndex!= -1?Sanitize::html_decode(String::stripLinks($revisions[$revisionIndex]['ItemRevision']['Description'])):''));
				echo $this->Html->tag('label', 'Instructions', array('for'=>'InstructionsTextArea'));
				echo $this->Form->textarea('instructions', array('id'=>'InstructionsTextArea', 'value'=>isset($revisionIndex) && $revisionIndex!= -1?Sanitize::html_decode(String::stripLinks($revisions[$revisionIndex]['ItemRevision']['Instructions'])):''));
				echo $this->Form->input('tags', array('type'=>'text', 'label'=>'Tags <span class="label label-info">&nbsp;Comma Separated Terms&nbsp;</span>', 'value'=>isset($itemData['Tags'])?Sanitize::html_decode(str_replace(',', ', ', $itemData['Tags'])):''));
				
				echo $this->Form->input('version', array('label'=>'Revision Number', 'value'=>isset($revisionIndex) && $revisionIndex!= -1?$revisions[$revisionIndex]['ItemRevision']['Description']:''));
				//echo $this->element('licenseselect');
				
				echo $this->Form->input('license', array('type'=>'select', 'options'=>array(
					'cc'=>"Attribution - Creative Commons",
					'cc-sa'=>'Attribution - Share Alike - Creative Commons',
					'cc-nd'=>'Attribution - No Derivatives - Creative Commons',
					'cc-nc'=>'Attribution - Non-Commercial - Creative Commons',
					'cc-nc-sa'=>'Attribution - Non-Commercial - Share Alike',
					'cc-nc-nd'=>'Attribution - Non-Commercial - No Derivatives',
					'pd'=>'Creative Commons - Public Domain Dedication',
					'gpl'=>'Creative Commons - GNU GPL',
					'lgpl'=>'Creative Commons - LGPL',
					'bsd'=>'BSD License',
					'nokia'=>'Nokia',
					'public'=>'Public Domain'), 'value'=>(isset($revisionIndex) && $revisionIndex >= 1)?$revisions[$revisionIndex]['ItemRevision']['License']:''));
				if(isset($itemData['Flagged']) && (bool)$itemData['Flagged']){
					echo '<div class="alert alert-danger">This item has been marked as inappropriate for children under 18</div>';
				}else{
					echo $this->Form->input('inappropriate', array('type'=>'checkbox', 'label'=>'Mark this item as inappropriate for children under 18'));
				}
				
				if(!isset($userID) || !($userID >= 1)){
					$termsAgreeLabel = 'I agree to the <a href="' . $this->Html->url('/licenseagreement') . '" target="_blank">Terms of Service</a> and <a href="' . $this->Html->url('/privacypolicy') . '" target="_blank">Privacy Policy</a>';
					echo $this->Form->input('termsagree', array('type'=>'checkbox', 'label'=>$termsAgreeLabel));
				}
				
			
			?>
		</div>
		<?php	
						
			echo $this->Form->submit('Save and Process', array('class'=>'btn btn-primary btn-next pull-right', 'id'=>"UploadSaveBtn"));
			
			echo $this->Form->end();
		?>
	</div>
		
</div>
<div style="clear:both"></div>