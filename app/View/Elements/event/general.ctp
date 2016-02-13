<?php if (!empty($edit)):?>
	<?php echo $this->Form->create('Event',array('enctype'=>"multipart/form-data",'id'=>'EventEditForm','name'=>'EventEditForm','action'=>'edit'));?><?php echo $this->Form->hidden('id');	?>
<?php endif;?> 
<script type="text/javascript">
	function satelliteRequestMessage() {
		if ($('#satellite_request').attr('checked')) {
			alert("This will require approval");
		}
	}
</script>
<?php
    if (isset($this->request->data['Event']['start_time']) && isset($this->request->data['Event']['end_time'])) {
		$startTimeSelected = $this->request->data['Event']['start_time'];
		$endTimeSelected = $this->request->data['Event']['end_time'];
	} else {
		$startTimeSelected = $endTimeSelected = false;
	}
    echo $this->Form->input('Event.name', array('size' => 70, 'label' => 'Name <span class="red">*</span>'));
    echo $this->Form->input('shortname', array('size' => 50, 'label' => 'Short Name'));
    ?>
    <?php if (!empty($accessApprove)):?>
    	<?php     echo $this->Form->input('Event.type',array('type' => 'select','label'=>'Type','options' => Configure::read('Event.Types')));?>
    <?php endif;?>
    <?php
    echo $this->Form->input('Event.description', array('label'=>false));
    echo $this->Form->input('Event.timezone_id',array('type' => 'select', 'label' => 'Time zone <span class="red">*</span>','options' => array('0' => 'Select a Time Zone') + $timeZones));
    echo $this->Form->input('Event.start_date_',array('type'=> 'text', 'size' => 10 ,'class' => 'date-pick', 'label' => 'Start Date <span class="red">*</span>'));
    echo $this->Form->input('Event.start_time',array('type'=>'time', 'selected' => $startTimeSelected, 'interval' => 15));
    echo $this->Form->input('Event.end_date_',array('type'=> 'text', 'size' => 10, 'label' => 'End Date <span class="red">*</span>', 'class' => 'date-pick dp-applied',  'error' => 'End date must be later then start date'));
    echo $this->Form->input('Event.end_time',array('type'=>'time', 'selected' => $endTimeSelected, 'interval' => 15));
    echo $this->Form->input('Event.url', array('size' => 30));
    echo $this->Form->input('Event.cost', array('size' => 30));
 ?>
<!--  SET Author TAGS -->
<?php if(isset($this->request->data['Tag'])) {
    $tags = $this->request->data['Tag'];
} else {
    $tags = '';
}
echo $this->element('/tags/set_authors', array("modelName" => "Event", 'authorID' => false, 'tags' => $tags, 'label' => 'Tags')); ?>
<?php if ($action == 'add'):?>
<div style='width:100%; text-align:center;'>
	Make this event a Satellite Tournament of The World Series of Beer Pong - <?php echo $this->Form->input('Event.satellite_request', array('id' => 'satellite_request', 'type' => 'checkbox', 'label' => false, 'div' => false, 'onchange' => "satelliteRequestMessage();"));?>
</div>
<?php endif;?>
<?php if (!empty($offimage)): ?>
    <div style="display: inline-block">
	<div class="image-wrapper" style="float:left; margin-right: 20px;">
	    <?php echo $this->Image->manageImages('Event', false, $offimage , 1, array());?>
	</div>
	<?php
	    echo '<strong>Name:</strong> '.$offimage[0]['Image']['filename'].'<br/>';
	    echo '<strong>Size:</strong>  '.$offimage[0]['Image']['width'].' x '.$offimage[0]['Image']['height'];
	?>
    </div>
<?php else:
    echo $this->Form->input('Image.new',array('type' => 'file','class'=>'file','label'=>'Image'));
    echo $this->Form->hidden('Image.new.prop',array('value'=>'Personal'));
endif; ?>

<?php if (!empty($edit)):?>
  <div class="heightpad"></div>
  <?php echo $this->Form->end('Submit');?>
<?php endif;?> 