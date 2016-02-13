<?php 
$i = 0;
foreach ($teammates as $teammate):
	$i++;
	if (isset($teammate['User']['Address'][0])) {
		$homeAddress = $teammate['User']['Address'][0];
	} else {
		$homeAddress = array();	
	}
?>
<div style='width:100%;float:left;margin-bottom:10px;'>
	<div style='float:left;<?php if (!empty($homeAddress['city'])):?>padding-top:7px;<?php endif;?>'><a href="<?php echo '/u/' . $teammate['User']['lgn'];?>"><?php echo $this->Image->avatar($teammate['User']['avatar']);?></a></div>
	<div style='float:left;margin-left:10px;'>
		<strong><a href="<?php echo '/u/'.$teammate['User']['lgn'];?>" style='text-decoration: none;'><?php echo $this->Formater->userName($teammate['User'], 1);?></a></strong>
		<br/>
		<a href="<?php echo '/u/' . $teammate['User']['lgn'];?>" style='text-decoration:none;'><?php echo $teammate['User']['lgn'];?></a>
		<?php if (!empty($homeAddress['city'])):?><br/><span style='font-size:11px;'><?php echo ucwords(strtolower($homeAddress['city']));?></span><?php endif;?><?php if (!empty($homeAddress['Provincestate']['shortname'])):?>, <span style='font-size:11px;'><?php echo $homeAddress['Provincestate']['shortname'];?></span><?php endif;?>
	</div>
	<div style='float:right;padding-top:5px;'>
		<?php echo $this->element('/charts/user_pie_chart', array('chartIndex' => $i, 'winnings' => $teammate['stats']['wins'], 'losses' => $teammate['stats']['losses'], 'chartLink' => '/u/' . $teammate['User']['lgn']));?>
    </div>	
</div>

<?php 
endforeach;
?>