<h2>Edit News</h2>
<?php echo $this->element('mce_init', array('name' => 'data[OrganizationNews][body]')); ?>

<?php echo $this->Form->create('OrganizationNews', array('enctype'=>"multipart/form-data", 'url' => array('action' => 'edit', $id), 'id' => 'OrganizationNews'));?>
<?php echo $this->Form->input('OrganizationNews.title', array('style' => 'width:400px;', 'label' => 'News Title')); ?>
<?php if (!empty($news['Image']['id'])):?>
   <div style='margin-left:150px;padding:10px;'>
		<?php echo $this->Html->image(IMG_MODELS_URL . '/middle_' . $news['Image']['filename'], array( 'border' => '0' )); ?>
   </div>
<?php endif;?>
<?php if (!empty($news['Image']['id'])):?>
   	<?php echo $this->Form->input( 'Image.' . $news['Image']['id'], array('type' 	=> 'file', 'class'	=> 'file', 'label'	=> 'Image') );?>
	<?php echo $this->Form->hidden('Image.' . $news['Image']['id'] . '.prop', array('value' => 'Personal'));?>
<?php else:?>
	<?php echo $this->Form->input( 'Image.new', array('type' => 'file', 'class' => 'file', 'label' => 'Image'));?>
	<?php echo $this->Form->hidden('Image.new.prop', array('value' => 'Personal'));?>
<?php endif;?>

<div class="textarea"><?php echo $this->Form->input('OrganizationNews.body', array('rows' => 20, 'div' => false, 'label' => 'News Body'));?></div>
<br/>
<div class="submit"><?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?></div>
