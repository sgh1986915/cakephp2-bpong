<?php 
    $url = $this->passedArgs;
    $model = $model;

    $this->Paginator->options(array('model'=> $model, 'url' => $url));
    $this->Paginator->__defaultModel = $model;
    $sortKey = $this->Paginator->sortKey();
?>
<?php $this->pageTitle = 'All ' . $this->Formater->authorsName($user['User'], 1, $isAuthor) . ' Videos' ?>
<h2 class='hr'>All <?php echo $this->Formater->authorsName($user['User'], 1, $isAuthor); ?> Videos</h2>
<table class='sub_list sorter' cellpadding="0" cellspacing="0">
    <tr>
    	<th><a href="#">#</a></th>   
    	<th colspan='2' class='paginationSubmits'><?php echo $this->Paginator->sort('Title & Description', 'title', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('Ups', 'votes_plus', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('Downs', 'votes_minus', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('Views', 'views', array('sorter' => true));?></th>
    	<th class='paginationSubmits'><?php echo $this->Paginator->sort('When', 'created', array('sorter' => true));?></th>
		<th><a href="#" onclick='return false;'>Tags</a></th>   	
    </tr>
    <?php
    $prevNums = ($this->Paginator->current() - 1) * $limit; 
    $i = 0;
    $j = 0;
    foreach ($items as $item):
        $j++;
    	$class = ' class="lite_grey_bg"';
    	if ($i++ % 2 != 0) {
    		$class = ' class="grey_bg"';
    	}
    	if (isset ($item['Album']['id'])) {
        	$item[$model]['album_id'] = $item['Album']['id']; 
        	$item[$model]['album_name'] = $item['Album']['name'];      	    
    	} else {
    	    $item[$model]['album_id'] = 0; 
    	    $item[$model]['album_name'] = '';     	    
    	}   	
    ?>
    	
    	<tr<?php echo $class;?>>
			<td class='center'><?php echo $prevNums + $j;?></td>	   	
                <?php echo $this->element('/submissions/element_video', array('item' => $item[$model]));	?>  	
			<td>
                <?php echo $this->element("votes/vote_plus",array('model'=>$model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $votes, 'canVote'   =>$canVoteSubmissions));?>
			</td>
			<td>
                <?php echo $this->element("votes/vote_minus",array('model'=>$model, "modelId"  => $item[$model]['id'], 'votesPlus'=> $item[$model]['votes_plus'], 'votesMinus'=> $item[$model]['votes_minus'], 'ownerId'   => $item[$model]['user_id'], 'votes' => $votes, 'canVote'   =>$canVoteSubmissions));?>
			</td>
    		<td><?php  echo $item[$model]['views'];?></td>
    		<td><?php  echo date('m/d/y', strtotime($item[$model]['created']));?><br/><?php  echo date('h:i A', strtotime($item[$model]['created']));?></td>
    		<td>
    		<?php if (!empty($itemsTags[$item[$model]['id']])):?>
    			<?php  $countTags = count($itemsTags[$item[$model]['id']]);
    			            $t = 0;
    			            foreach ($itemsTags[$item[$model]['id']] as $itemTag):
    			            $t++;
    			            ?>
    				<?php echo $this->Html->link($itemTag['tag'], '/tag/' . $itemTag['id'] . '/' . $model, array('class' => 'tag_name')); ?>  <span class='underline'>(<?php echo $this->Html->link($itemTag['counter'], '/tag/' . $itemTag['id'] . '/' . $model); ?>)</span><?php if ($t<$countTags):?>, <?php endif;?>							
    			<?php endforeach;?>			
    		<?php endif;?>		
    		</td>	   		
    	</tr>

    <?php endforeach; ?>
    </table>
<div style='float:left'>View all of <?php echo $this->Formater->authorsName($user['User'], 1, $isAuthor); ?>
    <a href="/images/users_all/<?php echo $user['User']['id'];?>">Images</a> - 
    <a href="/links/users_all/<?php echo $user['User']['id'];?>">Links</a>
</div>