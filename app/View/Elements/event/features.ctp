<?php if (!empty($edit)):?>
	<?php echo $this->Form->create('Event',array('enctype'=>"multipart/form-data",'id'=>'EventEditForm','name'=>'EventEditForm','action'=>'edit'));?><?php echo $this->Form->hidden('id');	?>
<?php endif;?> 
<?php
		$checked_array = array();
		if (isset($this->request->data['Eventfeature'] )) {
				foreach ($this->request->data['Eventfeature'] as $checkedvalue) {
					if (isset($checkedvalue['id'])) {$checked_array[] = $checkedvalue['id'];} else {$checked_array = $checkedvalue;}
				}

		}?>
<?php /* foreach ($eventfeatures as $id=>$value):?>

<div class="box">
  <label for="EventfeatureEventfeature<?php echo $id; ?>"><?php echo $value; ?></label>
  <div class="check">
    <?php echo $this->Form->checkbox('Eventfeature.id.['.$id.']', array('value' => $id,
       'name'=>'data[Eventfeature][Eventfeature][]',
       'checked'=>(in_array( $id, $checked_array) ? 'checked' : false),
	    'id'=>'EventfeatureEventfeature'.$id,
	    'label'=>false
    )); ?>
  </div>
</div>
<?php endforeach; */?>
<?php
if(!isset($defaultText)) {
    $defaultText = '';
}
?>
<div class="text_descript"><?php echo $this->Form->input('Event.prize');?></div>
<div class="text_descript"><?php echo $this->Form->input('Event.other');?></div>

<?php if (!empty($edit)):?>
  <div class="heightpad"></div>
  <?php echo $this->Form->end('Submit');?>
<?php endif;?> 