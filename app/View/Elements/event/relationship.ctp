<script type="text/javascript">
function findParents(){
	 $('#LoadingRelation').show();
	 $('#ParentInformation').hide('slow');
	
	$.post('/events/findParents',{'data[Event][find]':$('#event_name').val(), 'data[Event][id]':$('#event_id').val()},function(responseText) {
		 $('#LoadingRelation').hide('slow');
 		 $("#SubmitButtonRelation").show('slow');

		 $('#ParentInformation').html(responseText);
		 $('#ParentInformation').show('slow');
		});
	return false;
}


function addRelation(parentEventID){
	if (confirm('Are you sure you want to add relationship?')){
		$('#ParentInformation').hide('slow');
		$('#LoadingRelation').show();
		$('#parents_list').hide();
		var relationType = $('#relation_type_' + parentEventID + " option:selected").val();
		$.post('/events_events/addRelation/<?php echo $this->request->data['Event']['id'];?>/' + parentEventID + '/' + relationType,{},function(responseText) {
			$('#parents_list').load('/events_events/parentsList/<?php echo $this->request->data['Event']['id'];?>', function() {
					 $('#LoadingRelation').hide('slow');
					 $('#parents_list').show();
			});
		});
	}
	return false;
}
function deleteRelation(relationID){
	if (confirm('Are you sure you want to delete relationship?')){
		$('#LoadingRelation').show();
		$('#parents_list').hide();
		$.post('/events_events/delete/'+relationID,{},function(responseText) {
			$('#parents_list').load('/events_events/parentsList/<?php echo $this->request->data['Event']['id'];?>', function() {
				 $('#LoadingRelation').hide('slow');
				 $('#parents_list').show();
			});
		});
	}
	return false;
}
</script>



<h3>Add new Relation</h3>
<fieldset class="fieldbox">
    <?php
    	echo $this->Form->hidden('Event.id', array('value' => $this->request->data['Event']['id'], 'id' => 'event_id'));
    	echo $this->Form->input('Event.find',array('id' => 'event_name', 'style' => 'width:500px', 'label' => 'Parents name like:', 'maxlength' => 300));
    ?>
    		<div id="ParentInformation" style="display: none;"><!-- Ajax  --></div>
</fieldset>
    <div id="SubmitButtonRelation" class="submit input">
    	<input type="button" name="Find" value="Find" onclick="return findParents();"/>
    </div>
    <div class="heightpad"></div>
    
    <div id="LoadingRelation" style="display:none">
    	<?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
    </div>
<div id="parents_list"><?php echo $this->element('event/parents_list');?></div> 