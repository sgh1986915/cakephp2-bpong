<h2>Signups</h2>
<?php if (!empty($signups)): ?>
<table>
<tr>
	<th><?php echo $this->Paginator->sort('signup_date');?></th>
	<th>Name</th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo $this->Paginator->sort('paid');?></th>
	<th><?php echo $this->Paginator->sort('discount');?></th>
	<th><?php echo $this->Paginator->sort('total');?></th>

	<th></th>
</tr>
<?php 
$i = 0;
foreach ($signups as $signup): 
    	$class = '';
    	if ($i++ % 2 != 0) {
    		$class = ' class="alt"';
    	}
?>
<tr<?php echo $class;?>>
	<td> <?php echo $this->Time->niceDate($signup['Signup']['signup_date']) ?> </td>
	<td> <?php echo $signup['Signup'][$signup['Signup']['model']]['name'] ?> </td>
	<td> <?php echo $signup['Signup']['status'] ?> <?php if ($signup['Signup']['for_team']):?>(for entire team)<?php endif;?></td>
	<td> $<?php echo sprintf("%.2f", $signup['Signup']['paid']) ?></td>
	<td> $<?php echo sprintf("%.2f", $signup['Signup']['discount'])?></td>
	<td> $<?php echo sprintf("%.2f", $signup['Signup']['total']) ?></td>

	<td>
	<?php if ($userSession['id'] == $signup['Signup']['user_id']):?>
		<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View signup details" title="View signup details"/>', SECURE_SERVER . '/signups/signupDetails/' . $signup['Signup']['id'], array('escape'=>false)); ?>
	<?php else:?>
		<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View signup details" title="View signup details"/>', SECURE_SERVER . '/signups/signupDetailsTeammate/' . $signup['Signup']['id'], array('escape'=>false)); ?>
	<?php endif;?>
		 <!-- Use promocode -->
		<?php if ($signup['Signup']['status'] == 'paid' && (empty($signup[$signup['Signup']['model']]['finish_signup_date']) || $this->Time->fromString($signup[$signup['Signup']['model']]['finish_signup_date'])>=strtotime(date("Y-m-d")))):?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/use_promo.gif" alt="Use promocode" title="Use promocode"/>', SECURE_SERVER.'/signups/usePromocode/'.$signup['Signup']['id'], array('escape'=>false)); ?>
		<?php endif;?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php else:?>
	<div class="you_have_no">You are not signed up!</div>
<?php endif;?>