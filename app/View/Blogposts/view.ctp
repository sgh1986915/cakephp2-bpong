<div class="blogposts">
<div class='conte'>
 	<h2><?php echo $this->request->data['Blogpost']['title']; ?></h2>
    <div class='f_left'><small>Posted: <?php echo $this->Time->niceShort($this->request->data['Blogpost']['created']); ?> 
    				by <?php echo $this->Html->link($this->request->data['User']['lgn'], array('controller' => 'users', 'action'=>'view',$this->request->data['User']['lgn'])); ?> </small></div>
    <?php if($this->request->data['Blogpost']['modified']): ?>
    <div class='f_right'>
     <small>Last modified: <?php echo $this->Time->niceShort($this->request->data['Blogpost']['modified']); ?> &nbsp;
     <strong>Comments: <?php echo $this->request->data['Blogpost']['comments']; ?></strong>;
     </small></div>
    <?php endif; ?>
</div>
<div class='post'>
	<p>
       <?php echo $this->request->data['Blogpost']['description']; ?>
    </p>
</div>
 <div class='f_right' style='width:100px;'>
	<?php echo $this->element("votes/vote",array('model'=>"Blogpost",
		"modelId"  =>$this->request->data['Blogpost']['id'],
		'votesPlus'=> $this->request->data['Blogpost']['votes_plus'],
		'votesMinus'=> $this->request->data['Blogpost']['votes_minus'],
		'ownerId'   => $this->request->data['Blogpost']['user_id'],
		'votes'       => $votes,
		'canVote'   =>$canVoteBlogpost ));?>
	<strong class='f_right rating'>Rating: </strong>
 </div>
 </div> 
 <h2 class="hr"><span id='comment_counter_val'><?php echo $this->request->data['Blogpost']['comments'];?></span> <span id='comment_counter_text'><?php echo $this->Language->pluralize($this->request->data['Blogpost']['comments'], 'Comment', 'Comments');?></span></h2>
 <?php echo $this->element("comments/add",array('model'=>"Blogpost","modelId"=>$this->request->data['Blogpost']['id'],'comments'=>$comments,'commentVotes'=>$commentVotes ));?>
 