<script type="text/javascript">
    function imageLoader() {
    	$("#image_loader").show();
    	
    }
</script>
<h2>Upload images to album "<?php echo $album['name'];?>"</h2>
<div style='float:left;'>
<?php echo $this->Form->create('Image',array('enctype'=>"multipart/form-data",'url'=>'/Images/albumAdd/' . $album['id'], 'onsubmit' => 'imageLoader();'));?>
	<?php
    	echo $this->Form->input('Image.1', array('type' => 'file', 'class' => 'file', 'label' => 'Image 1'));
    	echo $this->Form->input('Image.2', array('type' => 'file', 'class' => 'file', 'label' => 'Image 2'));
    	echo $this->Form->input('Image.3', array('type' => 'file', 'class' => 'file', 'label' => 'Image 3'));  
    	echo $this->Form->input('Image.4', array('type' => 'file', 'class' => 'file', 'label' => 'Image 4')); 
    	echo $this->Form->input('Image.5', array('type' => 'file', 'class' => 'file', 'label' => 'Image 5'));    
    ?>
    <span style='font-size:90%;'>Image types allowed: jpg, gif, png. Maximum  image size: 3MB</span><br/><br/><br/>
    <?php echo $this->Form->end('Submit');?>
</div>
<div style='float:left;margin-left:80px;margin-top:40px;'>
<img src="/img/ajax-loader.gif" border="0" id='image_loader' style='display:none;'>
</div>
<br class='clear' />
<br class='clear' />