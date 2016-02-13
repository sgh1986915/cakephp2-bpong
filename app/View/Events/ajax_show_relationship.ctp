<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
<?php if (empty($subEvents)):
?>
      <div style='padding-left:15px;font-size:14px;'>There are no relationships</div>
      <?php else:
      	$relationshipTypes = Configure::read('Event.Relationship.Types');
      ?>
	<?php foreach ($subEvents as $subEvent):?>
		<?php if ($subEvent['Parent']['id'] == $eventID):?>
			<span class='purple b'><?php echo $relationshipTypes[$subEvent['EventsEvent']['relationship_type']];?></span> :: 
			<?php echo $this->Html->link($subEvent['Event']['name'], '/event/' . $subEvent['Event']['id'] . '/' . $subEvent['Event']['slug']); ?>
			
			<?php else:?>
			<span class='purple b'>Parent Event</span> ::
			<?php echo $this->Html->link($subEvent['Parent']['name'], '/event/' . $subEvent['Parent']['id'] . '/' . $subEvent['Parent']['slug']); ?> 		
		<?php endif;?>
		<br/>
	<?php endforeach;?>
<?php endif;?>

	<div class="paginationRelationship">
		<?php echo $this->element('simple_paging');?>
	</div>
