<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    <?php echo $username; ?>
    <span class="caret"></span>
</a>
<ul class="dropdown-menu">
	<li><a href="<?php echo $this->Html->url('/u/' . $username); ?>">Uploaded Items</a></li>
	<li><a href="<?php echo $this->Html->url('/User/settings'); ?>">Settings</a></li>
	<li><a href="<?php echo $this->Html->url('/User/changepassword'); ?>">Change Password</a></li>
	<li><a href="<?php echo $this->Html->url('/logout'); ?>">Logout</a></li>
</ul>