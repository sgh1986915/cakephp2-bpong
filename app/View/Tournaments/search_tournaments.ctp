<div class="tournaments index">
<h2>Tournaments</h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th style="width:330px"><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('start_date');?></th>
	<th><?php echo $this->Paginator->sort('end_date');?></th>
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

	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging2">
	<?php echo $this->Paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled2'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next('next'.' >>', array(), null, array('class'=>'disabled2'));?>
</div>