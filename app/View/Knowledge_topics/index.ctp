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
             //$('#multiTree li:has("ul")').find('a:first').prepend('<em class="marker"></em>');
             
             $('#multiTree li span').click(function () {
                //var a = $('a.current',this.parentNode);
                //a.toggleClass('current');
                var li=$(this.parentNode);
                if (!li.next().length) {
                    li.find('ul:first > li').addClass('last');
                } 
            });

             $('#multiTree .marker').click(function () {
            	 var ul=$('ul:first',(this.parentNode).parentNode);
            	 
                 if (ul.length) {
                     ul.slideToggle(300);
                     var em=$('em:first',this.parentNode);// this = 'li span'
                     em.toggleClass('open');
                 }
             }); 

	}
/////////////////////////////
     var moveTreeElementData = {'from':{'pk':0,'name':''}, 'to':{'pk':0,'name':''}};
     function moveTreeElement(pk, name, moveUrl) {
     	//last slash
     	if (moveUrl.charAt(moveUrl.length - 1) != '/') {
     		moveUrl += '/';
     	}
     	
     	if (moveTreeElementData.from.name == '') {
     		if (pk == 0) {
     			return;
     		}
     		moveTreeElementData.from = {'pk':pk, 'name':name};
     		alert("You have selected '"+name+"' element to move.\n Now select destination element.");
     	} else if (moveTreeElementData.to.name == '') {
     		moveTreeElementData.to = {'pk':pk, 'name':name};
     		var res = confirm("Move element '"+moveTreeElementData.from.name+"' to '"+moveTreeElementData.to.name+"'?");
     		if (res) {
     			moveUrl += moveTreeElementData.from.pk+'/'+moveTreeElementData.to.pk+'/';
     			$.get(moveUrl, reloadOnSuccessJsFunction);
     		}
     		moveTreeElementData = {'from':{'pk':0,'name':''}, 'to':{'pk':0,'name':''}}; //empty
     	}
     }
////////////////
 
    var reloadOnSuccessJsFunction = function(response) {
		if (response == '') {
			 $('#multiTree').load('/knowledge_topics/index',function(){initTree();}); 			 
		} else {
			alert(response);
		}
	};
    </script>

<div class="knowledgeTopics index">

<div id="multiTree" style="float: left; width: 43%; padding-right:2%; line-height:25px">
<?php echo $this->element('knowledge_tree',array('onlyRead'=>$isAccess,'hide'=>false));?>
</div>
<div id="knowledgeKontent" style="float: left; width: 50%; padding-left:2%; padding-top:25px">

</div>

</div>
