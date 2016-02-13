<h2>Add News</h2>
<?php echo $this->element('mce_init', array('name' => 'data[OrganizationNews][body]')); ?>

<?php echo $this->Form->create('OrganizationNews', array('enctype'=>"multipart/form-data", 'url' => array('action' => 'add', $orgID), 'id' => 'OrganizationNews'));?>
<?php echo $this->Form->input('OrganizationNews.title', array('style' => 'width:400px;', 'label' => 'News Title')); ?>
<?php echo $this->Form->input( 'Image.new', array('type' => 'file', 'class' => 'file', 'label' => 'Image'));?>
<?php echo $this->Form->hidden('Image.new.prop', array('value' => 'All'));?>
<div class="textarea"><?php echo $this->Form->input('OrganizationNews.body', array('rows' => 20, 'div' => false, 'label' => 'News Body'));?></div>
<br/>
<div class="submit"><?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?></div>
