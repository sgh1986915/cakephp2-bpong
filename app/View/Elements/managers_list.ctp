<h3>Managers list</h3>
<?php if (!empty($this->request->data['User'])):?>
    <table cellpadding="0" cellspacing="0">
    <tr>
    <th nowrap="nowrap">Email</th>
    <th nowrap="nowrap">Login</th>
    <th nowrap="nowrap">First Name</th>
    <th nowrap="nowrap">Last Name</th>
    <th nowrap="nowrap">Confirmed</th>
    <th nowrap="nowrap">Actions</th>  			
    </tr>
    <?php foreach ($this->request->data['User'] as $manager): ?>
	    <tr>
	    <td><?php echo $manager['email'] ?></td>
	    <td><?php echo $manager['lgn'] ?></td>
	    <td><?php echo $manager['firstname'] ?></td>
	    <td><?php echo $manager['lastname'] ?></td>
	    <td><?php echo empty($manager['Manager']['is_confirmed'])?"No":"Yes"?></td>
	    <td><a onclick="return removeManager(<?php echo $manager['id']; ?>)" href="#">Remove</a></td>
	    </tr>
    <?php endforeach; ?>
    </table>
<?php else:?>
No managers
<?php endif;?>
    
