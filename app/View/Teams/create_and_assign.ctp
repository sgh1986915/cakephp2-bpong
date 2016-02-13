<?php echo $this->element('mce_init_simple', array('name' => 'TeamDescription')); ?>
<script type="text/javascript">
$(document).ready(function() {
// validate signup form on keyup and submit
	$("#TeamAddForm").validate({
		rules: {
			"data[Team][name]": "required",
			"data[Team][people_in_team]": "digits"
		}
	});
	//EOF Validation

});
//EOF ready
</script>

<h2>Add new team</h2>
<div class="teams form p10" >
<?php echo $this->Form->create('Team',array('id' => 'TeamAddForm', 'enctype'=>"multipart/form-data", 'url' => '/teams/create_and_assign/' . $model . '/' . $modelID . '/' . $signupID));?>
<?
	echo $this->Form->input('Image.new',array('type' => 'file','class'=>'file','label'=>'Image'));
	echo $this->Form->hidden('Image.new.prop',array('value'=>'Personal'));
?>
	<?php
	echo $this->Form->input('name', array('size' => 70));
	echo $this->Form->hidden('people_in_team',array('value'=> $peopleInTeam));
	echo $this->Form->input('description', array('style' => 'width:80%;', 'label' => 'Team Statement'));
	?>
<?php echo $this->Form->end('Submit');?>
</div> 