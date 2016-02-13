<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<?php echo $this->Html->css('comments.css'); ?>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/comments/comments.js'></script>
<script type="text/javascript">
/*For comment*/

 $(function() {
	 <?php if (isset($this->request->params['url']['addcomment'])):?>
		 commentForm(0);
	  <?php endif;?>
	  <?php if (isset($this->request->params['url']['reply_to'])):?>
	  	commentForm(<?php echo $this->request->params['url']['reply_to']; ?>);
	  <?php endif;?>
 })

function showResponse(responseText)  {

	  $('.coment .loader').fadeOut(100, function(){$('.coment .submit_gray').css({display:'inline-block'}).animate({opacity:1},100)});
	  if (responseText != "") {
		    $('#error').html(responseText);
	  		$('#error').show('slow');
	  } else {
		  $('#comment_textarea').val("");
		  var iframe = document.getElementById(XBB.iframe_id).contentWindow;
		  iframe.document.forms.xbb.xbb_textarea.value = "";
		  $('#error').hide();
		  $('#preview_'+active_form_id).html("");
		  $('#error_'+active_form_id).html("");
		  //$('#reply_'+active_form_id).html("");
		  $('#comments').load("/comments/show/<?php echo $model."/".$modelId ?>" ,{cache: false},
		  			function(){
			      		$('#comment_loader').hide();
			      		if ($('#comment_counter_text').html()) {
				  		var commentNum = parseInt($('#comment_counter_val').html()) + 1;
				  		$('#comment_counter_val').html(commentNum);
				  		if (commentNum == 1) {
				  			$('#comment_counter_text').html('Comment');
						 } else {
					  			$('#comment_counter_text').html('Comments');
							}
					  }
		 });
	  }
	}
</script>
<div class="clear"></div>
<?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
    <?php echo $this->Form->create('Comment', array('id' => 'Comment','url' => '/comments/add'));?>
    <?php if(empty($hideAddLink) && count($comments)>0):?>
    <a href="#add_comment" class='add_link' onclick="commentForm('0');">Add a New comment</a>
    <?php endif;?>
    <div class="clear"></div>
    <div class="error" id="error" style="display: none;"><?php echo __("Can not add new Comment.");?></div>
    <?php echo $this->Form->hidden('Comment.model_id',array('value' => $modelId));?> <?php echo $this->Form->hidden('Comment.model',    array('value'   => $model));?>
    <?php echo $this->Form->hidden('Comment.parent_id',array('value'   => ''));?> <?php echo $this->Form->hidden('Comment.url',          array('value'   => $this->request->here));?>
<?php endif;?>
<div id="comments">
  <?php echo $this->element("comments/show",array('model'=>$model,"modelId"=>$modelId,'comments'=>$comments,'commentVotes'=>$commentVotes));?>
</div>

<?php if (empty($hideAddLink) && isset($LoggedMenu) && !empty($LoggedMenu)): ?>
    <a href="#add_comment" name="add_comment" class='add_link' onclick="commentForm('0'); return false;">Add a New comment</a>
    <div class='prev1' id="preview_0"></div>
    <div class='comment_error' id="error_0"></div>
    <div id="reply_0" ></div>

    <!-- ADD COMMENT FORM -->
    <div id="replyForm"  style="display: none;">
    	<div id='add_comment_form'>
    		<?php echo $this->Form->input('Comment.comment', array('type' => 'textarea', 'label' => false, 'div' => false, 'id' => 'comment_textarea')); ?>
    		<div class="controls_wrapper" style="height: 43px;">
    			<div id='preview_loader' style='display:none;margin-top:10px; float: left;'>
    				Loading...<br/><img src='<?php echo STATIC_BPONG?>/img/loader_verify.gif' />
    			</div>
    			<div class='submit'>
    				<input class="submit_gray" style='float:left;margin-right:15px;' type='submit' value='Submit' />
    				<input class="preview submit_gray" style='float:left;margin-right:15px;' type="button" value="Preview" onclick="preview();" />
    				<input class="cancel submit_gray" style='float:left;margin-right:15px;' type="button" value="Cancel" onclick="cancel();" />
    				
    			</div>
    		</div>
        </div>
        <div id='comment_loader' style='display:none;margin-top:5px;padding-left: 40px; padding-bottom: 20px'>
        	Loading...<br/><img src='<?php echo STATIC_BPONG?>/img/loader_verify.gif' />
        </div>
    </div>
    <!-- EOF ADD COMMENT FORM -->

      <?php echo $this->Form->end();?>
  <?php endif;?>
  <div class="clear"></div>