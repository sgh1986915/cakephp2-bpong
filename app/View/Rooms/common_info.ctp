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
<br />Creator:
	Login - <?php echo $roomInfo['Creator']['lgn']; ?>
	 ,email - <?php echo $roomInfo['Creator']['email']; ?>
<?php if(!empty($roomInfo['Mates'])): ?>
	<br />Mates:
	<?php foreach ($roomInfo['Mates'] as $index => $value) { ?>
		  Login - <?php echo $value['User']['lgn']; ?>
		, email - <?php echo $value['User']['email']; ?>
	<?php } ?>
<?php endif; ?>