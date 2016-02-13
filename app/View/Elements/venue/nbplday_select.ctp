<script type="text/javascript">
var nbpldays_num = <?php if (empty($nbpldays)) { echo '0';} else { echo intval(count($nbpldays));};?>;
function addNewDay() {
	$("#nbplday_loader").show();
	nbpldays_num = nbpldays_num+1;
	$.get('/nbpldays/showNew/' + nbpldays_num, function(data) {
		$("#new_days").append(data);
		$("#nbplday_loader").hide();
		});
	return false;
}

</script>

<h3 style='padding-left:80px;'>NBPL Days:</h3>
 <div style='float:left;width:100%;padding-left:150px;padding-bottom:20px;'>        
<?php if (!empty($nbpldays)):?>
	<?php $i=0;foreach ($nbpldays as $nbplday):$i++;?>
		<?php echo $i; ?>. 
		<?php echo $this->Formenum->hidden('Nbpldays.' . $nbplday['Nbplday']['id'] . '.id',array('value' => $nbplday['Nbplday']['id']));?>		
		<?php echo $this->Formenum->input('Nbpldays.' . $nbplday['Nbplday']['id'] . '.nbplday',array('selected' => $nbplday['Nbplday']['nbplday'], 'label' => false, 'div' => false, 'type' => 'select', 'options' => Configure::read('Weekdays')));?>
		&nbsp;—&nbsp; <?php echo $this->Formenum->input('Nbpldays.' . $nbplday['Nbplday']['id'] . '.nbplstarttime',array('value' => $nbplday['Nbplday']['nbplstarttime'], 'label' => false, 'div' => false, 'type' => 'time'));?>
		&nbsp;<a href="/nbpldays/delete/<?php echo $nbplday['Nbplday']['id'];?>" onclick="return confirm('Are you sure?');">Delete</a><br/><br/>
	<?php endforeach;?>
<?php endif;?>
<div id='new_days'></div>
<a href="#" onclick="return addNewDay();">Add new NBPL day</a><img id='nbplday_loader' style='display:none;margin-left:5px;' src='/img/ajax_loader_m.gif' border="0"/>
</div>