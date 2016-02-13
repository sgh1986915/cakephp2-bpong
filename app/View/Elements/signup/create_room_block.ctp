<form action="/rooms/createRoom/<?php echo $signupDetails['Signup']['id'];?>/<?php echo $userRole;?>" method="post" id="SignupAddForm" class="p10">
<?php echo $this->element('questions');?>
	<br/>
	<div class="submit">
		<input type="submit" value="Create Room" />
	</div>
</form>