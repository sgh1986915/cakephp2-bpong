<?php
$class_next='class="nextstep"';
$class_step='class="step"';
$class_step1=$class_step2=$class_step3=$class_step4=$class_step5=$class_step6=$class_step7='';
switch ($step){
	case 1:
		$class_step1='class="step"';
	break;
	case 2:
	    $class_step1=$class_next;
	    $class_step2=$class_step;
	break;
	case 3:
	    $class_step1=$class_step2=$class_next;
	    $class_step3=$class_step;
	break;
	case 4:
    	$class_step1=$class_step2=$class_step3=$class_next;
    	$class_step4=$class_step;
	break;
}
?>
<ul class="content">
	<li <?php echo $class_step1?>>
        <?php if($step>1){?><a href="javascript:GotoStep(1,'prev');"><?php } ?><p>Enter and Verify Address</p><?php if($step>1){?></a><?php } ?>
    </li>
    <li class="tab">
    	<img src="<?php echo STATIC_BPONG?>/img/tab.gif" alt=">>" />
    </li>
    <li <?php echo $class_step2?>>
                <?php if($step>2){?><a href="javascript:GotoStep(2,'prev');"><?php } ?><p>Total calculation</p><?php if($step>2){?></a><?php } ?>
    </li>
    <li class="tab">
    	<img src="<?php echo STATIC_BPONG?>/img/tab.gif" alt=">>" />
    </li>
    <li <?php echo $class_step3?>>
                <?php if($step>3){?><a href="javascript:GotoStep(3,'prev');"><?php } ?><p>Credit card information</p><?php if($step>3){?></a><?php } ?>
    </li>
    <li class="tab">
    	<img src="<?php echo STATIC_BPONG?>/img/tab.gif" alt=">>" />
    </li>
    <li <?php echo $class_step4?>>
                <?php if($step>4){?><a href="javascript:GotoStep(4,'prev');"><?php } ?><p>Order confirmation</p><?php if($step>4){?></a><?php } ?>
    </li>
</ul>
<div class="clear"></div>