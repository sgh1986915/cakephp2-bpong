<?php if(!empty( $this->request->data['Package'])): ?>
    	<?php 
    		echo $this->Form->hidden('Package.deposit_id');
    		echo $this->Form->hidden('Package.id');
    		echo $this->Form->hidden('Package.price');
    		echo $this->Form->hidden('Package.deposit');
    	?>

Description:<?php echo( $this->request->data['Package']['description']); ?><br>
Price: <?php echo( $this->request->data['Package']['price']); ?><br>
Deposit: <?php echo( $this->request->data['Package']['deposit']); ?><br>
<?php endif; ?>