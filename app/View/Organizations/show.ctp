<?php $showNewsCount = 3;?>

<script type="text/javascript">
var currentNewsBlock = 1;
function olderNews () {
	var countNewsBlocks = <?php echo ceil(count($news)/$showNewsCount);?>;
	currentNewsBlock = currentNewsBlock + 1;
	$('.news_block_' + currentNewsBlock).slideDown();
	if (countNewsBlocks == currentNewsBlock) {
		$('#older_news').slideUp();
	}
	return false;
}

</script>

<table><tr>
<?php if (!empty($organization['Image']['filename'])):?>
<td style='padding-right:10px;vertical-align:top;'>
	<img src="<?php echo IMG_MODELS_URL;?>/middle_<?php echo $organization['Image']['filename'];?>" alt="<?php echo $organization['Organization']['name'];?>" border="0" />
</td>
<?php endif;?>
<?php if (!empty($organization['Organization']['about'])):?>
<td  style='vertical-align:top;'>
	<div style='border-bottom: 1px solid #CCCCCC;width:100%;margin-bottom:10px;'>
		<div style='float:left;font-size:17px;color:#D61C20;font-weight:bold;'>About <?php echo $organization['Organization']['name'];?></div>
		<div style='float:right;font-size:14px;font-weight:bold;'>
			<?php if ($organization['Address']['city']):?><?php echo $organization['Address']['city'];?><?php endif;?><?php if (!empty($organization['Address']['Provincestate']['shortname']) && !empty($organization['Address']['city'])):?>,<?php endif;?>
			<?php if (!empty($organization['Address']['Provincestate']['shortname'])):?><?php if (is_numeric($organization['Address']['Provincestate']['shortname'])) { echo $organization['Address']['Provincestate']['name'];}else{ echo $organization['Address']['Provincestate']['shortname'];};?><?php endif;?>
		</div>
		<div class='clear'></div>
	</div>
	<?php echo $this->Formater->stringCut($organization['Organization']['about'], 700, '...<div class="clear" style="height:10px;"></div><a href="/o_about/' . $organization['Organization']['slug'] . '"><span class="red_button_link">Read more...</span></a>', 0);?>

</td>
<?php endif;?>
</tr></table>

<!-- ORG OBJECTS -->
<div style='float:left;width:37%;margin-right:20px;margin-top:20px;'>

<?php if (!empty($albums)):?>
	<div style='width:100%;margin-bottom:10px;' class='red_header'>
		<div class='red_header_name'>Albums</div>
		<div class='red_header_link'><a href="/o_albums/<?php echo $organization['Organization']['slug'];?>">vew all ></a></div>
	</div>
	<table style='width:100%;' class='solid_table' cellspacing="0" cellpadding="0" border="0">
	<?php
	$i = 0;
	foreach ($albums as $album):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray" ';
	  }
	?>
		<tr<?php echo $class;?>>
		<?php if ($album['Album']['content_type'] == 'image'):?>
			<td><?php if (!empty($album['CoverImage']['filename'])):?><a href="/albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"><img src="<?php echo IMG_ALBUMS_URL;?>/small_<?php echo $album['CoverImage']['filename'];?>" alt=""/></a><?php endif;?></td>
			<td><a href="/albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"><?php echo $album['Album']['name'];?></a></td>
		<?php else:?>
			<td><?php if (!empty($album['CoverVideo']['id'])):?><a href="/albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"><img width="70" src="<?php echo $this->Youtube->getVideoImage($album['CoverVideo']['youtube_id']);?>" alt=""/></a><?php endif;?></td>
			<td><a href="/albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"><?php echo $album['Album']['name'];?></a></td>
		<?php endif;?>
		</tr>
	<?php endforeach;?>
	</table>
<br/>
<?php endif;?>


