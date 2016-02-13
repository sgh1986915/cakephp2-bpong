<?php if (!empty($this->request->data['Image']['0']['id'])):?>
<?php echo $this->Html->image(IMG_MODELS_URL . '/' . $this->request->data['Image']['0']['filename'], array('border' => '0')); ?>
<?php endif;?>