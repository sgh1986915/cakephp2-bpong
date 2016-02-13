<?php $this->pageTitle = 'Beer Pong Photo Album - ' . $album['Album']['name']; ?>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<?php
$this->Paginator->options(array('url' => $this->passedArgs));
$total = $this->Paginator->counter(array('format' => '%count%'));
?>
<h2 class='hr'>
    	<?php switch ($album['Album']['model']) {
    	case 'User': ?>
			<?php echo $this->Formater->authorsName($album['User'], 1, $myProfile); ?> Photo Album
    		<?php
    	break;
    	case 'Event': ?>
				Event Photo Album
    	<?php break;?>

    	<?php }?>
    <?php if ($allowChange):?>
    <?php echo $this->Html->link('Edit', '/Albums/edit/' . $album['Album']['id']);?>
     <span class='hr_subtext'>|</span> <?php echo $this->Html->link('Delete', '/Albums/delete/' . $album['Album']['id'], null, 'Are you sure you want to delete album?');?>
    <?php endif;?>
    <?php if ($canUpload):?>
     <span class='hr_subtext'>|</span> <?php echo $this->Html->link('Add More Photos', '/submit/image/album/' . $album['Album']['id']);?>
    <?php endif;?>
</h2>
<div style='text-align:center;width:100%;margin-bottom:7px;'>
    <div style='float:left;' class='no_underline'>
    	<?php switch ($album['Album']['model']) {
    	case 'User': ?>
			<?php echo $this->Html->link('<< Back to ' . $this->Formater->authorsName($album['User'], 0, $myProfile) . ' Albums', '/u/' . $album['User']['lgn']);?>
    	<?php
    	break;
    	case 'Event': ?>
			<?php echo $this->Html->link('<< Back to Event page', '/event/' . $model['Event']['id'] . '/' . $model['Event']['slug']);?>
    	<?php break;
    	case 'Organization': ?>
			<?php echo $this->Html->link('<< Back to Organization page', '/o/' . $model['Organization']['slug']);?>
    	<?php break;?>
    	<?php }?>
    </div>
    <div style='float:right;' class='no_underline'>
        <?php if($this->Paginator->numbers()): ?>
    <?php echo $this->Paginator->prev('<< prev');?>&nbsp;&nbsp;<?php echo $this->Paginator->next('next >>');?><br/>
    <?php endif;?>
    </div>
    <div>
        <b><?php echo $total;?></b> <?php echo $this->Language->pluralize($total, 'photo', 'photos'); ?> in album     <?php echo $this->Html->link($album['Album']['name'], '/Albums/show_image/' . $album['Album']['id']);?>
    </div>
</div>

<?php if (!empty($images)) {?>
<div style='background-color: #F8F8F8;border:1px solid #EFEFEF;padding: 10px 10px 0px 10px;'>
    <?php
    $i = 0;
    foreach ($images as $img) :
    $i++;
    ?>
<div class='album_image'>
    		<a href="/Images/albumShow/<?php echo $img['Image']['id'];?>" style='background-image:url("<?php echo IMG_ALBUMS_URL;?>/thumb_<?php echo $img['Image']['filename'];?>");'> </a>
</div>

    <?php if ($i == 4): $i=0;?>
	<br class='clear'/>
    <?php endif;?>
	<?php endforeach;?>
	<div class='clear'></div>
</div>
<?php } else {?>
<div style='width:100%;padding:20px;text-align:center;'><strong>No Photos</strong></div>
<?php }?>
<?php echo $this->element('simple_paging');?>
<br/>
<div class='album_info'>
    <div class="album-voting">
    	<div style='float:left;width:100%;'>
        <?php echo $this->element("votes/vote_plus",array('model'=>"Album", "modelId"  => $album['Album']['id'], 'votesPlus'=> $album['Album']['votes_plus'], 'votesMinus'=> $album['Album']['votes_minus'], 'ownerId'   => $album['Album']['user_id'], 'votes' => $imageAlbumVotes, 'canVote'   =>$canVoteSubmissions));?>
        <?php echo $this->element("votes/vote_minus",array('model'=>"Album", "modelId"  => $album['Album']['id'], 'votesPlus'=> $album['Album']['votes_plus'], 'votesMinus'=> $album['Album']['votes_minus'], 'ownerId' => $album['Album']['user_id'], 'votes' => $imageAlbumVotes, 'canVote' =>$canVoteSubmissions));?>
    	</div><br class='clear'/>
		<div class="sharethis"><?php echo $this->element('share_this');?></div>
		<br class='clear'/>
    </div>
    <div class="album-description">
          <span class='album_name'><?php echo $album['Album']['name'];?></span><br/>
        <?php if($album['Album']['description']):?><?php echo strip_tags($album['Album']['description']);?><?php else:?>
        No album description entered
        <?php endif;?>
    </div>
    <?php if ($album['User']['avatar']):?>
    	<div class="album-avatar"><a class='ava_log' href='/u/<?php echo $album['User']['lgn'];?>'><?php echo $this->Image->avatar($album['User']['avatar']);?></a></div>
    <?php endif;?>
	<div class="album-userinfo">
		<b>Posted by:</b><br/><span class='strong_red'><?php echo $this->Html->link($album['User']['lgn'], '/u/' . $album['User']['lgn']);?></span><br/>
		<b>Date:</b> <span class='strong_red'><?php echo date('m/d/y', strtotime($album['Album']['created']));?></span>
	</div>
	<div class='clear'> </div>
</div>

<br/>
<!--  TAGS ------------------------------------------->
<?php if (!empty($album['Tag'])):?>
	<h3 class="subtitle"><?php echo $this->Formater->authorsName($album['User'], 0, $myProfile);?> tags for this album: </h3>
	<!--  SHOW Author TAGS --><?php echo $this->element('/tags/show_authors', array('authorID' => $album['Album']['user_id'], 'tags' => $album['Tag']));?>
	<hr class='thin_hr'/>
<h3 class="subtitle">Tags from other users:</h3>
<?php endif;?>
<!--  SHOW Users TAGS -->
<?php echo $this->element('/tags/show_users', array('modelName' => 'Album', 'modelID' => $album['Album']['id'], 'authorID' => $album['Album']['user_id'], 'tags' => $album['Tag']));?>
<!--  EOF TAGS --------------------------------------->

<h2 class="hr"><span id='comment_counter_val'><?php echo $album['Album']['comments'];?></span> <span id='comment_counter_text'><?php echo $this->Language->pluralize($album['Album']['comments'], 'Comment', 'Comments');?></span></h2>
<?php echo $this->element("comments/add",array('model'=>"Album","modelId"=> $album['Album']['id'], 'comments'=> $comments, 'commentVotes' => $commentVotes));?>
