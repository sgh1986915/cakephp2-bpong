<script type="text/javascript">
function findManager(){
	 $('#Loading').show();
	 $('#ManagerInformation').hide('slow');
	
	$.post('/managers/find',
    {
     'data[Manager][lgn]':$('#ManagerLgn').val(),
     'data[Manager][lastname]':$('#ManagerLastname').val(),   
     'data[Manager][email]':$('#ManagerEmail').val(),
     'data[Manager][model]':$('#ManagerModel').val(),
     'data[Manager][model_id]':$('#ManagerModelId').val()},function(responseText) {
		 $('#Loading').hide('slow');
 		 $("#SubmitButton").show('slow');

		 $('#ManagerInformation').html(responseText);
		 $('#ManagerInformation').show('slow');
		});
	return false;
}
function removeManager(id){
	if (confirm('Are you sure you want to remove manager?')){
		$('#Loading').show();
		$('#managers_list').hide();
        <?php if (isset($this->request->data['Event'])): ?>
            $.post('/managers/remove/Event/<?php echo $this->request->data['Event']['id'];?>/'+id,{},function(responseText) {
                $('#managers_list').load('/events/ajaxShowManagers/<?php echo $this->request->data['Event']['id'];?>', function() {
                     $('#Loading').hide('slow');
                     $('#managers_list').show();
                });
            });            
        <?php endif; ?>
        <?php if (isset($this->request->data['Venue'])): ?>   
            $.post('/managers/remove/Venue/<?php echo $this->request->data['Venue']['id'];?>/'+id,{},function(responseText) {
                $('#managers_list').load('/venues/ajaxShowManagers/<?php echo $this->request->data['Venue']['id'];?>', function() {
                         $('#Loading').hide('slow');
                         $('#managers_list').show();
                });
            });
        <?php endif; ?>
	}
	return false;
}

function assignManager(model, email, modelID){
	if (confirm('Are you sure you want to assign manager?')){
		$('#ManagerInformation').hide('slow');
		$('#Loading').show();
		$('#managers_list').hide();
        <?php if (isset($this->request->data['Event'])): ?>
		    $.post('/managers/assignManager/'+ model +'/' + email + '/' + modelID,{},function(responseText) {
			    $('#managers_list').load('/events/ajaxShowManagers/<?php echo $this->request->data['Event']['id'];?>', function() {
					     $('#Loading').hide('slow');
					     $('#managers_list').show();
			    });
		    });        
        <?php endif; ?>
        <?php if (isset($this->request->data['Venue'])): ?>
            $.post('/managers/assignManager/'+ model +'/' + email + '/' + modelID,{},function(responseText) {
                $('#managers_list').load('/venues/ajaxShowManagers/<?php echo $this->request->data['Venue']['id'];?>', function() {
                         $('#Loading').hide('slow');
                         $('#managers_list').show();
                });
            });
        <?php endif; ?>
    }
	return false;
}
</script>
<h3>Add new Manager</h3>
<fieldset class="fieldbox">
    <?php
        if (isset($this->request->data['Event']['id'])) {
    	    echo $this->Form->hidden('Manager.model', array('value' => 'Event'));
    	    echo $this->Form->hidden('Manager.model_id', array('value' => $this->request->data['Event']['id']));
    	}
        if (isset($this->request->data['Venue']['id'])) {
            echo $this->Form->hidden('Manager.model', array('value' => 'Venue'));
            echo $this->Form->hidden('Manager.model_id', array('value' => $this->request->data['Venue']['id']));
        }
        echo $this->Form->input('Manager.email',array('size' => 40,'onsubmit'=>'return findManager();'));
        echo $this->Form->input('Manager.lgn',array('label'=>'Username','size'=>40,'onsubmit'=>'return findManager();'));
        echo $this->Form->input('Manager.lastname',array('size'=>40,'onsubmit'=>'return findManager();'));
        
    ?>
    		<div id="ManagerInformation" style="display: none;"><!-- Ajax  --></div>
</fieldset>
    <div id="SubmitButton" class="submit input">
    	<input type="button" name="Find" value="Find" onclick="return findManager();"/>
    </div>
    <div class="heightpad"></div>
    
    <div id="Loading" style="display:none">
    	<?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
    </div>
    <div id="managers_list"><?php echo $this->element('managers_list');?></div> 