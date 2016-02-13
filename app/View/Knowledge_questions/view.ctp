    <link rel="stylesheet" href="/css/multiTree.css">
    <script type="text/javascript">
 $(document).ready(function () {
		<?php if (!empty($topicId)):?>
			  $('#knowledgeKontent').load('/knowledge_topics/view/<?php echo $topicId; ?>'); 
		<?php endif;?>
	 
		initTree(); 		
    })
    
    function nodeClick (a,id) {
    	 $('a.current').removeClass('current');
    	 $(a).toggleClass('current');
		
    	 $('#knowledgeKontent').load('/knowledge_topics/view/'+id);  
	}
    
    
    function initTree(){
                 $('#multiTree .marker').click(function () {
            	 var ul=$('ul:first',(this.parentNode).parentNode);

            	  $('#multiTree li span').click(function () {
                      //var a = $('a.current',this.parentNode);
                      //a.toggleClass('current');
                      var li=$(this.parentNode);
                      if (!li.next().length) {
                          li.find('ul:first > li').addClass('last');
                      } 
                  });
            	 
            	 
                 if (ul.length) {
                     ul.slideToggle(300);
                     var em=$('em:first',this.parentNode);// this = 'li span'
                     em.toggleClass('open');
                 }
             }); 

	}
   </script>
<div id="multiTree" style="float: left; width: 43%; padding-right:2%">
<?php echo $this->element('knowledge_tree',array('onlyRead'=>true,'parentsId'=>$parentsId,'hide'=>false));?>
</div>
<div class="knowledgeQuestions view">
<h2><?php echo __('Question');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Question'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $knowledgeQuestion['KnowledgeQuestion']['question']; ?>
			&nbsp;
		</dd>
		
	</dl>

<div class="related" style="float:right; width:95%; padding:10px; margin:20px 0 0">
	<h3><?php echo __('Answer');?></h3>
	<?php if (!empty($knowledgeQuestion['KnowledgeAnswer'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<?php
		$i = 0;
		foreach ($knowledgeQuestion['KnowledgeAnswer'] as $knowledgeAnswer):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $knowledgeAnswer['answer'];?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
</div>
