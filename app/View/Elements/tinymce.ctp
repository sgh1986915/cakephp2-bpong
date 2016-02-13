<?php echo $this->Html->script('tiny_mce/tiny_mce.js'); ?>
<script type="text/javascript">
    tinyMCE.init({
	    // General options
	    force_br_newlines : true,
	    forced_root_block : '',
	    mode : 'textareas',
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