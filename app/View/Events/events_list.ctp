<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php if (empty($events)):?>
	<div style='font-size:120%;text-align:center;width:100%;padding:20px;'>There are no Events</div>
<?php else:?>
	<table class="sub_list sorter" border="0" cellspacing="0" cellpadding="0">
	  <tr class="paginationEvents">
	    <th><?php echo $this->Paginator->sort('Name&nbsp;&&nbsp;Description', 'name', array('sorter' => true, 'escape' => false));?></th>
	    <th><?php echo $this->Paginator->sort('Start&nbsp;Date', 'EventView.start_date', array('sorter' => true, 'escape' => false));?></th>
	    <th><?php echo $this->Paginator->sort('Venue', 'Venue.name', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('City', 'Venue.city', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('State', 'Venue.shortname', array('sorter' => true));?></th>
	    <th>Share</th>
	    <th width='80px'><?php echo $this->Paginator->sort('Rank', 'votes', array('sorter' => true));?></th>
	    <?php /*?>
	    <th><?php echo $this->Paginator->sort('Comments', 'comments', array('sorter' => true));?></th>
	    <?php */ ?>
	    <th>Created&nbsp;By</th>
	  </tr>
	<?php
		$i = 0;
		foreach($events as $index => $event):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="alt"';
			}
		?>
	  <tr <?php echo $class;?>>
	    <td class="event-name"><span class="cell-content"><?php echo $this->Html->link($event['EventView']['name'], '/event/' . $event['EventView']['id'] . '/' . $event['EventView']['slug'],array('escape'=>false)); ?></span></td>
	    <td class="created"><span class="cell-content"><?php echo $this->Time->niceForEvent($event['EventView']['start_date']); ?></span></td>
	    <td class="venue-name"><span class="cell-content"><?php echo $this->Html->link($event['Venue']['name'], array('controller'=>'venues','action'=>'view', $event['Venue']['slug']), array('escape'=>false), false, false); ?></span></td>
	    <td class="city"><span class="cell-content"><?php
	    	if (!empty($event['Venue']['city'])) {
				echo $event['Venue']['city'];
			} else {
				echo '-';
			}
	    ?></span></td>
	    <td class="state"><span class="cell-content"><?php
		if (!empty($event['Venue']['shortname'])) {
		    echo $event['Venue']['shortname'];
		} else {
		    echo '-';
		}
	    ?></span></td>
	    <td class="share">
		<span class="cell-content">
			<div style="width: 83px; height: 16px; text-align: left;"><iframe allowtransparency="true" class="sharethis-frame" style="background-color: transparent; width: 83px; height: 16px; border: none; position: absolute; z-index: <?php echo 1000000 - 100 * $i ?>" scrolling="no" frameborder="no" src="<?php echo $this->Html->url(array('controller' => 'events', 'action' => 'sharethis_frame', $event['EventView']['name'], $event['EventView']['slug'])) ?>"></iframe></div>
		</span>
	    </td>
		<?php if (!empty($event['Creator'])) {
			$ownerId = $event['Creator']['id'];
		} else {
			$ownerId = null;
		} ?>
	    <td class="rank">
		<span class="cell-content">
	    	<?php echo $this->element("votes/vote",array('model' => "Event",
				"modelId"  =>$event['EventView']['id'],
				'votesPlus'=> $event['EventView']['votes_plus'],
				'votesMinus'=> $event['EventView']['votes_minus'],
				'ownerId'   => $ownerId,
				'votes'     => $votes,
				'canVote'   => $canVote ));?>
		</span>
	    </td>
	    <?php /*?>
	    <td class="comments"><span class="cell-content"><?php echo$event['EventView']['comments']; ?></span></td>
	    <?php */ ?>
	    <td class="user">
		<span class="cell-content">
		    <?php if (!empty($event['Creator'])):?>
				<?php echo $this->Image->avatar($event['Creator']['avatar']);?><br/>
				<span class="avatar-title">
				<?php echo $this->Html->link($event['Creator']['lgn'], '/u/' . $event['Creator']['lgn']); ?>
				</span>
		    <?php else:?>
				<img src="<?php echo STATIC_BPONG?>/img/avatars/default_40.gif" />
				<span class="avatar-title">unknown</span>
		    <?php endif;?>
		</span>
	    </td>
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>
<?php endif;?>

<div class="paging paginationEvents">
	<?php echo $this->Paginator->prev('<<', array(), null, array('class' => 'disabled')); ?>
	<?php echo $this->Paginator->first(3, array('separator' => ' '), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers(array('separator' => ' ')); ?>
	<?php echo $this->Paginator->last(3, array('separator' => ' '), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->next('>>', array(), null, array('class' => 'disabled')); ?>
</div>
<script>
	$(".sharethis-frame").hover(function () {
		$(this).css('width', '355px');
		$(this).css('height', '235px');
	},
	function () {
		$(this).css('width', '83px');
		$(this).css('height', '16px');
	});
</script>