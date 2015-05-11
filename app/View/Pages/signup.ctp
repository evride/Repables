<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Repables</title>
	<link href="favicon.ico" type="image/x-icon" rel="icon" />
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<?php
		echo $this->Html->css('bootstrap');
		echo $this->Html->css('http://fonts.googleapis.com/css?family=Orbitron:900');
		echo $this->Html->css('email_signup');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
		echo $this->Html->script('http://code.jquery.com/ui/1.10.2/jquery-ui.js');
		echo $this->Html->script('modernizr');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('email_signup');
	?>
</head>

<body>
	<div id="container">
		<div id="navBar" class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<span class="brand" id="codename">REPABLES</span>			
			</div>
		</div>
		<div id="background"></div>
		<div id="dotOverlay" class="visible-desktop"></div>
		<div id="messageContainer">
			<div id="content">
				<div id="message">
					<h1>REPABLES</h1>
					<p>We are working on a collaborative file sharing site for makers. Sign up now to receive an email when we launch.</p>					
					<div id="formDiv">
						<form id="emailForm" action="<?php echo $this->Html->url('/'); ?>" method="POST">
							<label for="email">Email</label>
							<input type="email" name="email" id="email" />
							<input type="submit" class="btn btn-primary btn-large" value="Sign Up" />						
						</form>
					</div>
					<div id="responseMessage">
						<?php
						if($success){							
							echo '<p class="text-success">Thank you! We\'ll email you when it\'s ready.</p>';
						}else if($exists){
							echo '<p class="text-info">The email submitted already exists in our database. Thank you!</p>';
						}else if($error){
							echo '<p class="text-error">An error was experienced when entering the email address.</p>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="clearBoth"></div>
	</div>
	</body>
</html>