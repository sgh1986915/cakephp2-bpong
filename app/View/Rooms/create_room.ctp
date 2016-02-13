<?php if (!empty($questions)):?>
<form action="" method="post" id="SignupAddForm" class="p10 left35">
	 <h2>Answer the questions</h2>
	<fieldset>
		<?php echo $this->element('questions');?>
	</fieldset>
	<div class="submit">
		<input type="submit" value="Submit" />
	</div>
</form>
<?php else: ?>
<div class="you_have_no">Can not find questions in the database</div>
<?php endif;?>
