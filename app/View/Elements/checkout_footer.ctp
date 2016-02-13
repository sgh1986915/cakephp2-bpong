<br/><br/>
<div  style="float:right; margin-top:15px">
<?

$next_step=$step+1;
$prev_step=$step-1;
$userSession = $_SESSION['loggedUser'];
if($userSession['id']!=VISITOR_USER){
	if($step==1){
		$next_step=3;
	}elseif($step==3){
		$prev_step=1;
	}
}

/*
switch ($step) {
  case 1: ?><a href="/carts/step2/" class="buttons">Go to step2</a><?
    break;
  case 2: ?><?php echo $this->Form->submit('To step3',array('class'=>'buttons')) ?><?
    break;
  case 3: ?><a href="/carts/step4/" class="buttons">Go to step4</a><?
    break;
  case 4: ?><input type="submit" value="Go to step 5" class="buttons"><?
    break;
}
*/
?>
</div>
<div class="clear"></div>
<?php echo $this->element('checkout_totals');  ?>


