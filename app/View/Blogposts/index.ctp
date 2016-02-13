<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/comments/comments.js'></script>
<?php $paginate_params = array('escape' => false); ?>
<div style="display:inline-block; width:100%;">
<a href="/nation/beer-pong-blog.rss" title="rss feed" style="float:right;">
    <?php echo $this->Html->image('rss/rss_feed.gif'); ?>
</a>
</div>
<div class="blogposts">
 <?php foreach($blogposts as $index => $post):  ?>
 	<div class='conta'>
		<h2><a href="/nation/beer-pong-blog/view/<?php echo $post['Blogpost']['slug']; ?>"> <?php echo $post['Blogpost']['title']; ?></a></h2>
	</div>
    <div class='f_left'><small>Posted: <?php echo $this->Time->niceShort($post['Blogpost']['created']); ?>
    				by <?php echo $this->Html->link($post['User']['lgn'], array('controller' => 'users', 'action'=>'view',$post['User']['lgn'])); ?> </small>
    <?php if($post['Blogpost']['modified']): ?></div>
     <div class='f_right'><small>Last modified: <?php echo $this->Time->niceShort($post['Blogpost']['modified']); ?></small></div>
    <?php endif; ?>
<div class='post'>

    <p>
       <?php echo $post['Blogpost']['description']; ?>
    </p>
</div>
       <div class="clear"></div>
 	<strong>
 	<a href="/nation/beer-pong-blog/view/<?php echo $post['Blogpost']['slug']; ?>#comments">Comments</a>: <?php echo $post['Blogpost']['comments']; ?>
 	</strong>
 	<?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
 	/&nbsp;<a href="/nation/beer-pong-blog/view/<?php echo $post['Blogpost']['slug']; ?>?addcomment#add_comment">Add Comment</a>
 	<?php endif;?>
    <?php if($canEdit) echo "/&nbsp;".$this->Html->link('<img alt="edit" src="/img/smalledit.gif" />', Router::url(array('action'=>'edit',$post['Blogpost']['id'])), array('escape' => false), null, false); ?>&nbsp;
    <?php if($canDelete) echo $this->Html->link('<img alt="edit" src="/img/smalldelete.gif" />', Router::url(array('action'=>'delete',$post['Blogpost']['id'])), array('escape' => false), "Are you sure?", false); ?>

	<div style='float:right;'>
		<div style='color:#D61C20;float:left;padding-top:5px;'><strong>Rating: </strong></div>
		<div style='float:left;'>
		<?php echo $this->element("votes/vote",array('model'=>"Blogpost",
			"modelId"  =>$post['Blogpost']['id'],
			'votesPlus'=> $post['Blogpost']['votes_plus'],
			'votesMinus'=> $post['Blogpost']['votes_minus'],
			'ownerId'   => $post['Blogpost']['user_id'],
			'votes'       => $votes,
			'canVote'   =>$canVoteBlogpost ));?>
		</div>
	 </div>
    <br />
    <br />
		<?php echo $this->element("comments/add",array('model' => 'Blogpost', "modelId"=> $post['Blogpost']['id'], 'comments'=>$post['Comments'], 'commentVotes'=> $post['commentVotes'], 'hideAddLink' => 1, 'commentsLimit' => 1));?>
<?php if ($post['Blogpost']['comments'] > 3): ?>
	 	<a href="/nation/beer-pong-blog/view/<?php echo $post['Blogpost']['slug']; ?>?addcomment#add_comment">Show All Comments</a>
<?php endif;?>
<?php if (isset($LoggedMenu) && !empty($LoggedMenu) && $post['Blogpost']['comments'] > 0): ?>
/&nbsp;<a href="/nation/beer-pong-blog/view/<?php echo $post['Blogpost']['slug']; ?>?addcomment#add_comment">Add Comment</a>
<?php endif;?>
<?php endforeach;  ?>
</div>
<?php if( $this->Paginator->params['paging']['Blogpost']['pageCount'] > 1 ): ?>
	<div class="paging2">
		<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
		<?php echo $this->Paginator->numbers();?>
		<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
		<?php echo $this->element('pagination'); ?>
		<?php unset($paginate_params); ?>
	</div>
<?php endif; ?>
<br />
<?php if($canAdd): ?>
	<div class="actions">
    <ul>
    <li><span class="addbtn"><?php echo $this->Html->link('Add Post', Router::url(array('action'=>'add')), array('class'=>'addbtn')); ?></span></li>
    </ul>
    </div>
<?php endif; ?>
