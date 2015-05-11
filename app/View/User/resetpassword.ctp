<div class="container-fluid">
	<div class="span6 offset3">
		<?php
			echo $this->Form->create('User', array('url' => array('controller' => 'User', 'action' => 'resetpassword'), 'id'=>'PasswordResetForm'));
			echo $this->Form->input('save', array('type'=>'hidden', 'value'=>'1'));
			echo $this->Form->input('username', array('type'=>'text', 'label'=>'Username or Email'));
			echo $this->Form->end('Reset');
		?>
	</div>
</div>