<div>
	<ul class="comments">
		<?php if (isset($comments) && !empty($comments)) :?>
			<?php foreach ($comments as $comment):?>
				<?php echo $this->element("comments/comment",array('model'=>$model,"modelId"=>$modelId,'comment'=>$comment,'votes'=>$votes  ));?>
			<?php endforeach;?>
		<?php endif;?>
	</ul>
</div>