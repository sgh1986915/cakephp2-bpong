<script type="text/javascript">
$(document).ready(function() {
	<?php if (!$isLoggined):?>
    $('#login').click();
    <?php endif;?>
});
</script>

<div style='text-align:center;font-size:15px;font-weight:bold;'>
	<br/><br/>
	<?php if (!$isLoggined):?>
	Please log in to join the organization "<?php echo $organization['Organization']['name'];?>"!
	<?php else:?>
	Do you want to join the organization "<?php echo $organization['Organization']['name'];?>"?
	<br/><br/>
	<a href="/o_join/<?php echo $organization['Organization']['slug'];?>/1"><img src="<?php echo STATIC_BPONG;?>/img/buttons/join.gif" border="0"></a>	
	<?php endif;?>
</div>