<?php $this->pageTitle = $img['Image']['name']; ?>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<div style='position: relative;margin-bottom:-30px;bottom:-5px; float:right;'><?php echo $this->element('facebook_like');?></div>
<h2 class='hr'>
	<?php if ($img['User']['id']):?>
    	<?php echo $this->Formater->authorsName($img['User'], 1, $isAuthor); ?> Photos
    <?php endif;?>
    <?php if ($allowUpdate):?>
    <?php echo $this->Html->link('Edit', '/Images/albumEdit/' . $img['Image']['id']);?>
    <?php endif;?>
    <?php if ($allowDelete):?>
     <span class='hr_subtext'>|</span> <?php echo $this->Html->link('Delete', '/Images/albumDelete/' . $img['Image']['id'], null, 'Are you sure you want to delete image?');?>
    <?php endif;?>
    <?php if ($allowUpdate && $album['Album']['cover_image_id'] != $img['Image']['id']):?>
     		<span class='hr_subtext'>|</span> <?php echo $this->Html->link('Use as album cover', '/Albums/change_cover/' . $album['Album']['id'] . '/' . $img['Image']['id'], null, 'Are you sure you want to use this photo as album cover?');?>
    <?php endif;?>
</h2>
<div style='text-align:center;width:100%;margin-bottom:7px;'>
    <div style='float:left;' class='no_underline'>
        <?php echo $this->Html->link('<< Back to thumbnail view', '/Albums/show_image/' . $album['Album']['id']);?>
    </div>
    <div style='float:right;' class='no_underline'>
        <?php if ($imagePaging['nextID']):?><a href="/Images/albumShow/<?php echo $imagePaging['nextID'];?>">next >></a><?php endif;?>
    </div>
    <div>
        <b><?php echo $imagePaging['pageNum'];?></b> of <b><?php echo $imagePaging['count'];?></b> <?php echo $this->Language->pluralize($imagePaging['count'], 'photo', 'photos'); ?> in album     <?php echo $this->Html->link($album['Album']['name'], '/Albums/show_image/' . $album['Album']['id']);?>
    </div>
</div>
<br/><div style='width:100%; text-align:center;'>
    <?php if ($imagePaging['nextID']):?><a href="/Images/albumShow/<?php echo $imagePaging['nextID'];?>"><?php endif;?>
    	<img src="<?php echo IMG_ALBUMS_URL;?>/big_<?php echo $img['Image']['filename'];?>"/>
    <?php if ($imagePaging['nextID']):?></a><?php endif;?>
</div>

<br/>

<div class='album_info'>
    <div style='float:left; position:relative;'>
    	<div style='float:left;width:100%;'>
        <?php echo $this->element("votes/vote_plus",array('model'=>"Image", "modelId"  => $img['Image']['id'], 'votesPlus'=> $img['Image']['votes_plus'], 'votesMinus'=> $img['Image']['votes_minus'], 'ownerId'   => $img['Image']['user_id'], 'votes' => $imageVotes, 'canVote'   =>$canVoteSubmissions));?>
        <?php echo $this->element("votes/vote_minus",array('model'=>"Image", "modelId"  => $img['Image']['id'], 'votesPlus'=> $img['Image']['votes_plus'], 'votesMinus'=> $img['Image']['votes_minus'], 'ownerId' => $img['Image']['user_id'], 'votes' => $imageVotes, 'canVote' =>$canVoteSubmissions));?>
    	</div><br class='clear'/>
		<div class="sharethis"><?php echo $this->element('share_this');?></div>
		<br class='clear'/>
    </div>
    <div style='float:left;margin-left:20px;'>
          <span class='album_name'><?php echo $img['Image']['name'];?></span><br/>
        <?php if($img['Image']['description']):?><?php echo $img['Image']['description'];?><?php else:?>
        No photo description entered
        <?php endif;?>
    </div>
    <?php if ($img['User']['avatar']):?>
    	<div style='float:right;padding-top:7px;padding-left:7px;'><a class='ava_log' href='/u/<?php echo $img['User']['lgn'];?>'><?php echo $this->Image->avatar($img['User']['avatar']);?></a></div>
    <?php endif;?>
	<div style='float:right;text-align:right;'>
		<b>Posted by:</b><br/><span class='strong_red'><?php echo $this->Html->link($img['User']['lgn'], '/u/' . $img['User']['lgn']);?></span><br/>
		<b>Date:</b> <span class='strong_red'><?php echo date('m/d/y', strtotime($img['Image']['created']));?></span>
	</div>
	<div class='clear'> </div>
</div>
<br/>

<!--  TAGS ------------------------------------------->
<?php if (!empty($img['Tag'])):?>
	<h3 class="subtitle"><?php echo $this->Formater->authorsName($img['User'], 0, $isAuthor);?> tags for this photo: </h3>
	<!--  SHOW Author TAGS --><?php echo $this->element('/tags/show_authors', array('authorID' => $img['Image']['user_id'], 'tags' => $img['Tag']));?>
	<hr class='thin_hr'/>
<?php endif;?>
<h3 class="subtitle">Tags from other users:</h3>
<!--  SHOW Users TAGS -->
<?php echo $this->element('/tags/show_users', array('modelName' => 'Image', 'modelID' => $img['Image']['id'], 'authorID' => $img['Image']['user_id'], 'tags' => $img['Tag']));?>
<!--  EOF TAGS --------------------------------------->

<h2 class="hr"><span id='comment_counter_val'><?php echo $img['Image']['comments'];?></span> <span id='comment_counter_text'><?php echo $this->Language->pluralize($img['Image']['comments'], 'Comment', 'Comments');?></span></h2>
 <?php echo $this->element("comments/add",array('model'=>"Image","modelId"=> $img['Image']['id'], 'comments'=> $comments, 'commentVotes' => $commentVotes));?>
