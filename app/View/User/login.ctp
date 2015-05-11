<div align="center">
	<div id="RegisterLoginBox" class="span6 offset3 UserFormBox">
		<div class="row-fluid">
			<div id="RegisterBox" class="span5">
				<h2>Register</h2>
				<div id="RegisterMessage">
					Don't have an account?<br />
					Create one now.
					<div>
						<a href="<?php echo $this->Html->url('/register') ?>" class="btn">Register</a>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div id="LoginForm" class="UserForm span7">
				<h2>Login</h2>
				<?php 
					if(isset($ErrorFlashText)){
						echo '<div class="ErrorFlash">' . $ErrorFlashText . '</div>';
					}
				?>
				<div>
					<?php
						echo $this->Form->create('User', array('url'=>array('controller'=>'User', 'action'=>'login')));
						
						echo $this->Form->input('username', array('autofocus'=>'autofocus'));
						echo $this->Form->input('password', array('value'=>''));
						echo $this->Html->div('forgotPassword', $this->Html->tag('a', 'Forgot your password?', array('href'=>$this->Html->url('/resetpassword'))));
						echo $this->Form->submit('Login', array('class'=>'btn btn-primary', 'title'=>'Login'));
						echo $this->Form->end();
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear:both"></div>