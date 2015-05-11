<div class="row-fluid">
	<div class="span6 offset3 AttentionContainer">
		<h4>Send Us Feedback</h4>
		<div class="AttentionBox" id="FeedbackBox">
		<?php
		if($this->Session->read('User.ID') == 6){
			echo $this->Form->create('Feedback', array('url'=>'/feedback'));	
			echo $this->Form->textarea('message');
			echo $this->Form->submit('Submit', array('class'=>'btn btn-primary', 'title'=>'Submit'));
			echo $this->Form->end();
		}

		?>
		</div>
	</div>
</div>