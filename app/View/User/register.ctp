<div align="center">
	<div id="RegisterFormContainer" class="span4 offset4 UserFormBox">
		<div id="RegisterFormBox">
			<h2>Register</h2>
			<div class="row-fluid">
				<div class="span10 offset1">
					<?php
						if(isset($ErrorFlashText)){
							echo $ErrorFlashText;
						}
						echo $this->Form->create('User', array('url'=>array('controller'=>'User', 'action'=>'register')));
						
						echo $this->Form->input('saved', array('type'=>'hidden', 'value'=>1));
						echo $this->Form->input('username', array('label'=>'Username <span class="label" id="UsernameStatus"></span>'));
						echo $this->Form->input('email');
						echo $this->Form->input('password');
						echo $this->Form->input('confirmpassword', array('label'=>'Password Again', 'type'=>'password'));
						$termsLabel = "I agree to the <a href=\"" . $this->Html->url('/licenseagreement') . "\">Terms of Service</a> and <a href=\"" . $this->Html->url('/privacypolicy') . "\">Privacy Policy</a>";
						echo $this->Form->input('termsagree', array('type'=>'checkbox', 'label'=>$termsLabel));
						echo $this->Form->submit('Register', array('class'=>'btn btn-primary', 'title'=>'Register'));
						echo $this->Form->end();
					?>
				</div>
			</div>
			<div class="clearBoth"></div>
		</div>
	</div>
</div>
<div class="clearBoth"></div>