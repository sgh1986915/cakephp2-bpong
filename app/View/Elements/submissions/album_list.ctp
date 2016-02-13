<script type="text/javascript">
	$(document).ready(function(){
		$('#albumID').sSelect();
	});
</script>
<?php 
    if (!isset($album_id)) {
        $album_id = '0';
    }
?>
<br/>
<b>Select An Album</b>
<?php echo $this->Form->input('albumID', array('id' => 'albumID', 'label' => false, 'div' => false, 'type' => 'select',  'class' => 'custom-select', 'onchange' => 'selectAlbumName();', 
'options' => array('0' => 'Select One') + $albums, 'selected' => $album_id)); ?>