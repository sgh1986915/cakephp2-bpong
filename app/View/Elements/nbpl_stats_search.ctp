<?php 
$action = $this->request->action;
$props = array(
	'players_stats' => array('title' => 'Players', 'search_text' => 'Find a player'),
	'teams_stats' => array('title' => 'Teams', 'search_text' => 'Find a team'),
	'schools_stats' => array('title' => 'School Affils', 'search_text' => 'Find a school'),
	'greeks_stats' => array('title' => 'Greek Affils', 'search_text' => 'Find a greek'),
	'organizations_stats' => array('title' => 'Organizations', 'search_text' => 'Find an organization'),
	'cities_stats' => array('title' => 'Cities', 'search_text' => 'Find a city'),
	'states_stats' => array('title' => 'States', 'search_text' => 'Find a state'),
	'countries_stats' => array('title' => 'Countries', 'search_text' => 'Find a country')
);
if (empty($this->request->data['Search']['q'])) {
	$this->request->data['Search']['q'] = $props[$action]['search_text'];	
}
?>
<script type="text/javascript">
	$(document).ready(function(){
        $('#stats_search_text').focus(function(){if ($('#stats_search_text').val()=="<?php echo $props[$action]['search_text'];?>")$('#stats_search_text').val('');});
		<?php /*?>
		$('#stats_search_text').blur(function(){if ($('#stats_search_text').val()=="")$('#stats_search_text').val('<?php echo $props[$action]['search_text'];?>');});
		<?php */ ?>
    });
	function chech_search () {
		if ($('#stats_search_text').val() == "<?php echo $props[$action]['search_text'];?>") {
			return false;			
		} else {
			return true;
		}
	}
</script>	

<div class="mainsearch">
	<h2><?php echo $props[$action]['title'];?></h2>
	<form name="stats_search" action="/<?php echo $action;?>/s/" method="post" onsubmit = "return chech_search();">
		<?php echo $this->Form->input('Search.q',array('label'=>false, 'div' => false, 'id' => 'stats_search_text', 'class' => 'text', 'value' => $this->request->data['Search']['q']));?>
		<input type="submit" class="submit" value="Search" name="sub" />
	</form>
	<div class="clear"></div>
</div>