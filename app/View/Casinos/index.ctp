<?php if (!empty($rooms)) :?>
<div class="venues index" style="padding:0">
	<h2>Rooms</h2>
	<fieldset>
	<form action="" method="post" name="Filter">
		<?php 
			echo $this->Form->input( 'Criteria.user', array( 'label' => 'User:' ) );
		?>
	<div style="width:60%; float:left"><?php echo $this->Form->input( 'Criteria.tornevent', array( 'type' => 'select'
														, 'label' => 'Name:'
														, 'options' => $type_options
														, 'empty' => true ) ); ?>
	</div>
	<div style="width:40%; float:left"><?php echo $this->Form->input( 'Criteria.status', array(	  'type' => 'select'
														, 'label' => 'Status:'
														, 'options' => $status_options
														, 'empty' => true ) ); ?>
	</div>
	<input type="submit" value="Filter"/>
	
	<?php if( !empty( $csvNotEmpty ) ): ?>
		<input type="button" value="Download CSV" onclick="window.location.href='/casinos/getCsv';" class="sbmt_ie"/>
	<?php endif;?>

	</form>
    </fieldset>
</div>
<br />
<table cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->Paginator->sort('User', 'User.lgn');?></th>
		<th><?php echo $this->Paginator->sort('Name', 'Tournament.name');?></th>
		<th><?php echo $this->Paginator->sort('status');?></th>
		<th class="actions">&nbsp</th>
	</tr>
	<?php
	$i = 0;
	foreach ($rooms as $room):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?>>
			<td>
				<?php echo $room['UserCreator']['lgn']; ?>
			</td>
			<td>
				<?php echo $room[$room['SignupRoom']['model']]['name']; ?>
			</td>
			<td>
				<?php echo $room['SignupRoom']['status']; ?>
			</td>
			<td class="actions">
				
				<?php
					$viewimage = $this->Html->image(STATIC_BPONG.'/img/view.gif', array( 'alt' => 'View' )); 
					echo $this->Html->link( $viewimage, array( 'action' => 'view', $room['SignupRoom']['id'] ), array( 'escape' => false )); 
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<?php else:?>
No rooms
<?php endif;?>