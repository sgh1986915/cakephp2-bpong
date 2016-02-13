<?php 
	echo $this->element('mce_init', array('name' => 'ContentContent')); 
?>
<script type="text/javascript">
$(document).ready(function() {

    $('#save').click(function(){
	    $('#save').hide();
	    $('#loading').show();

	    $.post('<?php echo $this->Html->url('/ajaxes/edittoken/'.$this->request->data['Content']['id']); ?>'
	           ,{
	           		 token           :  $('#ContentToken').val()
	           		,title           :  $('#ContentTitle').val()
	           		,language_id     :  $('#ContentLanguageId').val()
	           		,content         :  tinyMCE.getContent()
	            }
	           ,function(response){
	               setTimeout("saveAjax('"+escape(response)+"')", 400);
	            });
	    return false;
    });

});

function saveAjax(response) {
    if ( response != '1' ) {
        self.parent.tb_remove();
        alert('error occured: '+ response);
        return false;
    }
    self.parent.window.location.reload(true);
}

</script>
<div class="contents form">
Content
	<fieldset>
 		<legend>Edit Content</legend>
 		Language
	<?php echo $this->Form->input('Content.id',array('type'=>'hidden'));?>
	<?php echo $this->Form->input('Content.token',array('type'=>'hidden'));?>
		
	<?php echo $this->Form->input('Content.language_id',array('type' => 'select','label'=>'','options' => $languages));
		  echo $this->Form->input('Content.title',array('label' => 'Title: '
            	    							  ,'div'   => array('class' => 'input')
            	    							  ,'size'  => '45'
                     							  ) 
                );?>

	<?php	echo $this->Form->input(
              'Content.content'
             ,array(
                 'label' => 'Content: '
                ,'type'  => 'textarea'
        	    ,'div'   => array('class' => 'input')
        	    ,'cols'  => '20'
              )//array
           )//input
	?>
	</fieldset>
	<input type="button" id="save" value="Save" />
	<?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif', array('style' => 'display:none;', 'alt'=>'loading', 'id' =>'loading')); ?>
</div>