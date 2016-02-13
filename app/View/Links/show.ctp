<?php $this->pageTitle = 'Beer Pong Link - ' . $link['Link']['title']; ?>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>

<h2 class='hr' style='margin-bottom:0px;'>
    <?php echo $this->Formater->authorsName($link['User'], 1, $isAuthor); ?> Link
    <?php if ($allowUpdate):?><?php echo $this->Html->link('Edit', '/links/edit/' . $link['Link']['id']);?><?php endif;?>
     <?php if ($allowDelete):?><span class='hr_subtext'>|</span> <?php echo $this->Html->link('Delete', '/links/delete/' . $link['Link']['id'], null, 'Are you sure you want to delete link?');?><?php endif;?>
</h2>
<div class='album_info' style='border-top:0px;'>
    <div class='clear'><br/></div>
    <div style='float:left;margin-left:20px;'>
          <span class='album_name'>
<a href="http://<?php echo str_replace('http://', '', $link['Link']['url']);?>"><?php echo $link['Link']['title'];?></a></span>
<br/>
        <?php if($link['Link']['description']):?><?php echo $link['Link']['description'];?><?php else:?>
        No link description entered
        <?php endif;?>
    </div>
    <?php if ($link['User']['avatar']):?>
    	<div style='float:right;padding-top:7px;padding-left:7px;'><a class='ava_log' href='/u/<?php echo $link['User']['lgn'];?>'><?php echo $this->Image->avatar($link['User']['avatar']);?></a></div>
    <?php endif;?>
    <div style='float:left; position:relative;margin-left: 20px;margin-top:10px;'>
    	<div style='float:left;width:100%;'>
        <?php echo $this->element("votes/vote_plus",array('model'=>"Link", "modelId"  => $link['Link']['id'], 'votesPlus'=> $link['Link']['votes_plus'], 'votesMinus'=> $link['Link']['votes_minus'], 'ownerId'   => $link['Link']['user_id'], 'votes' => $linkVotes, 'canVote'   =>$canVoteSubmissions));?>
        <?php echo $this->element("votes/vote_minus",array('model'=>"Link", "modelId"  => $link['Link']['id'], 'votesPlus'=> $link['Link']['votes_plus'], 'votesMinus'=> $link['Link']['votes_minus'], 'ownerId' => $link['Link']['user_id'], 'votes' => $linkVotes, 'canVote' =>$canVoteSubmissions));?>
    	</div><br class='clear'/>
		<div class="sharethis"><?php echo $this->element('share_this');?></div>
    </div>
	<div style='float:right;text-align:right;'>
		<b>Posted by:</b><br/><span class='strong_red'><?php echo $this->Html->link($link['User']['lgn'], '/u/' . $link['User']['lgn']);?></span><br/>
		<b>Date:</b> <span class='strong_red'><?php echo date('m/d/y', strtotime($link['Link']['created']));?></span>
	</div>
	<div class='clear'> </div>
</div>
<br/>

<!--  TAGS ------------------------------------------->
<?php if (!empty($link['Tag'])):?>
	<h3 class="subtitle"><?php echo $this->Formater->authorsName($link['User'], 0, $isAuthor);?> tags for this photo: </h3>
	<!--  SHOW Author TAGS --><?php echo $this->element('/tags/show_authors', array('authorID' => $link['Link']['user_id'], 'tags' => $link['Tag']));?>
	<hr class='thin_hr'/>
<h3 class="subtitle">Tags from other users:</h3>
<?php endif;?>
<!--  SHOW Users TAGS -->
<?php echo $this->element('/tags/show_users', array('modelName' => 'Link', 'modelID' => $link['Link']['id'], 'authorID' => $link['Link']['user_id'], 'tags' => $link['Tag']));?>
<!--  EOF TAGS --------------------------------------->

<h2 class="hr"><span id='comment_counter_val'><?php echo $link['Link']['comments'];?></span> <span id='comment_counter_text'><?php echo $this->Language->pluralize($link['Link']['comments'], 'Comment', 'Comments');?></span></h2>
 <?php echo $this->element("comments/add",array('model'=>"Link","modelId"=> $link['Link']['id'], 'comments'=> $comments, 'commentVotes' => $commentVotes));?>


