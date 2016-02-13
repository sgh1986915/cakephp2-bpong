<?php 
$url = $this->passedArgs;
$url['controller'] = 'Albums';
$url['action'] = 'video_albums_list';
?>
<?php 
    $this->Paginator->options(array('model'=>'Album', 'url' => $url));
    $this->Paginator->__defaultModel = 'Album';
?>
    	<?php 
        $i = 0; $j = 0;	
        $countAlbums = count($albums);
    	foreach ($albums as $album):
    	$i++; $j++;
    	?>
<div class="videoAlbumItem<?php if(($i % 4) == 0) { echo " last"; } ?>">
	  <div style='height: 120px; margin: 0px auto;margin-bottom:5px;' class='clear'>
      <a href="/Albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>">
      		<?php if ($album['CoverVideo']['id']) { ?>
      			<img src="<?php echo $this->Youtube->getVideoImage($album['CoverVideo']['youtube_id']);?>"/>
    		<?php } else { ?>
    			<img src="<?php echo STATIC_BPONG?>/img/emptyVideo.jpg"/>
    		<?php }?>
    </a>
    </div>
	    <a href="/Albums/show_<?php echo $album['Album']['content_type'];?>/<?php echo $album['Album']['id'];?>"  style='text-decoration:none;'><span style='font-weight:bold;'><?php echo $this->Formater->stringCut($album['Album']['name'], 35);?></span></a>
	<br> <?php echo $album['Album']['files_num'];?> <?php echo $this->Language->pluralize($album['Album']['files_num'], 'Video', 'Videos'); ?>
	<?php if ($album['Album']['comments'] > 0):?>
	    &nbsp;- &nbsp;<?php echo $album['Album']['comments'];?> <?php echo $this->Language->pluralize($album['Album']['comments'], 'comment', 'comments'); ?>		
	<?php endif;?>
	<br/>
	<div style='width:100%;text-align:center; margin-top: 4px;'>
        <?php echo $this->element("votes/vote_plus",array('model'=>"Album", "modelId"  => $album['Album']['id'], 'votesPlus'=> $album['Album']['votes_plus'], 'votesMinus'=> $album['Album']['votes_minus'], 'ownerId'   => $album['Album']['user_id'], 'votes' => $videoAlbumVotes, 'canVote'   =>$canVoteSubmissions));?>    
        <?php echo $this->element("votes/vote_minus",array('model'=>"Album", "modelId"  => $album['Album']['id'], 'votesPlus'=> $album['Album']['votes_plus'], 'votesMinus'=> $album['Album']['votes_minus'], 'ownerId' => $album['Album']['user_id'], 'votes' => $videoAlbumVotes, 'canVote' =>$canVoteSubmissions));?>
		<br class='clear'/>
	</div>				
	<?php if ($allowChange || $loginUserID == $album['Album']['user_id']):?>
	<a href="/Albums/edit/<?php echo $album['Album']['id'];?>">edit</a> |
	<a href="/Albums/delete/<?php echo $album['Album']['id'];?>" onclick='return confirm("Are you sure, you want to delete this Album? ")'>delete</a>	
	<?php endif;?>
</div>      
        <?php endforeach;?> 
<?php if ($allowCreate): ?>
    <div class="videoAlbumItem last">
    	  <div style='height: 120px; margin: 0px auto;margin-bottom:5px;' class='clear'>
          <a href="/Albums/add/video/<?php echo $model;?>/<?php echo $modelID;?>">
        			<img src="<?php echo STATIC_BPONG?>/img/add_album.png"/>
         </a>
        </div>
    	    <a href="/Albums/add/video/<?php echo $model;?>/<?php echo $modelID;?>" class="add_link"><span>Create a new album</span></a>
    	    <br/><br/>&nbsp;
    </div> 
<?php else :?>
        <?php if (empty($albums)):?>
        No Albums
        <?php endif;?>
<?php endif; ?>  
  
<?php if ($this->Paginator->numbers(array('model' => 'Album'))):?>
	<br class='clear'/>
	<br class='clear'/>
	<div class="paginationVideoAlbums">
		<?php echo $this->element('simple_paging');?>
	</div>
<?php endif;?>