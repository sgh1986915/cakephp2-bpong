<?php if (!empty($rooms)) :?>

<div class="venues index" style="padding:0">
  <h2>Rooms</h2>
  <form action="" method="post" name="Filter">
    <?php echo $this->Form->input( 'Criteria.user', array( 'label' => 'User:' ) ); ?> <?php echo $this->Form->input( 'Criteria.tornevent', array( 'type' => 'select'
														, 'label' => 'Type:'
														, 'options' => $type_options
														, 'empty' => true ) ); ?> <?php echo $this->Form->input( 'Criteria.status', array(	  'type' => 'select'
														, 'label' => 'Status:'
														, 'options' => $status_options
														, 'empty' => true ) ); ?>
    <div class="submit">
      <input type="submit" value="Filter"/>
    </div>
  </form>
  <div class="heightpad"></div>
</div>
<br />
<table cellpadding="0" cellspacing="0">
  <tr>
    <th><?php echo $this->Paginator->sort('User', 'User.lgn');?></th>
    <th><?php echo $this->Paginator->sort('Name', 'Tournament.name');?></th>
    <th><?php echo $this->Paginator->sort('status');?></th>
    <th class="actions"><?php echo 'Actions';?></th>
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
    <td><?php echo $room['UserCreator']['lgn']; ?> </td>
    <td><?php echo $room[$room['SignupRoom']['model']]['name']; ?> </td>
    <td><?php echo $room['SignupRoom']['status']; ?> </td>
    <td class="actions"><?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View" />', array('action'=>'view', $room['SignupRoom']['id']), array('escape'=>false)); ?> <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_.gif" alt="Delete" />', array('action'=>'delete_room', $room['SignupRoom']['id']), array('escape'=>false), null, sprintf('Are you sure you want to delete # %s?', $room['SignupRoom']['id'])); ?> </td>
  </tr>
  <?php endforeach; ?>
</table>
<div class="paging"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<?php else:?>
No rooms
<?php endif;?>
