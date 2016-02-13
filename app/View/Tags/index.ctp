<h2>Tag's Models</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Model Name / Show tags</th>
</tr>
<?php
$i = 0;
foreach ($models as $model):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->link($model['ModelsTag']['model'], array('action'=>'modelShow', $model['ModelsTag']['model'])); ?>
		</td>
	</tr>
	
<?php endforeach; ?>
</table>

