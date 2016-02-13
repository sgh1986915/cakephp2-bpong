<div class="knowledgeQuestions index">
<h2><?php echo $knowledgeTopic['KnowledgeTopic']['name']?></h2>
<?php if ($isAccess):?>
    <?php echo $this->Html->link(__('Topic link'), array('action'=>'show', $knowledgeTopic['KnowledgeTopic']['slug'])); ?>
<?php endif;?>
<table cellpadding="0" cellspacing="0" border="1">
<?php 
$i = 0;
foreach ($knowledgeTopic['KnowledgeQuestion'] as $question): 
    $i++;
?>
	<tr>
		<td>
			<div class="quest">
				<?php echo $this->Html->link($i.". ".$question['question'], array('controller'=> 'knowledge_questions','action'=>'view', $question['slug']),null,false,false); ?>
			</div>
		</td>
		<?php if ($isAccess):?>
		<td class="actions"> 
 <?php echo $this->Html->link("<img  src='/img/edit.gif'/>", array('controller'=> 'knowledge_questions','action'=>'edit', $question['id']),null,false,false); ?>
 <?php echo $this->Html->link("<img  src='/img/delete.gif'/>", array('controller'=> 'knowledge_questions','action'=>'delete', $question['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $question['id']), false); ?>
		</td>
		<?php endif;?>
	</tr>
	<tr>
		<td colspan="2">
		<?php if (!empty($question['KnowledgeAnswer'])):?>
			<table cellpadding="0" cellspacing="0" border="0">
				<?php foreach ($question['KnowledgeAnswer'] as $answer): ?>
					<tr>
						<td>
                			<?php echo $answer['answer']?>
                		</td>
                		<?php if ($isAccess):?>
                		<td class="actions">
<?php echo $this->Html->link("<img  src='/img/tree/edit.gif'/>", array('controller'=> 'knowledge_answers','action'=>'edit', $answer['id']),null,false,false); ?>
<?php echo $this->Html->link("<img  src='/img/tree/delete.gif'/>", array('controller'=> 'knowledge_answers','action'=>'delete', $answer['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $answer['id']),false,false); ?>

                		</td>		
                		<?php endif;?>
			    	</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
		<?php if ($isAccess):?>
		<div class="actions">
        	<ul>
        		<li><?php echo $this->Html->link(__('New Knowledge Answer'), array('controller'=> 'knowledge_answers', 'action'=>'add',$question['id'])); ?> </li>
        	</ul>
        </div>
        <?php endif;?>
		</td>
	</tr>
<?php endforeach; ?>

</table>
<?php if ($isAccess):?>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('New KnowledgeQuestion'), array('controller'=> 'knowledge_questions','action'=>'add',$knowledgeTopic['KnowledgeTopic']['id'])); ?></li>
	</ul>
</div>
<?php endif;?>