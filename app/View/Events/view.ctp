<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<script type="text/javascript">
	$(document).ready(function() {
		customPreLoadPiece("/events/ajax_show_relationship/<?php echo $event['Event']['id'];?>","#event_relationship", 'paginationRelationship', 'event_relationship_loader');
		customPreLoadPiece("/events/ajax_show_teams/<?php echo $event['Event']['id'];?>","#event_teams", 'paginationTeams', 'event_teams_loader');
		customPreLoadPiece("/albums/image_albums_list/0/Event/<?php echo $event['Event']['id'];?>","#imageAlbumsList", 'paginationImageAlbums', 'imageAlbumsLoader');
		customPreLoadPiece("/albums/video_albums_list/0/Event/<?php echo $event['Event']['id'];?>","#videoAlbumsList", 'paginationVideoAlbums', 'videoAlbumsLoader');

		<?php if ($gamesCount):?>
			customPreLoadPiece("/games/ajaxShowEventGames/<?php echo $event['Event']['id'];?>","#event_games", 'paginationGames', 'event_games_loader');
		<?php endif; ?>
		<?php if (/*$isFinishedEvent && */$gamesCount):?>
			customPreLoadPiece("/teams_objects/ajaxFinalStandings/<?php echo $event['Event']['id'];?>","#event_standings", 'paginationStandings', 'final_standings_loader');
		<?php endif;?>

	});
</script>
<?php echo $this->Html->css('jquery.tabs'); ?>
<?php echo $this->Html->script(array('jquery.tabs.min.js')); ?>



<?php echo $this->Html->css('eventview.css'); ?>


<?php
	if (!empty($event['Venue']['Address']) && (!$event['Venue']['Address']['latitude'] || !$event['Venue']['Address']['longitude'])) {
		$latLon = $this->Address->getLatLon($event['Venue']['Address'], 1);
		$event['Venue']['Address']['latitude'] = $latLon['lat'];
		$event['Venue']['Address']['longitude'] = $latLon['lon'];
	}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
$(document).ready(function() {

	$("#red-tabs").tabs();
	<?php if (!empty($event['Venue']['Address']['latitude']) && !empty($event['Venue']['Address']['longitude'])) :?>
	var latitude = <?php echo $event['Venue']['Address']['latitude']; ?>;
	var longitude = <?php echo $event['Venue']['Address']['longitude']; ?>;

	var latlng = new google.maps.LatLng(latitude, longitude);
	var myOptions = {
		zoom: 4,
		center: latlng,
		mapTypeControl: false,
		navigationControl: true,
		navigationControlOptions: {
			style: google.maps.NavigationControlStyle.SMALL
		},
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById("venue_map"), myOptions);

	var image = new google.maps.MarkerImage("<?php echo IMG_MODELS_URL;?>/bpong-marker.png",
		// This marker is 20 pixels wide by 32 pixels tall.
		new google.maps.Size(33, 35),
		// The origin for this image is 0,0.
		new google.maps.Point(0,0),
		// The anchor for this image is the base of the flagpole at 0,32.
		new google.maps.Point(15, 13)
	);

	// Set up our GMarkerOptions object
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		icon: image
	});
	<?php endif;?>
});
</script>
<div style='position: relative;margin-bottom:-30px;bottom:-3px; float:right;'><?php echo $this->element('facebook_like');?></div>
<div class="event-header">
    <h2>Event view</h2>&nbsp;
    <span class="actions">
	<?php if($canAddEvent) : ?>
	    <?php echo $this->Html->link('Add', array('controller' => 'Events', 'action' => 'add')); ?>
	<?php endif; ?>
	<?php if($canEditEvent) : ?>
	    | <?php echo $this->Html->link('Edit', array('controller' => 'Events', 'action' => 'edit', $event['Event']['id'])); ?>
	    | <?php echo $this->Html->link('Signup Stats',array('controller'=>'Statistics','action'=>'signupsStatistics','Event',$event['Event']['id'])); ?>
    <?php endif; ?>
    </span>
