<?php if (!isset($item['name']) && isset($item['title'])){
	$item['name'] = $item['title'];
}
?>

<td style="text-align: left; padding-left: 14px" colspan = 2>
	<br/>
	<a href="/links/show/<?php echo $item['id'];?>"><b><?php echo $item['name'];?></b></a>
	<br/> <?php echo $item['description'];?><br/><br/>
</td>