<?//FULL version
  echo $this->Html->script('tiny_mce/tiny_mce.js');
?>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		force_br_newlines : true,
		forced_root_block : '',
		mode : "exact",
		elements : 'BlogpostDescription',
		theme : "advanced",
		plugins : "safari,pagebreak,style,table,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,forecolor,backcolor,|,hr,|,sub,sup,|,charmap",
		theme_advanced_buttons2 : "image,|,bullist,numlist,|,undo,redo,|,link,unlink,|,code,preview",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "center",
		theme_advanced_statusbar_location : "bottom",
		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",
		height : 400
	});
	
	
</script>

<script type="text/javascript">
$(document).ready(function() {
	$("#Addpost").validate({
		rules: {
			"data[Blogpost][title]": "required",
			"data[Blogpost][description]": "required"
		},
		messages: {
			"data[Blogpost][text]":  "Please enter your title",
			"data[Blogpost][description]":  "Please enter your description"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>

<div class="blogposts">

        <?php echo $this->Form->create('Blogpost', array('enctype'=>"multipart/form-data",'url' => array('action' => 'add'), 'id' => 'Addpost'));?>
            <fieldset style="padding-left:0px;">
            <?php //echo $this->Form->input('Post name');?>
            <?php
                echo $this->Form->input('Blogpost.title', array('size' => 70));
            ?>
        <div class="textarea">
                <?php
                    echo $this->Form->input('Blogpost.description', array('rows' => 20, 'div' => false));
                    echo $this->Image->manageImages('Blogpost', false);
            ?>
        </div>
           	<div class="input toleft"><?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?></div>

               </fieldset>
</div>
