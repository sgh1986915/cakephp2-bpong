<h2>You have uploaded <?php echo count($images)?> Images</h2>
<?php echo $this->Form->create('Image',array('enctype'=>"multipart/form-data",'url'=>'/Images/albumAddFinish/' . $album['Album']['id']));?>
<table width='100%'>
<?php foreach ($images as $img):?>
<tr>
<td>
<?php echo $this->Form->input('descriptions.' . $img['Image']['id'], array('label' => 'Description', 'type' => 'textarea')); ?>
</td>
<td>
	<img src="<?php echo IMG_ALBUMS_URL;?>/thumb_<?php echo $img['Image']['filename'];?>"/>
	<br/>
	<input name="data[cover]" type="radio" value="<?php echo $img['Image']['id'];?>">
	This is the album cover.
</td>
<?php endforeach;?>
</table>
<?php echo $this->Form->end('Save');?>