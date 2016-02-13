<?php 
$name = '';
if (!empty($item['description'])) {
	$name = $item['description'];	
}
if (!empty($item['name'])) {
	$name = $item['name'];	
}
if (!empty($item['title'])) {
	$name = $item['title'];	
}
if (!empty($item['filename']) && empty($item['youtube_id'])) {
	$item['youtube_id'] = $item['filename'];
} elseif (empty($item['youtube_id'])) {
	$item['youtube_id'] = '';
}
?>
<td style='padding-top:5px; padding-bottom:5px;'>
		<a href="/video/<?php echo $item['id'];?>"><img src="<?php echo $this->Youtube->getVideoImage($item['youtube_id']);?>" alt="" style='margin-right:5px;'></a>	
</td>
<td style="text-align: left;">
	<?php if ($name):?>
	<a href="/video/<?php echo $item['id'];?>"><b><?php echo $this->Formater->stringCut($name, 70);?></b></a>
	<?php endif;?>
</td>