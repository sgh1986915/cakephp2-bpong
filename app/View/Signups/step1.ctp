 <?php echo $this->Html->css(array(STATIC_BPONG.'/css/jquery.alerts.css'))
                  .$this->Html->script(array(JS_NBPL.'/jquery.ui.draggable.js',JS_NBPL.'/jquery.alerts.js'))
 ?>
<div id="SignupAjax"><!-- This is for AJAX please don't remove this DIV -->

<noscript>
    <div style="text-align: center; padding-top: 80px; font-size: large;">
    Sorry but you can not signup because Javascript is disabled.<br> 
    Please enable Javascript and try again.<br>
    Thank you.
    </div>
</noscript>
<script type="text/javascript">
    $(document).ready(function() {
        show = true;
    	if (navigator.appVersion.indexOf("AOL") != -1) {
    		show = false;	
    	}

        if (show) {
        	$('#jsenabled').show('fast');
        } else {
        	$('#browserNotSupported').show('fast');
        }
        
		<?php if ($gotoStep):?>
			$('#SignupAjax').html(showLoaderHtml());
	     	$('#SignupAjax').load("/signups/step<?php echo $gotoStep; ?>/<?php echo $modelName."/".$slug ?>",{cache: false});
		<?php endif;?>
    });

    function SubmitFrom () {
    	if (!$('#ReadandAgree').is(":checked")) {
    		 alert("You have to read and agree to the terms.");
    	} else if (!$('#Understand').is(":checked"))  {
    		 alert("You have to agree that you understand and agree that all monies paid are not refundable for any reason.");
    	} else{
			var loader = showNextLoading();
   		 	$('#next').html(loader);
   		    $.ajaxSetup({cache:false});
   	     	$('#SignupAjax').load("/signups/step2/<?php echo $modelName."/".$slug ?>",{cache: false});
        		 
    	}
    }
</script>
	   <div id="browserNotSupported" style="text-align: center; padding-top: 80px; font-size: large; display: none;">
	   	Sorry but your browser is not supported. Try another browser.
	   </div>	
       
       <div id="jsenabled" style="display: none;">
            <h2>Agreements</h2>
          <div id="aggreement" style=" height: 500px; overflow: auto; padding:20px 50px 20px 20px; border:1px dotted #ccc">
          	<?php echo $this->element('/signup/agreement');?> 
          </div>
        <div style="padding:15px 0 0 20px">
        	<div style="width:120px; float:left" class='step_previous'><input onclick='window.document.location.href = "<?php echo MAIN_SERVER;?>/wsobp";' type="button" value="Previous" class="submit" /></div>
        	<div id="next" class='step_next' style="width:100px; float:left">
           		<input type="button" value="Next" class="submit" onclick="SubmitFrom()" />
           </div>
        <br />
        </div>
     </div>


</div>
