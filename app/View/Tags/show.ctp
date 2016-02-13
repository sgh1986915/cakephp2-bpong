<?php
	echo $this->Html->css(array('stylish-select.css'));
	echo $this->Html->script('jquery.stylish-select.min.js');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.custom-select').sSelect();
	});
</script>
<script type="text/javascript" src='<?php echo STATIC_BPONG ?>/js/votes/votes.js'></script>
<?php $this->pageTitle = 'Beer Pong Tag - ' . $tag['Tag']['tag']; ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<script type="text/javascript">
	function submitTagForm() {
		var formAction = '';
		var tagID = <?php echo $tag['Tag']['id'];?>;
		var search = $('#tagSearch').val();
		formAction = '/tag/' + tagID + '/' + $('#modelName').val();
		if (search) {
			formAction = formAction + '/search/';
		}
		$('#TagForm').attr('action', formAction);
		return true;
	}
</script>


<?php
    $prevNums = ($this->Paginator->current() - 1) * $limit;
?>
<h2>All <?php echo $model . 's';?> Tagged With "<?php echo $tag['Tag']['tag'];?>"</h2>
<br class='clear'/>
<?php echo $this->Form->create('Tag',array('id'=>'TagForm','name'=>'TagForm','action'=>'index', 'onsubmit' => 'return submitTagForm();'));?>
<span class='red'>Switch to: &nbsp;&nbsp;</span> <?php echo $this->Form->input('model',array('div' => false, 'class' => 'custom-select', 'type' => 'select','label'=> false, 'id' => 'modelName' , 'onchange' => "$('#TagForm').submit();", 'options' => array('Album' => 'Albums', 'Image' => 'Images', 'Video' => 'Videos', 'Link' => 'Links', 'Event' => 'Events')));?>
<?php /*echo $this->Form->input('search',array('label'=>"Search:", 'id' => 'tagSearch'));*/?>
<?php /*echo $this->Form->end('Submit');*/?>
</form>

<br/>
<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter'>
<tr>
	<th><strong>#</strong></th>

	<?php if (!empty($modelOptions[$model]['calumns']['Title & Description'])):?>
		<th><?php echo $this->Paginator->sort('Title & Description', $modelOptions[$model]['calumns']['Title & Description'], array('sorter' => true));?></th>
	<?php endif;?>

	<?php if (!empty($modelOptions[$model]['calumns']['Upranks'])):?>
		<th><?php echo $this->Paginator->sort('Upranks', $modelOptions[$model]['calumns']['Upranks'], array('sorter' => true));?></th>
	<?php endif;?>

	<?php if (!empty($modelOptions[$model]['calumns']['Downranks'])):?>
		<th><?php echo $this->Paginator->sort('Downranks', $modelOptions[$model]['calumns']['Downranks'], array('sorter' => true));?></th>
	<?php endif;?>

    <?php if (!empty($modelOptions[$model]['calumns']['Comments'])):?>
		<th><?php echo $this->Paginator->sort('Comments', $modelOptions[$model]['calumns']['Comments'], array('sorter' => true));?></th>
	<?php endif;?>

	<?php if (!empty($modelOptions[$model]['calumns']['Submitted By'])):?>
		<th><?php echo $this->Paginator->sort('Submitted By', $modelOptions[$model]['calumns']['Submitted By'], array('sorter' => true));?></th>
	<?php endif;?>

	<?php if (!empty($modelOptions[$model]['calumns']['Submitted'])):?>
		<th><?php echo $this->Paginator->sort('Submitted', $modelOptions[$model]['calumns']['Submitted'], array('sorter' => true));?></th>
	<?php endif;?>
	<th><strong>Tags</strong></th>
</tr>
<?php
$i = 0;
$k = 0;
foreach ($items as $item):
	$class = null;
	if ($i++ % 2 != 0) {
		$class = ' class="gray_bg"';
	}
	$k++;
?>
	<tr<?php echo $class;?>>
		<td class='center'><?php echo $prevNums + $k;?></td>

	    <?php if (!empty($modelOptions[$model]['calumns']['Title & Description'])):?>
