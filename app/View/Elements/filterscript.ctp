<script type="text/javascript">
$(document).ready(function() {

    if ( $.cookie('<?php echo $filterCookie ?>') == 'show' ) {
        $('div.filter').show();
    } else {
        $.cookie('<?php echo $filterCookie ?>', 'hide', {path: '/'});       
    }

    $('#showfilter').click(function(){
    	$.cookie('<?php echo $filterCookie ?>', $.cookie('<?php echo $filterCookie ?>') == 'show' ? 'hide' : 'show', {path: '/'});
		$('div.filter').toggle();
        return false;
    });

});
</script>