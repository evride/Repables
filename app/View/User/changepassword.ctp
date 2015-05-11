<div align="center">
	<div id="RegisterFormContainer" class="span4 offset4 UserFormBox">
		<div id="RegisterFormBox">
			<h3>Change Password</h3>
			<div class="row-fluid">
				<div class="span10 offset1">
					<?php 
						if(isset($UserSettingWarning)){
					?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							
							<?php echo $UserSettingWarning; ?>
								
						</div>
					<?php
						}
						if(isset($UserSettingSuccess)){
					?>
						<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							
							<?php echo $UserSettingSuccess; ?>
								
						</div>
					<?php
						}
					?>
					<?php
						echo $this->Form->create('User', array('url' => array('controller' => 'User', 'action' => 'changepassword'), 'id'=>'UserPasswordForm'));
						echo $this->Form->input('save', array('type'=>'hidden', 'value'=>'1'));
						echo $this->Form->input('password', array('type'=>'password', 'value'=>''));
						echo $this->Form->input('newpassword', array('type'=>'password', 'label'=>'New Password', 'value'=>''));
						echo $this->Form->input('newpassword2', array('type'=>'password', 'label'=>'Confirm Password', 'value'=>''));
						echo $this->Form->submit('Save', array('class'=>'btn btn-primary', 'title'=>'Login'));
						echo $this->Form->end();
					
					?>
				</div>
			</div>
			<div class="clearBoth"></div>
		</div>
	</div>
</div>
<div class="clearBoth"></div>