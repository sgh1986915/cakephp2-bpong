<?php echo $this->Html->css('jquery.tabs'); ?>
<?php echo $this->Html->script(array('jquery.tabs.min.js')); ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#tabsmenu').tabs();
//MANAGER VALIDATION
	$("#TeamAssignment").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: function(){$('#Loading').show('slow');$('#TeamsList').hide('slow');},
                    success: showResponse
                });
        }
	});
	//EOF MANAGER Validation
});
//EOF ready
function showResponse(responseText)  {
   		 $('#Loading').hide('slow');
 		 //$("#SubmitButton").show('slow');

	  if (responseText==""){
	  		$('#ERROR').show('slow');
	  } else {
	  		$('#TeamsList').html(responseText);
	  		$('#TeamsList').show('slow');
	  }
}
</script>

<h2>Teams for the <?php echo $model?> <?php echo $modelIformation[$model]['name']?></h2>
<div id="tabsmenu">
            <ul>
                <li><a href="#fragment-1"><span>Filter</span></a></li>
                <li><a href="#fragment-2"><span>Assign new team</span></a></li>
            </ul>	
	
    <div id="fragment-1">
            <?php echo $this->Form->create('Team',array('id'=>'TeamFilter','name'=>'TeamFilter','url'=>'/teams/'.$this->request->action.'/'.$modelIformation[$model]['slug']));?>
            <fieldset>
            <?php echo $this->Form->input('TeamFilter.name',array('label'=>'Name LIKE'));?>
                <div class="clear"></div>
			<?php echo $this->Form->input('TeamFilter.status');?>
            </fieldset>
    <div class="clear"></div>
            <?php echo $this->Form->end('Filter');?>
    </div> 
    <div class="clear"></div>
    <div id="fragment-2">   
    	 <!-- ASSIGN TEAM  -->
            <fieldset>
			<?php echo $this->Form->create('Team',array('id'=>'TeamAssignment','name'=>'TeamAssignment','url'=>'/teams/FindByName'));?>
			<?php 
			    echo $this->Form->hidden('TeamAssignment.model', array('value' => $model));
    			echo $this->Form->hidden('TeamAssignment.model_id', array('value' => $modelIformation[$model]['id']));
			?>
            <?php echo $this->Form->input('TeamAssignment.name',array('label'=>'Team name'));?>
            </fieldset>
			    <div class="clear"></div>
			<?php echo $this->Form->end('Search');?>
            </div>
            <div id="ERROR" style="display: none;">Can't find such user.</div>
    		<div id="TeamsList" style="display: none;"><!-- Ajax  --></div>
    		<div id="Loading" style="display:none">
            <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
            
        <!-- EOF ASSIGN -->              
    </div>
</div>

<?php if (!empty($teams)): ?>
<table>
<tr>
  <th>Name</th>
  <th>People in team</th>
  <th>Status</th>
  <th>Created</th>
  <th></th>
</tr>
<?php foreach ($teams as $team): ?>
<tr>
  <td> <?php echo $team['Team']['name'] ?></td>
  <td> <?php echo $team['Team']['people_in_team'] ?></td>
  <td> <?php echo $team['Team']['status'] ?></td>
  <td> <?php echo $this->Time->niceDate($team['Team']['created']) ?> </td>
  <td> <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View team details" title="View team details"/>', array('action'=>'view' ,$team['Team']['slug'], $team['Team']['id']), array('escape'=>false)); ?>
       <?php if ($team['Team']['status']!="Deleted"):?>
       <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete this team" title="Delete this team"/>', array('action'=>'delete', $team['Team']['id']), array('escape'=>false),'Are you sure you want to delete this team?'); ?>
       <?php endif;?>
  </td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<?php else:?>
<div class="you_have_no">There are no teams for such creteria</div>
<?php endif;?>