<?php if (!empty($events)):?>
	<div style='width:100%;margin-bottom:10px;' class='red_header'>
		<div class='red_header_name'>Events</div>
		<div class='red_header_link'><a href="/o_events/<?php echo $organization['Organization']['slug'];?>">vew all ></a></div>
	</div>
	<table style='width:100%;' class='solid_table' cellspacing="0" cellpadding="0" border="0">
	<?php
	$i = 0;
	foreach ($events as $event):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray" ';
	  }
	?>
		<tr<?php echo $class;?>>
			<td><a href="/event/<?php echo $event['Event']['id'];?>/<?php echo $event['Event']['slug'];?>"><?php echo $event['Event']['name'];?></a></td>
			<td><?php echo empty($event['Event']['start_date'])?"--":$this->Time->niceShort($event['Event']['start_date']); ?></td>

		</tr>
	<?php endforeach;?>
	</table>
<br/>
<?php endif;?>

<?php if (!empty($venues)):?>
	<div style='width:100%;margin-bottom:10px;' class='red_header'>
		<div class='red_header_name'>Venues</div>
		<div class='red_header_link'><a href="/o_venues/<?php echo $organization['Organization']['slug'];?>">vew all ></a></div>
	</div>
	<table style='width:100%;' class='solid_table' cellspacing="0" cellpadding="0" border="0">
	<?php
	$i = 0;
	foreach ($venues as $venue):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray" ';
	  }
	?>
		<tr<?php echo $class;?>>
			<td><a href="/venues/view/<?php echo $venue['Venue']['slug'];?>"><?php echo $venue['Venue']['name'];?></a></td>
			<td><?php echo $venue['Venue']['Address']['city'];?></td>
			<td><?php if (!empty($venue['Venue']['Address']['Provincestate']['shortname'])):?><?php echo $venue['Venue']['Address']['Provincestate']['shortname'];?><?php endif;?></td>
		</tr>
	<?php endforeach;?>
	</table>
<br/>
<?php endif;?>

</div>

<!-- ORG NEWS -->
<div style='float:left;width:60%;margin-top:20px;'>
<?php if (!empty($news)):?>
	<div style='width:100%;margin-bottom:10px;' class='red_header'>
		<div class='red_header_name'>News</div>
		<div class='red_header_link'><a href="/o_news/<?php echo $organization['Organization']['slug'];?>">vew all ></a></div>
	</div>
<?php
$i = 0;
$j = 1;

foreach ($news as $new):?>
<div style='width:100%; border-bottom:1px solid #CCCCCC;margin-bottom:15px;padding-bottom:5px;<?php if($j>1):?>display:none;<?php endif;?>' class='news_block_<?php echo $j;?>'>
	<h2><?php echo $new['OrganizationNews']['title'];?></h2>

<?php if (!empty($new['Image']['filename'])):?>
	<img style='margin-right:10px; float:left;' src="<?php echo IMG_MODELS_URL;?>/little_<?php echo $new['Image']['filename'];?>" alt="" border="0" />
<?php endif;?>

	<?php echo $new['OrganizationNews']['body'];?>
	<div style='width:100%;clear:both; height:20px;'> </div>
	Posted: <?php echo $this->Time->niceShort($new['OrganizationNews']['created']); ?> by <a href="/u/<?php echo $new['User']['lgn'] ?>"><?php echo $this->Formater->userName($new['User'], 1); ?></a>
    <?php if($canEditNews) echo $this->Html->link('<img alt="edit" src="/img/smalledit.gif" />', Router::url(array('controller' => 'organization_news', 'action'=>'edit', $new['OrganizationNews']['id'])), array('escape' => false), null, false); ?>&nbsp;
    <?php if($canDeleteNews) echo $this->Html->link('<img alt="edit" src="/img/smalldelete.gif" />', Router::url(array('controller' => 'organization_news', 'action'=>'delete', $new['OrganizationNews']['id'])), array('escape' => false), "Are you sure?", false); ?>
</div>
	<?php
	$i++;
	if ($i == $showNewsCount) {
		$i = 0;
		$j++;
	}
	?>
    <?php endforeach;?>
<br/>
<div id='older_news' style='<?php if ($showNewsCount >= count($news)):?> display:none;<?php endif;?>' onclick='return olderNews();'>Oleder Posts &#9660;</div>
<?php endif;?>
</div>
