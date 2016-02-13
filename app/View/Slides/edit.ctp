<?php echo $this->element('mce_init', array('name' => 'SlideDescription')); ?>
<style>
div.submit, form div.submit  {
    padding-left: 0;
}
</style>

<?php echo $this->Form->create('Slide', array('url' => array('action' => 'edit', $this->request->data['Slide']['id']), 'enctype'=>"multipart/form-data"));?>
<div class='clear'></div>
<?php if (!empty($this->request->data['Image']['0']['id'])):?>
   	<?php echo $this->Form->input( 'Image.' . $this->request->data['Image']['0']['id'], array('type' 	=> 'file', 'class'	=> 'file', 'label'	=> 'Image (960x410)'));?>
	<?php echo $this->Form->hidden('Image.' . $this->request->data['Image']['0']['id'] . '.prop', array('value' => 'Personal'));?>
<?php else:?>
	<?php echo $this->Form->input( 'Image.new', array('type' => 'file', 'class' => 'file', 'label' => 'Image (960x410)'));?>
	<?php echo $this->Form->hidden('Image.new.prop', array('value' => 'Personal'));?>
<?php endif;?>

<?php
    echo $this->Form->input('url', array('style' => 'display: block'));
    echo $this->Form->input('title', array('style' => 'display: block'));
    echo $this->Form->input('description', array('label' => false, 'div' => false));
    echo $this->Form->hidden('id');
	echo $this->Form->end('Submit', true);
?>
