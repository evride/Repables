<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php if(isset($pageTitle) && strlen($pageTitle)>= 1){ echo $pageTitle . ' - ';} ?>Repables</title>
		<script type="text/javascript">
			var baseURL = "<?php echo $this->Html->url('/', true); ?>";
			<?php
				if(isset($jsRedirect)){
					if(!isset($jsRedirectDelay)){
						$jsRedirectDelay = 5000;
					}
			?>
			
			var redirectURL = <?php echo '"' . $jsRedirect . '"'; ?>;
			var redirectDelay = <?php echo $jsRedirectDelay; ?>;
			function redirectPage(){
				window.location = redirectURL;							
			}
			setTimeout(redirectPage, redirectDelay);
			<?php					
				}
			?>
		</script>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap');
		echo $this->Html->css('default');
		echo $this->Html->css('http://fonts.googleapis.com/css?family=Orbitron:900');
		echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
		//echo $this->Html->script('http://code.jquery.com/ui/1.10.2/jquery-ui.js');
		echo $this->Html->script('modernizr');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('main');
		
		if(isset($cssIncludes)){
			foreach($cssIncludes as $css){
				echo $this->Html->css($css);
			}
		}
		if(isset($jsIncludes)){
			foreach($jsIncludes as $js){
				echo $this->Html->script($js);
			}
		}

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-48926201-1', 'repables.com');
	  ga('send', 'pageview');

	</script>
	<?php
		if(isset($showBanner)){
			if($showBanner == true){
				echo $this->element('banner');
			}
		}	
	?>
	<div id="container">
		<div id="navBar" class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="<?php echo $this->Html->url('/'); ?>">REPABLES</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
						  <li><a href="<?php echo $this->Html->url('/explore'); ?>">Explore</a></li>
						  <li><a href="<?php echo $this->Html->url('/upload'); ?>">Upload</a></li>
						</ul>
					</div>
					<div id="searchBox" class="pull-left">
						<form id="searchForm" action="<?php echo $this->Html->url('/search'); ?>" method="POST">	
							<input type="text" id="searchInput" name="search" autocomplete="off" placeholder="Search"/>
							<input type="submit" id="searchSubmit" name="searchSubmit" value="">
						</form>
						<div id="searchHints"></div>
					</div>
					<div class="nav-collapse collapse" style="float:right">
						<?php
							if(isset($username) && strlen($username)>=1){
								echo $this->element('userdropdown');
							}else{
								echo $this->element('usersignin');
							}
						?>
					</div>
					
				</div>
			</div>
		</div>
		<div class="container">
			<div id="content">
				
				<?php echo $this->element("side_banner"); ?>
				
				<?php echo $this->Session->flash(); ?>

				<?php echo $this->fetch('content'); ?>
			</div>
		</div>
		<div align="center">
			<div id="footer">
				<div class="container">
					<a class="brand" href="<?php echo $this->Html->url('/'); ?>">REPABLES</a> 
					<ul class="FooterLinks">
						<li>
							<a href="<?php echo $this->Html->url('/feedback/'); ?>">Feedback</a>
						</li>
						<li>
							<form class="PaypalLink" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="NXQPEJS36CXRG">
								<input type="submit" name="submit" value="Donate" class="DonateButton">
							</form>
						</li>
						<li>
							<a href="<?php echo $this->Html->url('/licenseagreement/'); ?>">Terms of Service</a>
						</li>
						<li>
							<a href="<?php echo $this->Html->url('/privacypolicy/'); ?>">Privacy Policy</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>	
	<?php 
	if(isset($adminlevel) && $adminlevel == '1'){
		echo $adminlevel . '.';
		echo $this->element('sql_dump'); 
	} 
	?>
</body>
</html>
