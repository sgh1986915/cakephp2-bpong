<?php $this->pageTitle = 'Edit Video'; ?>
<h2>Edit Video</h2>
<?php echo $this->Form->create('Video',array('enctype'=>"multipart/form-data"));?>
 		<br/>
 			<div class="input text">
	<?php
	    echo $this->Form->hidden('id');
    	if ($this->request->data['Video']['title']):	   
    	    echo $this->Form->input('title', array('type' => 'text', 'label' => 'Title', 'style' => 'width:400px;'));
    	endif;?>
    	</div>
	<div class="input text">
    	<?php
    	if ($this->request->data['Video']['code']):
    	    echo $this->Form->input('code', array('type' => 'textarea', 'label' => 'Embed Code', 'div' => false, 'style' => 'width:400px; height:100px;'));   
    	endif;		
    	?></div>   	
	<div class="input text">
    <?php    
    	echo $this->Form->input('description', array('type' => 'textarea', 'label' => 'Description', 'div' => false, 'style' => 'width:400px; height:50px;'));
    ?>
    </div>
 		<!--  SET Authors TAGS --><?php echo $this->element('/tags/set_authors', array("modelName" => "Video", 'authorID' => $this->request->data['Video']['user_id'], 'tags' => $this->request->data['Tag']));?>
   
<br class='clear'/><br class='clear'/>		
<?php echo $this->Form->end('Submit');?>		 
