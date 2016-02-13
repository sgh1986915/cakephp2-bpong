<script type="text/javascript">
function ShowTournEventInformation(tournEvent) {

	if ($(tournEvent).val()!=0) {
		$('#TournEventModel').val($(tournEvent).find("option:selected").parent().attr("label"));
		$('#TournEventInformation').load("/teams/getTournEventInformation/<?php echo $teamID."/"?>"+$(tournEvent).val()+"/"+$(tournEvent).find("option:selected").parent().attr("label"),{cache: false});
	}else {
		$('#TournEventModel').val('');
		$('#TournEventInformation').load("/teams/getTournEventInformation",{cache: false});
	}

}

</script>
<!-- EOF teams -->
<?php if (!empty($tournEvents)):?>
<!--Assign team to the Event or tournament -->

<?php echo $this->Form->create('TournEvent',array('url'=>"/teams/AssignTournEvent"));?>
  <h2>Assign team to the new tournament or event</h2>
  <div class="select"><?php echo $this->Form->hidden('team_id',array('value'=>$teamID));?> <?php echo $this->Form->hidden('model',array('value'=>''));?> <?php echo $this->Form->select("TournEvent.object_id", $tournEvents, false, array('escape' => false,  'onchange' => 'ShowTournEventInformation(this)'), false); ?></div>
      <div class="heightpad"></div>
  <div id="TournEventInformation"><strong>Choose event or tournament.</strong></div>
    <div class="heightpad"></div>
  <?php echo $this->Form->end('Assign');?> 
<!-- EoF assigment -->
<?php endif;?>
<!-- Assigments-->
<?php if (!empty($teamAssigments)):?>
    <div class="heightpad"></div>
<h2>Assigments</h2>
<table>
  <tr>
    <th>Assign name</th>
    <th>Team name</th>
    <th>Status</th>
    <th>Created</th>
  </tr>
  <?php foreach($teamAssigments as $teamAssigment):?>
  <tr>
    <td><?php echo $teamAssigment['TeamsObject']['model'].": ".$teamAssigment[$teamAssigment['TeamsObject']['model']]['name']?></td>
    <td><?php echo $teamAssigment['TeamsObject']['name']?></td>
    <td><?php echo $teamAssigment['TeamsObject']['status']?></td>
    <td><?php echo $this->Time->niceDate($teamAssigment['TeamsObject']['created'])?></td>
  </tr>
  <?php endforeach;?>
</table>
<div class="paging"> <?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?> | <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?> </div>
<?php endif;?>
<!-- EOF Assigments-->
