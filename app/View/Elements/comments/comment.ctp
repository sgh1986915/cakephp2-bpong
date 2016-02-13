<li class="coment<?php echo $isLast ?><?php if (!empty($comment['children'])) echo " parent"?>" id="comment_<?php echo $comment['Comment']['id'];?>" >
	<div class="comment-left">
		<div class="comment-bottom<?php if (!empty($comment['children'])) echo " parent"?>">
				<div class="message <?php if ($comment['Comment']['votes_plus'] - $comment['Comment']['votes_minus']< 0) echo " hid ";?>">
					<div class='cont'>
						<small>
							<?php echo $this->Image->avatar($comment['User']['avatar']);?>
							<?php echo $this->Html->link($comment['User']['lgn'], '/u/' . $comment['User']['lgn']); ?>

							<?php echo $this->Time->niceShort($comment['Comment']['created']); ?>
							<a href="#comment_<?php echo $comment['Comment']['id'];?>"  name = "comment_<?php echo $comment['Comment']['id'];?>" title="comment link" rel="bookmark">#</a>

							<?php if ($this->Access->getAccess($canComment)):?>
								<a href="?reply_to=<?php echo $comment['Comment']['id'];?>#comment_<?php echo $comment['Comment']['id'];?>" onclick="commentForm('<?php echo $comment['Comment']['id'];?>'); return false;">reply</a>
							<?php endif;?>
							<a class='collaps' href='#'>[-]</a>
							<a class='showcam' href='#'>[+]</a>
							<?php if (!empty($AdminMenu) || $comment['Comment']['user_id'] == $userSession['id']):?>
									<a href="#" onclick="deleteComment(<?php echo $comment['Comment']['id'];?>); return false;">[delete]</a>
							<?php endif;?>
						</small>
					</div>
				<?php echo $this->element("votes/vote",array('model'    =>"Comment",
																					"modelId" =>$comment['Comment']['id'],
																					'votesPlus'=>$comment['Comment']['votes_plus'],
																					'votesMinus'=>$comment['Comment']['votes_minus'],
																					'ownerId'  =>$comment['Comment']['user_id'],
				                                                                    'votes'       => $commentVotes,
																					'canVote'  =>$canVoteComment ));?>
					<div class="commentContent">
					        <?php echo html_entity_decode($this->Bbcode->convert_bbcode( $comment['Comment']['comment']), ENT_QUOTES, 'UTF-8'); ?>
					</div>
				</div>
				<div class="form_messages">
					<div class='prev1' id="preview_<?php echo $comment['Comment']['id'];?>"></div>
					<div class='comment_error' id="error_<?php echo $comment['Comment']['id'];?>"></div>
					<div id="reply_<?php echo $comment['Comment']['id'];?>"></div>
				</div>

		</div>
	</div>
				<?php if (!empty($comment['children'])):?>
					<ul class="comments sublevel">
				            <?php $cnt = count($comment['children']); $i = 0; $isLast = ""?>
							<?php foreach ($comment['children'] as $c):?>
							        <?php $i++;?>
							        <?php if ($cnt == $i) $isLast = " lst";?>
									<?php echo $this->element("comments/comment",array('model'=>$model,"modelId"=>$modelId,'comment'=>$c, 'commentVotes'=>$commentVotes, "isLast" => $isLast ));?>
							<?php endforeach;?>
					</ul>
				<?php endif;?>
</li>