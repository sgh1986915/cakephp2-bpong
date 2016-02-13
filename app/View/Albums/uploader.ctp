<h2>Upload Content</h2>

<div style='float:left;vertical-align:top;'>
	What are you uploading?
</div>
<div style='float:left;vertical-align:top;margin-left:20px;'>
<?php echo $this->Form->input('content_type', array('label' => false, 'style' => 'width:100px', 'div' => false, 'type' => 'select', 'options' => array('image' => 'An Image', 'link' => 'A Link', 'video' => 'A Video')));?>
</div>

<div style='float:left;vertical-align:top;margin-left:20px;'>
	Do you want to create a new album<br/>
	for this image or it by itself?
</div>

<div style='float:left;vertical-align:top;margin-left:20px;'>
<?php echo $this->Form->input('album_type', array('label' => false, 'style' => 'width:100px', 'div' => false, 'type' => 'select', 'options' => array('image' => 'By Itself', 'link' => 'New Album', 'video' => 'Existing Album')));?>
</div>


