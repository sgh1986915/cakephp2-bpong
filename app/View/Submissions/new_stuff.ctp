<div style='position: relative;margin-bottom:-30px;bottom:-5px; float:right;'><?php echo $this->element('facebook_like');?></div>
<?php
	echo $this->Html->css(array('stylish-select.css'));
	echo $this->Html->script('jquery.stylish-select.min.js');
?>
<script type="text/javascript" src='<?php echo STATIC_BPONG ?>/js/votes/votes.js'></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.custom-select').sSelect();
	});
</script>

<?php
	$contentsConfigs = array(
		'All' => array('name' => 'All Submissions', 'title' => 'All Beer Pong Submissions', 'url' => 'all_submissions/'),
		'Image' => array('name' => 'Images', 'title' => 'All New Beer Pong Images', 'url' => 'images/'),
		'Video' => array('name' => 'Videos', 'title' => 'All New Beer Pong Videos', 'url' => 'videos/'),
		'Link' => array('name' => 'Links', 'title' => 'All New Beer Pong Links', 'url' => 'links/'),
	);
    $this->Paginator->options(array('url' => array('controller' => $contentsConfigs[$model]['url'], 'action' => 'index') + $this->passedArgs));
    if ($modelMix) {
		$this->Paginator->__defaultModel = 'Link';	
	} else {
    	$this->Paginator->__defaultModel = $model;		
	}
    $sortKey = $this->Paginator->sortKey();

    foreach ($contentsConfigs as $contentsConfig) {
		 $contentLinks[$contentsConfig['url']] = $contentsConfig['name'];
    }

?>

<script type="text/javascript">
	function submitSubmissionForm() {
		var formAction = '';
		var search = $('#search_string').val();
		formAction = '/' + $('#url_link').val();
		$('#searchesForm').attr('action', formAction);
		return true;
	}
</script>

<?php $this->pageTitle = $contentsConfigs[$model]['title'];?>
<h1 class="h1_middle"><?php echo $contentsConfigs[$model]['title'];?></h1>
<br/>
<?php echo $this->Form->create('Submissions',array('id'=>'searchesForm','action'=>'index', 'onsubmit' => 'return submitSubmissionForm();'));?>
<table>
	<td width='60px' align='right'><span class='red'>Switch to:</span></td> <td><?php echo $this->Form->input('model',array('selected' => $contentsConfigs[$model]['url'], 'div' => false, 'class' => 'custom-select', 'style' => 'width: 120px;','type' => 'select','label'=> false, 'id' => 'url_link' , 'onchange' => "$('#searchesForm').submit();", 'options' => $contentLinks));?></td>
<tr>
	<td align='right'><span class='red'>Search:</span></td> <td><?php echo $this->Form->input('NewStaffSearch',array('label'=>false, 'div' => false, 'id' => 'search_string', 'class' => 'red_input'));?></td>
</tr>
</table>
<?php echo $this->Form->end('Search');?>
<br/>
<?php if (empty($items)):?>
<br/>
<div style='width:100%;text-align:center;font-size:16px;'>No Results</div> <br/><br/>
<?php else:?>
<div class='hr'></div>
<table class='sub_list sorter' cellpadding="0" cellspacing="0">
    <tr>
    	<th><strong>#<strong></th>
    	<th colspan='2' class='paginationSubmits'><?php echo $this->Paginator->sort('Title & Description', 'name', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('Ups', 'votes_plus', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('Downs', 'votes_minus', array('sorter' => true));?></th>
    	<th style='width:55px;' class='paginationSubmits'><?php echo $this->Paginator->sort('Comments', 'comments', array('sorter' => true));?></th>
    	<th style='width:55px;' class='paginationSubmits'><?php echo $this->Paginator->sort('View Count', 'views', array('sorter' => true));?></th>
    	<th style='width:65px;' class='paginationSubmits'><?php echo $this->Paginator->sort('Submitted By', 'User.lgn', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('When', 'created', array('sorter' => true));?></th>
		<th>Tags</th>
    </tr>
    <?php
    $prevNums = ($this->Paginator->current() - 1) * $limit;
    $i = 0;
    $j = 0;
    foreach ($items as $item):
	if ($modelMix) {
		$model = $item['model'];	
	}
    $class = ' class="lite_grey_bg"';
    	if ($i++ % 2 != 0) {
    		$class = ' class="grey_bg"';
    	}
    	if (!empty($item['Album'])) {
    		$item[$model]['album_name'] = $item['Album']['name'];
    		$item[$model]['album_id'] = $item['Album']['id'];
    	}
    	$j++;
    ?>
    	<tr<?php echo $class;?>>
			<td class='center'><?php echo $prevNums + $j;?></td>
				<?php switch($model){
				case 'Link':
    		        echo $this->element('/submissions/element_link', array('item' => $item[$model]));
				break;
				case 'Video':
    		        echo $this->element('/submissions/element_video', array('item' => $item[$model]));
				break;
				case 'Image':
    		        echo $this->element('/submissions/element_image', array('item' => $item[$model]));
				break;
				}
				?>
			<td>
                <?php echo $this->element("votes/vote_plus",array('model' => $model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $votes[$model], 'canVote'   =>$canVoteSubmissions));?>
			</td>
			<td>
                <?php echo $this->element("votes/vote_minus",array('model' => $model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $votes[$model], 'canVote'   =>$canVoteSubmissions));?>
			</td>
    		<td><?php  echo $item[$model]['comments'];?></td>
    		<td><?php  echo $item[$model]['views'];?></td>  		
    		<td><a href="/u/<?php  echo $item['User']['lgn'];?>"><?php  echo $item['User']['lgn'];?></a></td>
    		<td><?php  echo date('m/d/y', strtotime($item[$model]['created']));?></td>
		<td>
		<?php if (!empty($item['Tag'])):?>
			<?php  $countTags = count($item['Tag']);
			            $t = 0;
			            foreach ($item['Tag'] as $tag):
			            $t++;
			            ?>
				<?php echo $this->Formater->showTag($tag, $model);?><?php if ($t<$countTags):?>, <?php endif;?>
				<?php endforeach;?>
		<?php endif;?>
		</td>
    	</tr>

    <?php endforeach; ?>

    </table>
<?php endif;?>
<?php 
    if ($modelMix) {
		$model = 'Link';
    }		
?>
		<div class="no_underline" style='width:100%; text-align:center;'>
    <?php if ($this->Paginator->numbers(array('model' => $model))):?>
        <div class='no_underline paginationSubmits'>
    	pages: <?php echo $this->Paginator->prev('<< prev');?> <?php echo $this->Paginator->numbers(array('separator' => '&nbsp;&nbsp;'));?> <?php echo $this->Paginator->next('next >>');?><br/>
    	</div>
    <?php endif;?>
		</div>