<td class='center' style='padding:10px'>
			<?php switch ($model) {
case 'Image': ?>
<div style='float:left;'>
    <a href="/Images/albumShow/<?php echo $item[$model]['id'];?>">
    	<img src="<?php echo IMG_ALBUMS_URL;?>/small_<?php echo $item[$model]['filename'];?>" alt="<?php echo $item[$model]['name'];?>" />
    </a>
</div>
<div style='float:left;height:100%;margin-left:20px;'>
    <?php if ($item[$model]['name']):?>    <a href="/Images/albumShow/<?php echo $item[$model]['id'];?>"> <strong><?php echo $item[$model]['name'];?></strong></a><br/><?php endif;?>
    <?php echo $item[$model]['description'];?>
</div>
<?php break; ?>
<?php case 'Video':?>
	<?php if ($item[$model]['youtube_id']):?>
		<div style='float:left;'><a href="/video/<?php echo $item[$model]['id'];?>"><img src="<?php echo $this->Youtube->getVideoImage($item[$model]['youtube_id']);?>" alt=""></a></div>
    <?php endif;?>
    <div style='float:left;height:100%;margin-left:20px;'>
    		<a href="/video/<?php echo $item[$model]['id'];?>"><?php echo $item[$model]['description'];?></a>
    </div>
<?php break; ?>
<?php case 'Link':?>
    <a href="/links/show/<?php echo $item[$model]['id'];?>"><strong><?php echo $item[$model]['title'];?></strong></a>
    <br/><?php echo $item[$model]['description'];?>
<?php break; ?>
<?php case 'Album':?>
    <a href="<?php echo $this->Formater->getAlbumLink($item[$model]);?>"><?php echo $item[$model]['name'];?></a><br/>
    <?php echo $item[$model]['description'];?>
<?php break; ?>
<?php case 'Event':?>
     <a href="/events/view/<?php echo $item[$model]['slug'];?>"><?php echo $item[$model]['name'];?></a><br/>
    <?php echo $item[$model]['description'];?>
<?php }?>
</td>


			<?php endif;?>

	    <?php if (!empty($modelOptions[$model]['calumns']['Upranks'])):?>
			<td class='center'><?php echo $this->element("votes/vote_plus",array('model'=> $model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $modelVotes, 'canVote'   =>$canVoteSubmissions));?>    		</td>
        <?php endif;?>

	    <?php if (!empty($modelOptions[$model]['calumns']['Downranks'])):?>
    		<td class='center'><?php echo $this->element("votes/vote_minus",array('model'=> $model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $modelVotes, 'canVote'   =>$canVoteSubmissions));?>    				    </td>
		<?php endif;?>

        <?php if (!empty($modelOptions[$model]['calumns']['Comments'])):?>
			<td class='center strong_number'><?php echo intval($item[$model]['comments']);?></td>
		<?php endif;?>

	    <?php if (!empty($modelOptions[$model]['calumns']['Submitted By'])):?>
			<td class='center'><a href="/u/<?php echo $item['User']['lgn'] ?>"><?php echo $item['User']['lgn'] ?></a></td>
		<?php endif;?>

	    <?php if (!empty($modelOptions[$model]['calumns']['Submitted'])):?>
			<td class='center'><?php echo date('m/d/y', strtotime($item[$model]['created']));?></td>
		<?php endif;?>
		<td>
		<?php if (!empty($itemsTags[$item[$model]['id']])):?>
			<?php  $countTags = count($itemsTags[$item[$model]['id']]);
			            $t = 0;
			            foreach ($itemsTags[$item[$model]['id']] as $itemTag):
			            $t++;
			            ?>
				<?php echo $this->Formater->showTag($itemTag, $model);?><?php if ($t<$countTags):?>, <?php endif;?>
				<?php endforeach;?>
		<?php endif;?>
		</td>
	</tr>

<?php endforeach; ?>
</table>
<?php echo $this->element('simple_paging');?>

