<div class="bord" style="background-color:#efefef">
<strong>Package:</strong> <?php echo $packagename; ?><br />
<strong>People in room:</strong> <?php echo $roomInfo['Room']['people_in_room']; ?><br />
<strong>Status of a Room:</strong> <?php echo $roomInfo['Room']['status']; ?><br />
<strong>Answers:</strong>
<?php
	foreach( $answers as $index => $answer) {
		echo $answer['Options']['optiontext'];
		if ( $index != count($answers)-1 ) {
			echo ", ";
		}
	}
?>
</div>
<div style="width:330px; float:left; padding:15px; margin:10px" class="bord">
<h3>Creator:</h3>
	<div style="float: left;width: 300px;">
		 <strong>Fullname:</strong><?php echo $roomInfo['Creator']['firstname'] . " " . $roomInfo['Creator']['lastname']; ?>
		<br /> 
    <strong>Address:</strong> <?php 
						if ( !empty( $roomInfo['Address']['address'] ) ) {
							$address = array();
							
							if( $roomInfo['Address']['address'] != "" ) {
								$address[] = $roomInfo['Address']['address'];
							}		
							if( $roomInfo['Address']['city'] != "" ) {
								$address[] = $roomInfo['Address']['city'];
							}		
							if( isset($roomInfo['Provincestate']['name']) 
								&& $roomInfo['Provincestate']['name'] != "" ) {
								$address[] = $roomInfo['Provincestate']['name'];
							}		
							if( $roomInfo['Address']['postalcode'] != "" ) {
								$address[] = $roomInfo['Address']['postalcode'];
							}		
							
							echo implode( ", ", $address );
						}
					?>
		<br /><strong>Phone:</strong> <?php echo $roomInfo['Phone']['phone']; ?>
		<br /><strong>E-mail:</strong> <?php echo $roomInfo['Creator']['email']; ?>
	</div>
<div style="float:left; width:320px; margin-top:15px;" id="id_<?php echo $roomInfo['SignupRoommate']['id'];?>" name="confirmationblock">
		<span style="font-size:11px; color:#999; font-weight:bold">Confirmation code:</span> <input type="text" value="<?php echo stripslashes($roomInfo['SignupRoommate']['confirmation_code']);?>" name="confirmationcode"/>
		<input name="change" type="button" value="OK" class="submit_gray" style="width: 70px;"/>
	</div>
</div>
<div style="width:330px; float:left; padding:15px; margin:10px" class="bord">
<?php if(!empty($roomInfo['Mates'])): ?>
	<h3>Mates:</h3>
	<?php foreach ($roomInfo['Mates'] as $index => $value) { ?>
		<div style="float: left;width: 300px;">
			 <strong>Fullname:</strong> <?php echo $value['User']['firstname'] . " " . $value['User']['lastname']; ?>
			<br /> <strong>Address:</strong> <?php 
							if ( !empty( $value['Address']['address'] ) ) {
								$address = array();
								
								if( $value['Address']['address'] != "" ) {
									$address[] = $value['Address']['address'];
								}		
								if( $value['Address']['city'] != "" ) {
									$address[] = $value['Address']['city'];
								}		
								if( isset($value['Provincestate']['name']) 
									&& $value['Provincestate']['name'] != "" ) {
									$address[] = $value['Provincestate']['name'];
								}		
								if( $value['Address']['postalcode'] != "" ) {
									$address[] = $value['Address']['postalcode'];
								}		
								
								echo implode( ", ", $address );
							}
						?>
			<br /> <strong>Phone:</strong> <?php echo $value['Phone']['phone']; ?>
			<br /> <strong>E-mail:</strong> <?php echo $value['User']['email']; ?>
		</div>
		<div style="float:left; width:320px; margin-top:15px" id="id_<?php echo $value['SignupRoommate']['id'];?>" name="confirmationblock">
			<span style="font-size:11px; color:#999; font-weight:bold">Confirmation code:</span> <input type="text" value="<?php echo stripslashes($value['SignupRoommate']['confirmation_code']);?>" name="confirmationcode"/>
			<input name="change" type="button" value="OK" class="submit_gray" style="width: 70px;"/>
	 	</div>
	<?php } ?>
<?php endif; ?>
</div>
<div style="clear:both;"></div>
<hr style="height:1px; background-color:#CCC" />
<div style="text-align:center; margin:10px 0 20px 0">
	<input type="button" value="Back" class="submit_gray" style="width: 100px;" onclick="history.go(-1)"/>
</div>

<script language="JavaScript1.2" type="text/javascript">
	var fieldValue = new Array();
	//Hide all buttons at start
	$("div[@name='confirmationblock'] > input[@type='button']").attr("disabled","disabled").val("OK").hide();
	
	//Collect field values
	$("div[@name='confirmationblock'] > input[@type='text']").each( function(i) {
		fieldValue[$(this).parent().attr('id')] = $(this).val();
	});		
	
	//If value of input was changed, show button 
	$("div[@name='confirmationblock'] > input[@type='text']").keyup(function(e) {
		var parent_id = $(this).parent().attr('id');
		var buttonelem = $(this).siblings().filter("input[@type='button']"); 
		
		if( fieldValue[parent_id] == $(this).val() ) {
			$(buttonelem).attr("disabled", "disabled").val("OK").hide();
		} else {
			$(buttonelem).removeAttr("disabled").val("Save").show();
		}
		return true;
	});
	
	$("div[@name='confirmationblock'] > input[@type='button']").click(function() {
		var inputelement = $(this).siblings();
		var button_element = this;
		var parent_id = $(this).parent().attr('id');

		if( $.trim( $(inputelement).val() ) == "" ) {
			return false;
		}
		$(button_element).hide().parent().append("<img src='<?php echo STATIC_BPONG?>/img/loader.gif' alt='loading'/>");
		$.post("/casinos/changecode", {
									   id: parent_id
									 , code: $(inputelement).val() },
				function(data) {
					if (data != 0 && data != "") {
						$(button_element).siblings().filter("img").remove();
						$(button_element).removeAttr("disabled").val("Save").show();
						alert(data);
					}
					else {
						$(button_element).siblings().filter("img").remove();
						$(button_element).attr("disabled", "disabled").val("OK").hide();
						fieldValue[ parent_id ] = $(inputelement).val();
					}
				});
	});
</script>