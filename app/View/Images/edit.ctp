<div class="editimages form">
<?php echo $this->Form->create('Image',array('id' => 'image_form', 'enctype'=>"multipart/form-data"));?>
	<fieldset>
 		<legend>Edit Image</legend>
 		<br/>
 		<a href="<?php echo  STATIC_BPONG ?>/img/<?php echo $this->request->data['Image']['model'] ?>/<?php echo $this->request->data['Image']['filename'] ?>" title="<?php echo $this->request->data['Image']['title'] ?>"  class="thickbox">
			<img src="<?php echo  STATIC_BPONG ?>/img/<?php echo $this->request->data['Image']['model'] ?>/thumbs/<?php echo $this->request->data['Image']['filename'] ?>" alt="<?php echo $this->request->data['Image']['alt'] ?>" >
        </a>
	<?php
	    echo $this->Form->hidden('Image.' . $this->request->data['Image']['id'] . '.prop',array("value"=>$this->request->data['Image']['prop']));
    	echo $this->Form->input('Image.' . $this->request->data['Image']['id'], array('type' => 'file', 'class' => 'file', 'label' => 'Edit Image'));
		echo $this->Form->hidden('Form.back_url', array('value' => $back_url));
		if($show_attributes){		
			echo $this->Form->input('Image.' . $this->request->data['Image']['id'] . '.title', array('size' => 100));
			echo $this->Form->input('Image.' . $this->request->data['Image']['id'] . '.alt', array('size' => 100, 'label' => 'Alternative text'));
			echo $this->Form->input('Image.' . $this->request->data['Image']['id'] . '.description', array('label' => 'Description'));
		}
		echo $this->Form->end('Submit');
		 ?>
		 </fieldset>
</div>