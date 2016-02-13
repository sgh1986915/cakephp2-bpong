<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php foreach ($news as $new):?>
	<h2><?php echo $new['OrganizationNews']['title'];?></h2>

<?php if (!empty($new['Image']['filename'])):?>
	<img style='margin-right:10px; float:left;' src="<?php echo IMG_MODELS_URL;?>/little_<?php echo $new['Image']['filename'];?>" alt="" border="0" />
<?php endif;?>

	<?php echo $new['OrganizationNews']['body'];?>
	<div style='width:100%;clear:both; height:20px;'> </div>
    <?php if($canEdit) echo $this->Html->link('<img alt="edit" src="/img/smalledit.gif" />', Router::url(array('action'=>'edit', $new['OrganizationNews']['id'])), array('escape' => false), null, false); ?>&nbsp;
    <?php if($canDelete) echo $this->Html->link('<img alt="edit" src="/img/smalldelete.gif" />', Router::url(array('action'=>'delete', $new['OrganizationNews']['id'])), array('escape' => false), "Are you sure?", false); ?>
	<div class='clear'><br/></div>
<?php endforeach;?>

<br/><br/>
<?php if($canAdd): ?>
	<span class="addbtn"><?php echo $this->Html->link('Add News', Router::url(array('action'=>'add', $organization['Organization']['id'])), array('class'=>'addbtn')); ?></span>
<?php endif;?>
<?php echo $this->element('simple_paging');?>