</div>
<?php echo $this->Html->link('<< Back to event list', array('controller' => 'events', 'action' => 'index'), array('class' => 'backlink'));?>
<div class="event-view">
    <div class="left">
    <div class="event-image" style='text-align:center;clear:both;'>
	<?php if(!empty($images)): ?>
		<?php foreach($images as $img): ?>
		<img src="<?php echo IMG_MODELS_URL;?>/middle_<?php echo $img['Image']['filename'] ?>" border="0">
		<?php endforeach; ?>
	<?php else:?>
		<img src="<?php echo STATIC_BPONG?>/img/event_default.jpg" border="0">
	<?php endif; ?>
	<?php if (!empty($organizations)):?>
	<div class="red_header_little" style="width:100%;margin-bottom:10px;margin-top:15px;">
		<div class="red_header_name_little">Organization</div>
	</div>
		<?php foreach ($organizations as $organization):?>
		<?php if (!empty($organization['Organization']['Image']['filename'])):?>
			<a href="/o/<?php echo $organization['Organization']['slug'];?>"><img src="<?php echo IMG_MODELS_URL;?>/thumbs_<?php echo $organization['Organization']['Image']['filename'];?>" alt="<?php echo $organization['Organization']['name'];?>" border="0" /></a><br/>
		<?php endif;?>
			<strong><a href="/o/<?php echo $organization['Organization']['slug'];?>"><?php echo $organization['Organization']['name'];?></a> &rsaquo;&rsaquo;</strong><br/>
		<?php endforeach;?>
		<hr style='height:1px;'/>
	<?php endif;?>
	</div>
	<h3 class="subtitle">Tags for this tournament</h3>
	<span class="tags">
	<?php if(!empty ($event['Tag'])) : ?>
	    <?php echo $this->element('/tags/show_authors', array('authorID' => false, 'tags' => $event['Tag'], 'url' => false));?></span>
	<?php else: ?>
	    <?php if (!empty($event['Event']['Owner'])) : ?>
		<?php echo $event['Event']['Owner']['User']['lgn'].' has not entered tags for this event'; ?>
	    <?php else : ?>
		There are no tags for this event
	    <?php endif; ?>
	<?php endif; ?>
    </div>
    <div class="center" style='text-align:left !important;'>
	<h2><?php echo $event['Event']['name']; ?></h2>
	<?php if (!empty($event['Event']['signup_required'])): ?>
	    <p class="event-element">
		<span class="element-header">Signup Required:</span>
		<span>Yes</span>
	    </p>
	    <?php if (!empty($event['Event']['finish_signup_date'])): ?>
		<p class="event-element">
		    <span class="element-header">Finish signup:</span>
		    <span><?php echo $this->Time->niceForEvent($event['Event']['finish_signup_date'], $event['Timezone']['value']); ?></span>
		</p>
	    <?php endif; ?>
	<?php endif; ?>
        <div class="widgets">

		<?php echo $this->element("votes/vote_plus", array(
			'model' => "Event",
			"modelId"  => $event['Event']['id'],
			'votesPlus'=> $event['Event']['votes_plus'],
			'votesMinus'=> $event['Event']['votes_minus'],
			'ownerId' => $event['Event']['user_id'],
			'votes' => $votes,
			'canVote' =>$canVoteBlogpost
		));?>
		<?php echo $this->element("votes/vote_minus", array(
			'model' => "Event",
			"modelId"  => $event['Event']['id'],
			'votesPlus'=> $event['Event']['votes_plus'],
			'votesMinus'=> $event['Event']['votes_minus'],
			'ownerId' => $event['Event']['user_id'],
			'votes' => $votes,
			'canVote' =>$canVoteBlogpost
		));?>
	    <?php echo $this->element('share_this'); ?>
	</div>
	<div class="event-text">
	    <p class="event-element">
		<span class="element-header">Start date:</span>
		<span><?php echo $this->Time->niceShort($event['Event']['start_date']); ?>
		<?php if (!empty($event['Timezone']['name'])):?> (<?php echo $event['Timezone']['name'];?>) <?php endif;?>
		</span>
	    </p>
	    <?php if (strstr($event['Event']['end_date'],'0000-00-00') === false): ?>
		<p class="event-element">
		    <span class="element-header">End date:</span>
		    <span><?php echo $this->Time->niceShort($event['Event']['end_date']); ?>
		    <?php if (!empty($event['Timezone']['name'])):?> (<?php echo $event['Timezone']['name'];?>) <?php endif;?>
		    </span>
		</p>
	    <?php endif; ?>
        <?php if (!empty($event['Event']['signup_required'])): ?>
            <p class="event-element">
                <a href="<?php echo SECURE_SERVER.'/signups/Event/'.$event['Event']['slug']; ?>">
                    <img src="<?php echo IMG_NBPL_LAYOUTS_URL; ?>/signup.png" />
                </a>
            </p>
        <?php endif; ?>
	    <?php if (!empty($event['Event']['cost'])):?>
		    <p class="event-element">
				<span class="element-header">Entry fee:</span>
				<span><?php echo $event['Event']['cost']; ?></span>
		    </p>
	    <?php endif; ?>
	    <?php if (!empty($event['Event']['description'])):?>
		    <p class="event-element">
				<span class="element-header">Description:</span>
				<span> <?php echo $event['Event']['description']; ?> </span>
		    </p>
	    <?php endif; ?>
	    <?php if (!empty($event['Event']['prize'])):?>
		    <p class="event-element">
				<span class="element-header">Prizes:</span>
				<span> <?php echo $event['Event']['prize']; ?> </span>
		    </p>
	    <?php endif;?>
	    <?php if (!empty($event['Event']['other'])):?>
		    <p class="event-element">
				<span class="element-header">Additional Information:</span>
				<span> <?php echo $event['Event']['other']; ?> </span>
		    </p>
	    <?php endif;?>

	</div>
    </div>
    <div class="right">
	<div class="user-info">
	    <?php if (!empty($event['Creator'])):?>
		<?php if (!empty($event['Creator']['avatar'])) : ?>
		    <div class="avatar-wrapper"><?php echo $this->Image->avatar($event['Creator']['avatar']);?></div>
		<?php else: ?>
		    <div class="avatar-wrapper"><?php echo $this->Html->image("avatars/default_40.gif");?></div>
		<?php endif; ?>
		<span class="avatar-title">
		    <?php echo $this->Html->link($event['Creator']['lgn'], array('controller' => 'users', 'action'=>'view',$event['Creator']['lgn'])); ?>
		</span>
	    <?php else: ?>
		<div class="avatar-wrapper"><?php echo $this->Html->image("avatars/default_40.gif");?></div>
		<span class="posted-by">Posted by</span><br/>
		<span class="avatar-title">unknown</span>
	    <?php endif;?>
	    <br/>
	    <span class="created">Date: <span class="created-date"><?php echo $this->Time->format('m/d/y', $event['Event']['created']);?></span></span>
	</div>
	<div class="venue">
	    <?php if (!empty($event['Venue']['Address'])) : ?>
		<h3 class="subtitle">Tournament venue</h3>
		<div id="venue_map" class="venue_map"></div>
		<span class="venue-title"><?php echo $this->Html->link($event['Venue']['name'], array('controller' => 'Venues', 'action' => 'view', $event['Venue']['slug']), array(), false, false);?></span><br/>

		<p class="address">
		    <span><?php echo $event['Venue']['Address']['address'];?></span><br/>
		    <span><?php echo $event['Venue']['Address']['city'];?></span>,&nbsp;
			<?php if(!empty($event['Venue']['Address']['Provincestate'])) : ?>
				<span><?php echo $event['Venue']['Address']['Provincestate']['shortname'];?></span>&nbsp;
			<?php endif; ?>
		    <span><?php echo $event['Venue']['Address']['postalcode'];?></span><br/>
		</p>
	    <?php endif; ?>
	    <p class="phones">
		<span>&ndash;</span><br/>
		<?php
		    if(!empty($event['Venue']['Phone'])) :
			foreach($event['Venue']['Phone'] AS $phone) :
		?>
			    <span><?php echo $phone['phone']; ?></span><br/>
		<?php
			endforeach;
		    endif;
		?>
	    </p>
	    <?php if ($event['Venue']['web_address'] && $event['Venue']['web_address'] !='http://'):?>
	    <span class="web-address"><?php echo $this->Html->link($event['Venue']['web_address'], $this->Html->addHttp($event['Venue']['web_address'])); ?></span>
	    <?php endif;?>
	</div>
    </div>
