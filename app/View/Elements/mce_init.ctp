<?//FULL version
  echo $this->Html->script('tiny_mce_4/tiny_mce.js');
?>

<script type="text/javascript">
function myCustomCleanup(type, value) {
    //alert("Value HTML string: " + value);
    //<!--[if gte mso
    
    if (type == "insert_to_editor") {    	
    	//var pattern = /<!--[if gte mso(\w+)<!--[endif]-->/;
    	var pattern = /<!--\[if gte mso[\s\S]+\[endif\]-->/g;
    	value = value.replace(pattern, '');    	
    	//alert (value);
    }	 
    return value;
}

	tinyMCE.init({
		// General options
        remove_script_host : false,
        convert_urls : false,		
		force_br_newlines : true,
		forced_root_block : '',
		mode : "exact",
	    elements : '<?php echo $name ?>',
		theme : "advanced",
		plugins : "safari,pagebreak,style,table,advimage,advlink,insertdatetime,preview,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",
		// Theme options
		cleanup_callback : "myCustomCleanup",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,forecolor,backcolor,|,hr,|,sub,sup,|,charmap",
		theme_advanced_buttons2 : "bullist,numlist,|,undo,redo,|,link,unlink,|,code,preview,pasteword",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "center",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "<?php echo STATIC_BPONG?>/css/word.css"
			
	});
	
	
</script>