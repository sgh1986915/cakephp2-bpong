<?php 
	$typesConfig = array(
		'all' => array('title' => 'All Beer Pong Events', 'link' => '/events/index', 'link_name' => 'All Events'),
		'tournament' => array('title' => 'All Beer Pong Tournaments', 'link' => '/events/index/tournament', 'link_name' => 'Tournaments'),
		'satellite' => array('title' => 'All Beer Pong Satellite Tournaments', 'link' => '/wsobp/world-series-of-beer-pong-satellite-tournaments', 'link_name' => 'Satellite Tournaments'),
		//'sub_event' => array('title' => 'All Beer Pong Sub-Events', 'link' => '/events/index/sub_event', 'link_name' => 'Sub-Events'),
		'tour_stop' => array('title' => 'All Beer Pong Tour-Stops', 'link' => '/events/index/tour_stop', 'link_name' => 'Tour-Stops')	
	
	);
?>
<?php $this->pageTitle = $typesConfig[$eventsType]['title'];?>

<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<?php echo $this->Html->script('fullcalendar/fullcalendar.min.js'); ?>
<?php echo $this->Html->script('jquery.ui.min.js'); ?>
<?php echo $this->Html->css('fullcalendar/calendar.css'); ?>
<?php echo $this->Html->css('fullcalendar/fullcalendar.css'); ?>
<?php echo $this->Html->css('ui.datepicker2.css'); ?>
<?php echo $this->Html->css('eventlist.css'); ?>

<script type='text/javascript'>
$(document).ready(function() {
	customPreLoadPiece("/events/events_list/<?php echo $eventsType;?>","#eventsList", 'paginationEvents', 'eventsLoader');
	
	$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#tabs" ).tabs();

	$( "#tabs" ).bind( "tabsshow", function(event, ui) {
	    $('#calendar').fullCalendar('render');
	});

	$.ajaxSetup({ cache: false });
	$("#EventViewName").autocomplete("<?php echo $this->Html->url(array('controller' => 'Events', 'action' => 'autocomplete', 'name')) ?>", {
	    width: 320,
	    max: 8,
	    highlight: false,
	    multiple: false,
	    scroll: true,
	    scrollHeight: 300
	});

	$.ajaxSetup({ cache: false });
	$("#EventViewLgn").autocomplete("<?php echo $this->Html->url(array('controller' => 'Events', 'action' => 'autocomplete', 'lgn')) ?>", {
	    width: 320,
	    max: 8,
	    highlight: false,
	    multiple: false,
	    scroll: true,
	    scrollHeight: 300
	});
	initializeCalendar();
});

    function initializeCalendar() {
		$('#calendar').fullCalendar({
			header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,basicWeek,basicDay'
			},
			editable: false,
			events: "<?php echo $this->Html->url(array('controller' => 'events', 'action' => 'json_events', $eventsType)); ?>",
			loading: function(bool) {
			if (bool) $('#loader').show();
			else $('#loader').hide();
			}
		});
    }

    function changeEventType (newType) {
		window.location = newType;
    }
    function changePastEvent(link) {
        if ($('#past_events').attr('checked')) {
        	link = link + '/past_events:1';							
        }        
		window.location = link;
    }

</script>
<?php echo $this->element("event/marker_map", array('markers' => $markers, 'zoom' => 4));?> 

<!-- <div class="bannerbox"> <img src="/img/banner.jpg" /> </div> -->
<?php $paginate_params = array('escape' => false); ?>
<div style="height: 0; line-height: 0; font-size: 0;" class="clear">&nbsp;</div>

<div class="calendartournaments">
<?php echo $this->Form->create('EventView', array('url' => array('controller' => 'events','action' => 'search'))); ?>
<div style='padding-bottom:10px;'>
	 
	<?php /* foreach ($typesConfig as $typeName => $type):?>
			<div style='margin-right:10px; float:left;'><input name="events_type" onclick='changeEventType("<?php echo $type['link'];?>");' type="radio" value="" <?php if ($typeName == $eventsType):?> checked <?php endif;?>><?php echo $type['link_name'];?></div> 	
	<?php endforeach; */ ?>
	<div style='margin-left:5px;padding: 5px;'>
		<input name="data[EventView][past_events]" id='past_events' type="checkbox" <?php if (!empty($this->passedArgs['past_events'])):?> checked <?php endif;?> value="1" onclick="changePastEvent('<?php echo $typesConfig[$eventsType]['link'];?>');">
        Show past events      
    </div>
</div>  
      <div class="searchcalendar">
	<?php 
	if (!empty($this->passedArgs['lgn'])) {
		$searchLgn = $this->passedArgs['lgn'];	
	} else {
		$searchLgn = '';
	}
	if (!empty($this->passedArgs['date'])) {
		$searchDate = $this->passedArgs['date'];	
	} else {
		$searchDate = '';
	}
	if (!empty($this->passedArgs['name'])) {
		$searchName = $this->passedArgs['name'];	
	} else {
		$searchName = '';
	}	
	if (!empty($this->passedArgs['state_id'])) {
		$searchState = $this->passedArgs['state_id'];	
	} else {
		$searchState = '0';
	}	
	?>
	&nbsp;&nbsp;&nbsp;<span class="form-title">Title:</span>
	<?php echo $this->Form->input('name', array('label' => false, 'div' => false, 'value' => $searchName, 'style' => 'width:120px;')); ?>
	&nbsp;<span class="form-title">Creator:</span>
	<?php echo $this->Form->input('lgn', array('label' => false, 'div' => false, 'value' => $searchLgn, 'style' => 'width:70px;')); ?>
	&nbsp;<span class="form-title">State:</span>
	<?php echo $this->Form->input('state_id', array('label' => false, 'div' => false, 'value' => $searchState, 'options' => array('0' => 'All')+ $states, 'style' => 'width:105px;')); ?>
	<?php echo $this->Form->hidden('url', array('value' => $typesConfig[$eventsType]['link'])); ?>
	&nbsp;<span class="form-title">Date:</span>
	<?php echo $this->Form->input('date', array('id' => 'datepicker', 'label' => false, 'div' => false, 'value' => $searchDate, 'style' => 'width:66px;')); ?>
	<input type="submit" class="submit6" value="Go"/>
      </div>
  <?php echo $this->Form->end() ?>
  
  <div id="tabs">
    <ul class="tabscalend">
      <li><a href="#tab1" class="maplink">&nbsp;</a></li>
      <li><a href="#tab2" class="calendlink ">&nbsp;</a></li>
    </ul>
      <div style="height: 0; line-height: 0; font-size: 0; margin-top: -1px" class="clear"></div>
      <div id="tab1">
	  <div id="map" style="width: 100%; height: 500px"></div>
      </div>
      <div id="tab2">
	<!--calendar-->
	<div id="loader_wrapper">
	    <div id="loader"></div>
	</div>
	<div id="calendar"></div>
	<!--end calendar-->
      </div>
 </div>

</div>
<div id="eventsList">
	<?php
		echo $eventList // /submissions/submits_list/
	?>
</div>
<div class='eventsLoader' style='height:20px;'></div>