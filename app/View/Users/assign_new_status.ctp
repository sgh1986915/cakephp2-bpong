			<tr>
				<td class="actions"><?php echo $status['Group']['name'] ?></td>
				<td class="actions"><?php echo $status['Status']['name'] ?></td>
				<td class="actions">
				<?php  echo $this->Html->link('Remove from the group', array('controller'=> 'users','action'=>'deleteFromGroup', $userID,$status['Status']['id']), null, sprintf('Are you sure you want to remove user from the group %s?',$status['Group']['name']));?>
				</td>
			</tr>