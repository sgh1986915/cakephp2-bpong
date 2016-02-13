<?php $this->pageTitle = 'Beer Pong | Submit Content'; ?>
<?php
echo $this->Html->css(array(STATIC_BPONG.'/css/uploadify.css'))
	.$this->Html->script(array(STATIC_BPONG.'/js/uploadify/jquery.uploadify.v2.1.0.min.js', STATIC_BPONG.'/js/uploadify/swfobject.js'));
echo $this->Html->css(array('stylish-select.css'));
echo $this->Html->script('jquery.stylish-select.min.js');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#selectSubmitType').sSelect();
	});
</script>
<?php
            if (empty($contentType)) {
                $contentType = 0;
            }
?>
<script type="text/javascript">
<?php if (!$contentType):?>
	$('#selectSubmitType').val('0');
<?php endif;?>
var submitType = null;
	function contentLoading() {
		return '<div style="text-align:center;"><br/><img src="/img/loader_verify.gif" border="0"></div>';
	}
	function selectSubmitType() {
		$.ajaxSetup({cache:false});
		submitType = $('#selectSubmitType').val();
		$('#submit_content').html(contentLoading());
		switch(submitType){
		case "image":
			$('#submit_content').load('/Submissions/upload_image/', function() {});
		  break;
		case "link":
			$('#submit_content').load('/Submissions/upload_link/', function() {});
		  break;
		case "video":
			$('#submit_content').load('/Submissions/upload_video/', function() {});
			  break;
		case "0":
			$('#submit_content').html('');
			  break;
		}
	}
</script>
<style type="text/css" media="screen, projection">
.text {margin-top:10px !important;}
</style>

<h2 style='margin-top:0px;margin-bottom:0px;'>Submit Content</h2>
<hr/><div style='text-align: justify;'>
    Here at BPONG.COM we think that all beer pong content on the web should be in one place - right here. That's why we created the BPONG Submission System,
    which allows anyone to submit their awesome/stupid/funny/nsfw images, links, and videos directly to the BPONG.COM Feeds, where they will be
    ruthlessly ranked, tagged, and commented on by the fine young men and women of the global <br/> beer pong community.</div>
<div class="submit_package">
	<span class='submit_nums'>1)</span>  <b>What are you submitting?</b>&nbsp;
    <?php echo $this->Form->input('uploadType', array('id' => 'selectSubmitType', 'class' => 'custom-select' ,'label' => false, 'div' => false, 'type' => 'select', 'onchange' => 'selectSubmitType();', 'options' =>
    array('0' => 'Select One', 'image' => 'Image(s)', 'link' => 'A Link', 'video' => 'A Video'), 'selected' => $contentType)); ?>
</div>
<div id='submit_content'>
	<?php if ($contentType == 'image' && !empty($album_id)):?>
        <?php echo $this->element('/submissions/upload_image'); ?>
	<?php elseif ($contentType == 'video' && !empty($album_id)):?>
        <?php echo $this->element('/submissions/upload_video'); ?>
	<?php endif;?>
</div>
<script type="text/javascript">
<?php if (!$contentType):?>
	$('#selectSubmitType').val('0');
<?php endif;?>
</script>
