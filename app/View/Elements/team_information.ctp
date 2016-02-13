<?php echo $teamInformation['Team']['name']?><br/>
<?php if (!empty($teammates)):?>
	Teammates:<br/>
	<?php $i = 0;?>
	<?php foreach ($teammates as $teammate):?>
	    <?php $i++;?>
		<?php echo $i.". ".$teammate['User']['firstname']." ".$teammate['User']['lastname'] ?>(<?php echo $teammate['User']['lgn']?>)<br/>
	<?php endforeach;?>
<?php endif;?>

<?php if(!empty($errors)):?>
<div style="color:red;">
	You can not assign this team to this <?php echo $model;?>:<br/>
	<?php echo $errors?>
</div>
<?php endif;?>