<?php if (!empty($signups)): ?>
<h2>Tournament Registrations</h2>
<table>
<tr>
	<th>Date</th>
	<th>Tournament</th>
	<th>Status</th>
	<th>Payments</th>
</tr>
<?php foreach ($signups as $signup): ?>
<tr>
	<td><?php echo $this->Time->niceDate($signup['Signup']['signup_date']) ?></td>
	<td><a href="/wsobp"><?php echo $signup['Tournament']['name'] ?></a></td>
	<td><?php echo $signup['Signup']['status'] ?></td>
	<td><a href="/payments/view/Signup/<?php echo $signup['Signup']['id'] ?>">Payments</a></td>
</tr>



<?php endforeach; ?>
</table>

<?php else: ?>
		<br /><div style="background-color:#fafafa; border:1px dotted #ccc; text-align:center; padding:20px; width:760px">You are not currently signed up for any events or tournaments</div>
<?php endif; ?>

<?php if (1!=1):  //Zaglushka?>


<?php if (!empty($tournaments)): ?>
<div class="tournaments index">
<h2>Tournaments</h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th style="width:330px"><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('start_date');?></th>
	<th><?php echo $this->Paginator->sort('end_date');?></th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($tournaments as $tournament):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
			<a href="/tournaments/view/<?php echo $tournament['Tournament']['slug']; ?>"> <?php echo $tournament['Tournament']['name']; ?></a>
		</td>
		<td>
			<?php echo $this->Time->niceDate($tournament['Tournament']['start_date']); ?>
		</td>
		<td>
			<?php echo $this->Time->niceDate($tournament['Tournament']['end_date']); ?>
		</td>

		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action'=>'edit', $tournament['Tournament']['slug'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging2">
	<?php echo $this->Paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled2'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next('next'.' >>', array(), null, array('class'=>'disabled2'));?>
</div>
<?php else: ?>

	there are no tournaments managed by you.

<?php endif; ?>
<?php if ($CreateTournaments): ?>
<div class="actions" style="padding-top:20px">
	<ul>
<li><span class="addbtn"><?php echo $this->Html->link('New Tournament', array('action'=>'add'), array('class'=>'addbtn3')); ?></span></li>
	</ul>
</div>
<?php endif; ?>
<?php endif;?>