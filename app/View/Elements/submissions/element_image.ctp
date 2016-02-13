<td style='padding:10px 5px;'>
	<a href="/Images/albumShow/<?php echo $item['id'];?>"><img src="<?php echo IMG_ALBUMS_URL;?>/small_<?php echo $item['filename'];?>" alt=""/></a>
</td>
<td style="text-align: left; padding-left: 14px">
    <a href="/Images/albumShow/<?php echo $item['id'];?>"><b><?php echo $this->Formater->stringCut($item['name'], 100);?></b></a>
	 <?php if ($item['album_name']):?>
    	<br/><b>Album:</b> <?php echo $item['album_name'];?>
    <?php endif;?>
    <br/><br/><?php echo $this->Formater->stringCut($item['description'], 200);?>
</td>