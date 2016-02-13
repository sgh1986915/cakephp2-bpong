<h2>Access</h2>
<ul class="accesslist">
<?php foreach ($categories as $category):?>
<li>
	<div class='txt'>
		<?php echo $this->Html->link($category['access_categories']['name'], Router::url(array('controller'=>'accessions','action'=>'show',$category['access_categories']['id']))); ?>
		<div class="row grey"><strong><?php echo $category[0]['cnt']; ?></strong> objects</div>
	</div>

		<?php echo $this->Html->link('<img class="ed" src="/img/edit.gif" alt="Edit" title="Edit" />', Router::url(array('controller'=>'accessCategories','action'=>'edit',$category['access_categories']['id'])), array('escape' => false), null, false); ?>
		<?php if ($category[0]['cnt']==0):?>
		<?php echo $this->Html->link('<img src="/img/delete.gif" alt="Delete" title="Delete" />', Router::url(array('controller'=>'accessCategories','action'=>'delete',$category['access_categories']['id'])), array('escape' => false), null, false); ?>
		<?php endif;?>

</li>
<?php endforeach; ?>
</ul>
<div class="clear"></div>
<div class="hr"></div>
	<?php echo $this->Html->link("New category", Router::url(array('controller'=>'accessCategories','action'=>'add',)), array('class'=>'button bt2')); ?>
