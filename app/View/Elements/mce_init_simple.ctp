<?//FULL version
  echo $this->Html->script('tiny_mce_4/tiny_mce.js');
?>

<script type="text/javascript">
$(document).ready(function() {
	tinyMCE.init({
		// General options
        remove_script_host : false,
        convert_urls : false,		
		force_br_newlines : true,
		forced_root_block : '',
		mode : "exact",
		elements : '<?php echo $name ?>',
		theme : "simple",
		plugins : "safari,pagebreak,style,table,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "center",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "<?php echo STATIC_BPONG?>/css/word.css"

	});
});

</script>
