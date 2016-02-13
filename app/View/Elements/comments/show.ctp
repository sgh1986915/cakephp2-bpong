<?php echo $this->Html->css ( array ('bbcode' ) );?>
<div> 
<a href="#comments"  name = "comments" title="comment link" rel="bookmark"></a>

<?php if (isset($comments) && !empty($comments)) :?>
<?php $cnt = count($comments); $i = 0; $isLast = ""?>
	<ul class="comments">
		<?php foreach ($comments as $comment):?>
			<?php
				if ($cnt == $i) $isLast = " lst";
				echo $this->element("comments/comment",array('model'=>$model,"modelId"=>$modelId,'comment'=>$comment,'class'=>"cla", "isLast" => $isLast, 'commentVotes' => $commentVotes ));
			?>
		<?php endforeach;?>
	</ul>
<?php endif;?>
</div>