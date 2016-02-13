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

<div class="knowledgeTopics index">

<div id="multiTree" style="float: left; width: 43%; padding-right:2%;">
<?php echo $this->element('knowledge_tree',array('onlyRead'=>true,'parentsId'=>$parentsId,'hide'=>false));?>
</div>
<div id="knowledgeKontent" style="float: left; width: 50%; padding-left:2%; padding-top:25px">

</div>

</div>
