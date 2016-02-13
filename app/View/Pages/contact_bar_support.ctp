<?php $this->set('title_for_layout', 'Contact NBPL Bar Support');?>
<script type="text/javascript">
$(document).ready(function() {
// validate signup form on keyup and submit
	$("#contact_form").validate({
		rules: {
			"data[Contact][first_name]": "required",
			"data[Contact][last_name]": "required",
			"data[Contact][bar_name]": "required",
			"data[Contact][bar_adddress]": "required",
			"data[Contact][email]": {
				required: true,
				email: true
			}
		},
		messages: {
			"data[Contact][email]": "Please enter a valid email address"
		}
	});
	//EOF Validation
});
</script>

<div class="col maincol bigtext not">				
	<h2>Bar Program Contact Form<br />
		<span>If you have any questions relating to bringing Beer Pong to your bar, fill in the form below and we will be pleased to contact you to answer your query.</span>
		</h2>
	  <?php echo $this->Form->create('Contact', array('id' => 'contact_form', 'name'=>'contact_form', 'url' => '/Pages/send_bar_support', 'method' => 'post', 'class' => 'inl'));?>
		<?php 
		echo $this->Form->input('first_name', array('label' => '*First name (Fields marked * are mandatory)', 'val' => ''));
		echo $this->Form->input('last_name', array('label' => '*Last name'));	
		echo $this->Form->input('email', array('label' => '*Email address'));
		echo $this->Form->input('bar_name', array('label' => '*Bar name'));	
		echo $this->Form->input('bar_adddress', array('label' => '*Bar address'));
        echo $this->Form->input('bar_city_state',array('label'=>'*Bar City/State'));
        echo $this->Form->input('bar_manager_name',array('label'=>'Name of Bar Manager (if available)'));
		echo $this->Form->input('telephone', array('label' => 'Telephone number'));
		echo $this->Form->input('question', array('type' => 'textarea', 'label' => 'Question'));
		?>
		<div class="submit">
				<input type="submit" class="btn" value="SEND QUESTION" name="" />
		</div>
	</form>
</div>