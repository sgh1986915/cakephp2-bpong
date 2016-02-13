<?php echo $modelInformation[$model]['name']?><br/>
<?php if (!empty($modelInformation[$model]['start_date'])):?>
	<strong>Start date: </strong><?php echo $this->Time->niceDate($modelInformation[$model]['start_date'])?><br/>
<?php endif;?>
<?php if (!empty($modelInformation[$model]['end_date'])):?>
	<strong>End date: </strong><?php echo $this->Time->niceDate($modelInformation[$model]['end_date'])?><br/>
<?php endif;?>

<!-- People in team-->
<?php if (!empty($modelInformation[$model]['min_people_team'])):?>
	<strong>Min people per team: </strong><?php echo $modelInformation[$model]['min_people_team']?><br/>
<?php endif;?>
<?php if (!empty($modelInformation[$model]['max_people_team'])):?>
	<strong>Max people per team: </strong><?php echo $modelInformation[$model]['max_people_team']?><br/>
<?php endif;?>
<!--EOF People in team-->

<?php if(!empty($errors)):?>
<div style="color:red;">
	You can not assign this team to this <?php echo $model;?>:<br/>
	<?php echo $errors?>
</div>
<?php endif;?>