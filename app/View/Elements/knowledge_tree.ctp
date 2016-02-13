<?php if (!isset($parentsId)) $parentsId = array();?>
<?php if (!isset($onlyRead) && $onlyRead):?>
    <h3>Knowledge Topics &nbsp;<?php echo $this->Html->link("<img  src='/img/tree/add.gif'/>", array('action'=>'add'), array('escape' => false),false,false); ?></h3>
    <?php  echo $this->Tree->multiTree($topics,'/KnowledgeTopic/id','/KnowledgeTopic/name','/knowledge_topics/add','/knowledge_topics/setParent','/knowledge_topics/edit','/knowledge_topics/delete','/knowledge_topics/sort',$topicId,$hide,$parentsId);?>
<?php else:?>
	<h3>Knowledge Topics</h3>
    <?php  echo $this->Tree->multiTree($topics,'/KnowledgeTopic/id','/KnowledgeTopic/name',NULL,NULL,NULL,NULL,NULL,$topicId,$hide,$parentsId);?>
<?php endif;?>