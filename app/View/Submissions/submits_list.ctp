<?php 
    $url = $this->passedArgs;
    $url['controller'] = 'Submissions';
    $url['action'] = 'submits_list';
    $model = 'Link';

//    $this->Paginator->options(array('model'=> $model, 'url' => $url));
//    $this->Paginator->__defaultModel = $model;
//    $sortKey = $this->Paginator->sortKey();
?>
<?php if (empty($submits)):?>
No Submissions <br/><br/>
<?php else:?>
<table class='sub_list sorter' cellpadding="0" cellspacing="0">
    <tr>
    	<th><a href="#" onclick='return false;'>#</a></th>
    	<th colspan='2' class='paginationSubmits'><?php echo $this->Paginator->sort('Title & Description', 'name', array('sorter' => true));?></th>
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
    foreach ($submits as $submit):
    	$class = ' class="lite_grey_bg"';
    	if ($i++ % 2 != 0) {
    		$class = ' class="grey_bg"';
    	}
    	$j++;
    	$item = $submit['0'];
    ?>
    	<tr<?php echo $class;?>>
			<td class='center'><?php echo $prevNums + $j;?></td>	   	
				<?php switch($item['model']){
				case 'Link':
    		        echo $this->element('/submissions/element_link', array('item' => $item));			    
				break;
				case 'Video':
    		        echo $this->element('/submissions/element_video', array('item' => $item));					    
				break;
				case 'Image':
    		        echo $this->element('/submissions/element_image', array('item' => $item));				    
				break;
				}
				?>  	
			<td>
                <?php echo $this->element("votes/vote_plus",array('model'=>$item['model'], "modelId"  => $item['id'], 'votesPlus'=> $item['votes_plus'], 'votesMinus'=> $item['votes_minus'], 'ownerId'   => $item['user_id'], 'votes' => $votes[$item['model']], 'canVote'   =>$canVoteSubmissions));?>
			</td>
			<td>
                <?php echo $this->element("votes/vote_minus",array('model'=>$item['model'], "modelId"  => $item['id'], 'votesPlus'=> $item['votes_plus'], 'votesMinus'=> $item['votes_minus'], 'ownerId'   => $item['user_id'], 'votes' => $votes[$item['model']], 'canVote'   =>$canVoteSubmissions));?>
			</td>
    		<td><?php  echo $item['views'];?></td>
    		<td>
    		<?php  echo date('m/d/y', strtotime($item['created']));?>
    		<br/><?php  echo date('h:i A', strtotime($item['created']));?>
    		</td>
		<td>
		<?php if (!empty($itemsTags[$item['model']][$item['id']])):?>
			<?php  $countTags = count($itemsTags[$item['model']][$item['id']]);
			            $t = 0;
			            foreach ($itemsTags[$item['model']][$item['id']] as $itemTag):
			            $t++;
			            ?>
			            <?php echo $this->Formater->showTag($itemTag, $item['model']);?><?php if ($t<$countTags):?>, <?php endif;?>							
			<?php endforeach;?>			
		<?php endif;?>		
		</td>    		
    	</tr>

    <?php endforeach; ?>

    </table>
<?php endif;?>
		<div class="no_underline" style='width:100%; text-align:center;'>
		<div style='float:left'>View all of <?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?>
            <a href="/images/users_all/<?php echo $user['User']['id'];?>">Images</a> - 
            <a href="/videos/users_all/<?php echo $user['User']['id'];?>">Videos</a> - 
            <a href="/links/users_all/<?php echo $user['User']['id'];?>">Links</a>
		</div>
    <?php if ($this->Paginator->numbers(array('model' => 'Link'))):?>
        <div class='no_underline paginationSubmits'>
    	pages: <?php echo $this->Paginator->prev('<< prev');?> <?php echo $this->Paginator->numbers(array('separator' => '&nbsp;&nbsp;'));?> <?php echo $this->Paginator->next('next >>');?><br/>
    	</div>
    <?php endif;?>
		</div>   

