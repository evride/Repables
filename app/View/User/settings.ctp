
<div class="container-fluid">
<h2 class="headerUnderline">Edit Your Profile</h2>
<?php
	
	echo $this->Form->create('User', array('url' => array('controller' => 'User', 'action' => 'settings'), 'id'=>'UserSettingsForm'));
	echo $this->Form->input('save', array('type'=>'hidden', 'value'=>'1'));
?>
<div class="row-fluid">
	<div class="span4">
	
	<?php
		echo $this->Form->input('name', array('type'=>'text', 'value'=>$Fullname, 'class'=>'UserSetting'));
		echo $this->Form->input('location', array('type'=>'text', 'value'=>$Location, 'class'=>'UserSetting'));
		echo $this->Form->input('company', array('type'=>'text', 'value'=>$Company, 'class'=>'UserSetting'));
		echo $this->Form->input('birthdate', array('type'=>'date', 'selected'=>$Birthdate, 'minYear' => date('Y') - 100, 'maxYear' => date('Y'), 'separator'=>'', 'class'=>'UserSetting'));
		
		
		echo $this->Form->input('displayage', array('label'=>'Display age on profile', 'type'=>'checkbox', 'checked'=>$DisplayBirthdate!='no', 'disabled'=>($age < 13?true:false)));
		
		echo $this->Form->input('hideinappropriate', array('label'=>'Hide inappropriate material', 'type'=>'checkbox', 'checked'=>$HideInappropriate, 'disabled'=>($age < 18?true:false)));
		
		echo $this->Html->tag('br');
		echo $this->Form->input('email', array('type'=>'email', 'value'=>$Email, 'class'=>'UserSetting'));
		echo $this->Form->input('displayemail', array('label'=>'Show email on profile', 'type'=>'checkbox', 'checked'=>$EmailPublic, 'disabled'=>($age < 13?true:false)));
		echo $this->Form->input('website', array('type'=>'text', 'value'=>$Website, 'class'=>'UserSetting'));
	?>
	</div>
	<div class="span8">
		<?php echo $this->Form->input('bio', array('type'=>'textarea', 'value'=>$Bio,'style'=>'width:580px;height:150px')); ?>
		<label>Profile Picture</label>
			
		<div class="Dropzone">
			<div id="ThumbEditorContainer"<?php if($hasProfileImage){ echo ' style="display:block"'; }?>>			
				<div id="ThumbnailSizeSlider"></div>
				<div id="ThumbnailSelector"></div>
				<div id="FullProfileImage">
					<?php
						if($hasProfileImage){
							echo $this->Html->image($this->Html->url('/' .$profileImage['File']));
						}
					?>
				</div>
				
			</div>
			<div class="DropzoneInner">
				<div class="DropzoneText"></div>
				<?php 
					echo $this->Form->input('image', array('type'=>'file', 'label'=>false)); 
					echo $this->Form->input('imageID', array('type'=>'hidden', 'value'=>0, 'id'=>'imageID'));
					echo $this->Form->input('thumbnailSize', array('type'=>'hidden', 'value'=>0, 'id'=>'thumbnailSize'));
					echo $this->Form->input('thumbnailX', array('type'=>'hidden', 'value'=>0, 'id'=>'thumbnailX'));
					echo $this->Form->input('thumbnailY', array('type'=>'hidden', 'value'=>0, 'id'=>'thumbnailY'));
				?>
			</div>
		</div>
	</div>
	<div class="clearBoth padding20">
	</div>

</div>
<?php
			
	echo $this->Form->submit('Save', array('class'=>'btn btn-primary btn-next pull-right'));
	echo $this->Form->end();
?>
</div>