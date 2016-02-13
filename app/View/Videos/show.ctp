<?php if ($video['Video']['description']):?>
<?php $this->pageTitle = $video['Video']['description']; ?>
<?php else:?>
<?php $this->pageTitle = 'Beer Pong Video' ?>
<?php endif;?>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<div style='position: relative;margin-bottom:-30px;bottom:-5px; float:right;'><?php echo $this->element('facebook_like');?></div>
<h2 class='hr'>
    <?php echo $this->Formater->authorsName($video['User'], 1, $isAuthor); ?> Videos
    <?php if ($allowUpdate):?>
    <?php echo $this->Html->link('Edit', '/Videos/edit/' . $video['Video']['id']);?>
    <?php endif;?>
    <?php if ($allowDelete):?>
     <span class='hr_subtext'>|</span> <?php echo $this->Html->link('Delete', '/Videos/delete/' . $video['Video']['id'], null, 'Are you sure you want to delete video?');?>
    <?php endif;?>
</h2>
<div style='text-align:center;width:100%;margin-bottom:7px;'>
    <div style='float:left;' class='no_underline'>
        <?php echo $this->Html->link('<< Back to album', '/Albums/show_video/' . $album['Album']['id']);?>
    </div>
    <div style='float:right;' class='no_underline'>
        <?php if ($videoPaging['nextID'] && !$isAlbumJunk):?><a href="/video/<?php echo $videoPaging['nextID'];?>">next >></a><?php endif;?>
    </div>
    <div>
        <b><?php echo $videoPaging['pageNum'];?></b> of <b><?php echo $videoPaging['count'];?></b> <?php echo $this->Language->pluralize($videoPaging['count'], 'video', 'videos'); ?> in album     <?php echo $this->Html->link($album['Album']['name'], '/Albums/show_video/' . $album['Album']['id']);?>
    </div>
</div>
<br/><div style='width:100%; text-align:center;'>
    <?php echo $this->element('/video/show_video', array('video' => $video));?>
</div>
    <br/>
<div class='album_info'>
    <div style='float:left; position:relative;'>
    	<div style='float:left;width:100%;'>
        <?php echo $this->element("votes/vote_plus",array('model'=>"Video", "modelId"  => $video['Video']['id'], 'votesPlus'=> $video['Video']['votes_plus'], 'votesMinus'=> $video['Video']['votes_minus'], 'ownerId'   => $video['Video']['user_id'], 'votes' => $videoVotes, 'canVote'   =>$canVoteSubmissions));?>
        <?php echo $this->element("votes/vote_minus",array('model'=>"Video", "modelId"  => $video['Video']['id'], 'votesPlus'=> $video['Video']['votes_plus'], 'votesMinus'=> $video['Video']['votes_minus'], 'ownerId' => $video['Video']['user_id'], 'votes' => $videoVotes, 'canVote' =>$canVoteSubmissions));?>
    	</div><br class='clear'/>
		<div class="sharethis"><?php echo $this->element('share_this');?></div>
		<br class='clear'/>
    </div>
    <div style='float:left;margin-left:20px;width:570px;'>
          <?php if ($video['Video']['title']):?><span class='album_name'><?php echo $video['Video']['title'];?></span><br/><?php endif;?>
        <?php if($video['Video']['description']):?><?php echo $video['Video']['description'];?><?php else:?>
        No video description entered
        <?php endif;?>
    </div>
    <?php if ($video['User']['avatar']):?>
    	<div style='float:right;padding-top:7px;padding-left:7px;'><a class='ava_log' href='/u/<?php echo $video['User']['lgn'];?>'><?php echo $this->Image->avatar($video['User']['avatar']);?></a></div>
    <?php endif;?>
	<div style='float:right;text-align:right;'>
		<b>Posted by:</b><br/><span class='strong_red'><?php echo $this->Html->link($video['User']['lgn'], '/u/' . $video['User']['lgn']);?></span><br/>
		<b>Date:</b> <span class='strong_red'><?php echo date('m/d/y', strtotime($video['Video']['created']));?></span>
	</div>
	<div class='clear'> </div>
</div>
<br/>

<!--  TAGS ------------------------------------------->
<?php if (!empty($video['Tag'])):?>
	<h3 class="subtitle"><?php echo $this->Formater->authorsName($video['User'], 0, $isAuthor);?> tags for this photo: </h3>
	<!--  SHOW Author TAGS --><?php echo $this->element('/tags/show_authors', array('authorID' => $video['Video']['user_id'], 'tags' => $video['Tag']));?>
	<hr class='thin_hr'/>
<h3 class="subtitle">Tags from other users:</h3>
<?php endif;?>
<!--  SHOW Users TAGS -->
<?php echo $this->element('/tags/show_users', array('modelName' => 'Video', 'modelID' => $video['Video']['id'], 'authorID' => $video['Video']['user_id'], 'tags' => $video['Tag']));?>
<!--  EOF TAGS --------------------------------------->

<h2 class="hr"><span id='comment_counter_val'><?php echo $video['Video']['comments'];?></span> <span id='comment_counter_text'><?php echo $this->Language->pluralize($video['Video']['comments'], 'Comment', 'Comments');?></span></h2>
 <?php echo $this->element("comments/add",array('model'=>"Video","modelId"=> $video['Video']['id'], 'comments'=> $comments, 'commentVotes' => $commentVotes));?>