</div>
<?php if($canEditEvent) : ?>
	<h2 class='hr'>Relationship</h2>
	<div id='event_relationship'>
		<?php echo $this->requestAction('/events/ajax_show_relationship/' . $event['Event']['id']); ?>
	</div>
	<div class='event_relationship_loader' style='height:10px;' class='clear'></div>
	<div class='clear'></div>
<?php endif;?>

<div id="red-tabs">
      <ul>
        	<li><a href="#tab-results"><span>Results</span></a></li>
        	<li><a href="#tab-teams"><span>Teams</span></a></li>
        	<li><a href="#tab-photos"><span>Photos & Video</span></a></li>
        	<li><a href="#tab-comments"><span>Comments (<?php echo intval($event['Event']['comments']);?>)</span></a></li>
      </ul>
      <div id="tab-results">
      	<?php if ($totalGamesCount): ?>
	      	<div style='width:100%;text-align:left;'>
	      		<a href="/viewbrackets/<?php echo $event['Event']['id'];?>"><strong style='font-size:14px;'>
	                <img src="<?php echo STATIC_BPONG; ?>/img/bracket-generic.jpg" alt="View Brackets" />
	            </strong></a>
	      	</div>
	      	<br/>
      	<?php endif;?>
		<?php if ($gamesCount):?>
			<div style='float:left; width:48%;'>
				<h2 class='hr'><?php if ($isFinishedEvent) echo "Final "; else echo "Current ";?> Standings <div style='float:right;position:relative;'><a href="/teams_objects/finalStandings/<?php echo $event['Event']['id'];?>">View all results</a></div></h2>
				<div id='event_standings'>
					<?php echo $this->requestAction('/teams_objects/ajaxFinalStandings/' . $event['Event']['id']); ?>
				</div>
				<div class='final_standings_loader' style='height:10px;' class='clear'></div>
			</div>
		<?php endif;?>
		<?php if ($gamesCount):?>
			<div style='float:right; width:49%;margin-left:20px;'>
				<h2 class='hr'>Games
				<div style='float:right;position:relative;'><a href="/games/showEventGames/<?php echo $event['Event']['id'];?>">View all games</a></div>
				<div style='font-size:13px;float:right;color:#2BA500;font-weight:bold;padding-right:30px;bottom:-2px;position:relative;'>* Winning teams in green</div>
				</h2>
				<div id='event_games'>
					<?php echo $this->requestAction('/games/ajaxShowEventGames/' . $event['Event']['id']); ?>
				</div>
				<div class='event_games_loader' style='height:10px;' class='clear'></div>
			</div>
		<?php endif;?>
      </div>
      <div id="tab-teams">
		<div id='event_teams'>
			<?php echo $this->requestAction('/events/ajax_show_teams/' . $event['Event']['id']); ?>
		</div>
		<div class='event_teams_loader' style='height:10px;' class='clear'></div>
		<div class='clear'></div>
      </div>
      <div id="tab-photos">
			<h2>Event Photo Albums</h2>
			<div id="imageAlbumsList"><?php echo $this->requestAction('/albums/image_albums_list/Event/' . $event['Event']['id']); ?></div>
			<div class='imageAlbumsLoader' style='height:10px;' class='clear'></div>

			<div class='clear'></div>

			<h2>Event Video Albums</h2>
			<div id="videoAlbumsList"><?php echo $this->requestAction('/albums/video_albums_list/Event/' . $event['Event']['id']);  ?></div>
			<div class='videoAlbumsLoader' style='height:10px;' class='clear'></div>
      </div>
      <div id="tab-comments">
			<?php echo $this->element("comments/add",array('model'=>"Event","modelId"=> $event['Event']['id'], 'comments'=> $comments, 'commentVotes' => $commentVotes));?>
      </div>

</div>
