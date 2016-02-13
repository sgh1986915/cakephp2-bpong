<script type="text/javascript">
function ShowTournEventInformation(team) {

	if ($(team).val()!=0) {
		$('#TournEventInformation').load("/teams/getTeamInformation/"+$(team).val()+"/<?php echo $signupDetails['Signup']['model_id']?>"+"/<?php echo $signupDetails['Signup']['model']?>",{cache: false});
	}else {
		$('#TournEventModel').val('');
		$('#TournEventInformation').load("/teams/getTeamInformation",{cache: false});
	}

}

</script>

<!--Assign team to the Event or tournament -->
	<div class="tournaments form" style="border:#ccc solid 1px; padding:20px">
		<?php echo $this->Form->create('TournEvent',array('url'=>"/teams/AssignTournEvent"));?>
		<h2>Assign team to the <?php echo $signupDetails['Signup']['model']." ".$signupDetails[$signupDetails['Signup']['model']]['name'] ?></h2>
		<?php echo $this->Form->hidden('object_id',array('value'=>$signupDetails['Signup']['model_id']));?>
		<?php echo $this->Form->hidden('model',array('value'=>$signupDetails['Signup']['model']));?>
		<?php echo $this->Form->select("team_id", $teams, false, array('escape' => false,  'onchange' => 'ShowTournEventInformation(this)','style'=>'float:left'), false); ?>
		<div id="TournEventInformation" style="border:#ccc solid 1px; padding:10px; float:left; margin-left:15px; width:400px">
			<?php echo $information;?>
		</div>
		<?php echo $this->Form->end('Assign');?>
	</div>
<!-- EoF assigment -->