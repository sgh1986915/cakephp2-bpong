<?php

$content['file']['image'] = 'Photo';
$content['file']['video'] = 'Video';
$content['file']['junk'] = 'File';

$content['files']['image'] = 'Photos';
$content['files']['video'] = 'Videos';
$content['files']['junk'] = 'Files';
?>

<?php if ($canUpload) :?>
<?php echo $this->Html->link('Create a Photo Album', '/Albums/add/image/' . $albumModel . '/' . $albumModelID);?>&nbsp;&nbsp;
<?php echo $this->Html->link('Create a Video Album', '/Albums/add/video/' . $albumModel . '/' . $albumModelID);?>
<?php endif;?>
<br/>

<?php if (!empty($albums)) {?>
<table>
	<?php
    $i = 0; $j = 0;
    $countAlbums = count($albums);
	foreach ($albums as $album):
	$i++; $j++;
	?>
	<tr><td width='150px' valign='top' style='border:1px solid #C3C3C3;'>
  		<a href="/Albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>">
  		<?php if ($album['CoverImage']['filename']) { ?>
			<img src="<?php echo IMG_ALBUMS_URL;?>/thumb_<?php echo $album['CoverImage']['filename'];?>"/>
		<?php } else { ?>
			<img src="<?php echo STATIC_BPONG?>/img/photo_un_big.gif"/>
		<?php }?>
</a></td>
<td valign='top'  style='border:1px solid #C3C3C3;'>
<a href="/Albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"><b><?php echo $album['Album']['name'];?></b></a>
<br/>
<?php echo $album['Album']['files_num'];?> <?php if ($album['Album']['files_num']>1){ echo $content['files'][$album['Album']['content_type']]; } else { echo $content['file'][$album['Album']['content_type']]; }?><br/>
<?php if($album['Album']['description']):?><?php echo $album['Album']['description'];?><br/><?php endif;?>
<?php if ($this->Time->niceDateJS($album['Album']['created']) != $this->Time->niceDateJS($album['Album']['modified'])):?>
Updated <?php echo $this->Time->niceDateJS($album['Album']['modified']); ?><br/>
<?php endif;?>
Created <?php echo $this->Time->niceDateJS($album['Album']['created']); ?><br/>
Creator: <?php echo $this->Html->link($album['User']['lgn'], '/users/view/' . $album['User']['lgn']);?><br/>
<br/>
<?php echo $this->Html->link('View Album', '/Albums/show_' . $album['Album']['content_type'] . '/' . $album['Album']['id']);?>

<?php if ($userSession['id'] == $album['Album']['user_id']): ?>
    | <?php echo $this->Html->link('Edit Album', '/Albums/edit/' . $album['Album']['id']);?>
    | <?php echo $this->Html->link('Delete Album', '/Albums/delete/' . $album['Album']['id'], null, 'Are you sure you want to delete album?');?>
<?php endif;?>

</td>
</tr>

    <?php endforeach;?>
</table>
<?php } else { ?>
	No Albums
<?php } ?>
