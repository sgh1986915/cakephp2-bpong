<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<?php $this->pageTitle = 'Results of The World Series of Beer Pong'; ?>
<?php echo $this->Html->css('eventlist.css'); ?>
<div style='position: relative;margin-bottom:-30px;bottom:-5px; float:right;'><?php echo $this->element('facebook_like');?></div>
<h2 class='hr' style='margin-bottom:0px;'>Results of The World Series of Beer Pong</h2>

<div id="eventsList">
<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

	<table class="eventlist sorter" border="0" cellspacing="0" cellpadding="0">
	  <tr class="paginationEvents">
	    <th style="border-left:1px solid #d91d1e"><?php echo $this->Paginator->sort('Name&nbsp;&&nbsp;Description', 'name', array('sorter' => true, 'escape' => false));?></th>
	    <th><?php echo $this->Paginator->sort('End&nbsp;Date', 'EventView.end_date', array('sorter' => true, 'escape' => false));?></th>
	    <th><?php echo $this->Paginator->sort('Venue', 'Venue.name', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('City', 'Venue.city', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('State', 'Venue.shortname', array('sorter' => true));?></th>
	    <th>Stats</th>
	    <th><?php echo $this->Paginator->sort('Rank', 'votes', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('Comments', 'comments', array('sorter' => true));?></th>
	    <th style="border-right:1px solid #d91d1e">Created&nbsp;By</th>
	  </tr>
	<?php
		$i = 0;
		foreach($events as $index => $event):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
		?>
	  <tr <?php echo $class;?>>
	    <td class="event-name"><span class="cell-content"><?php echo $this->Html->link($event['EventView']['name'], '/event/' . $event['EventView']['id'] . '/' . $event['EventView']['slug']); ?></span></td>
	    <td class="created"><span class="cell-content"><?php echo $this->Time->niceForEvent($event['EventView']['end_date']); ?></span></td>
	    <td class="venue-name"><span class="cell-content"><?php echo $this->Html->link($event['Venue']['name'], array('controller'=>'venues','action'=>'view', $event['Venue']['slug']), array(), false, false); ?></span></td>
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
	    <td style='text-align:center;'>
	    <?php echo $this->Html->link('<img src="' . STATIC_BPONG . '/img/stats_link_events.gif" />', '/event/' . $event['EventView']['id'] . '/' . $event['EventView']['slug'], array('escape' => false)); ?>
	  
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
	    <td class="comments"><span class="cell-content"><?php echo$event['EventView']['comments']; ?></span></td>
	    <td class="user">
		<span class="cell-content">
		    <?php if (!empty($event['Creator'])):?>
				<?php echo $this->Image->avatar($event['Creator']['avatar']);?><br/>
				<span class="avatar-title">
				<?php echo $this->Html->link($event['Creator']['lgn'], '/u/' . $event['Creator']['lgn']); ?>
				</span>
		    <?php else:?>
				<img src="/img/avatars/default_40.gif" />
				<span class="avatar-title">unknown</span>
		    <?php endif;?>
		</span>
	    </td>
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>
</div>
	<?php echo $this->element('simple_paging');?>
