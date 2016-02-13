<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

	<head>
	    <title>
	       <?php echo $title_for_layout ?>
	    </title>
        <!--link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /-->
        <?php echo $this->Html->charset('UTF-8')
                  .$this->Html->script('jquery') ?>
         <script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/jquery.validate.js"></script>
         <script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/jquery.form.js"></script>
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />	                  
	</head>

	<body>
	    <div class="content">
    		<?php echo $content_for_layout; ?>
    	</div>
	</body>
</html>