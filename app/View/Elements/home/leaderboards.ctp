<script type="text/javascript">
	$(document).ready(function() {
		customPreLoadPiece("/rankings/ajax_players_stats/","#top_leaders_list", 'paginationRanking', 'top_leaders_loader');
	});
	function clickTopLeader(th, leadersType) {
		$('.tophead').hide();
		$('.topleaders').show();
		$('.topssearch').val('');

		$(th).parent().hide();
		$(th).parent().next().show();
		$('#top_leaders_list').hide();
		$('#top_loader').show();

   	    $('#top_leaders_list').load('/rankings/ajax_'+ leadersType +'_stats/', {}, function(){
   			$('#top_loader').hide();
   			$('#top_leaders_list').show();
   			customPreLoadPiece("/rankings/ajax_" + leadersType +"_stats/","#top_leaders_list", 'paginationRanking', 'top_leaders_loader');
   	    });

		return false;
	}
	function topLeaderSearch(th, leadersType) {
		$('#top_leaders_list').hide();
		$('#top_loader').show();
   	    $('#top_leaders_list').load('/rankings/ajax_'+ leadersType +'_stats/s', {'search': $(th).children(".topssearch").val()}, function(){
   			$('#top_loader').hide();
   			$('#top_leaders_list').show();
   			customPreLoadPiece("/rankings/ajax_" + leadersType +"_stats/","#top_leaders_list", 'paginationRanking', 'top_leaders_loader');
   	    });

		return false;
	}
</script>

				<div class="mainbox">
				<h3 class="left thin_h">Leaderboards from around the world</h3>
				<?php /*?><div class="searchfield board">
					<form>
						<div class='home_label'>Search player names</div>
						<input type="text" name="" value="Enter a player’s name" />
						<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
					</form>
				</div><?php */?>
				<div class="clear"></div>
				<div class="topplayers">
					<!-- EOF tophead -->
					<div class="topleaders" style='display:none;'><a href="/top_players" onclick="return clickTopLeader(this, 'players');">Top Players</a></div>
					<div class="tophead" style='display:block;'>
						<h4>Top Players</h4>
						<form class="searchfield" onsubmit="return topLeaderSearch(this, 'players');">
							<div class='home_label'>Find:</div>
							<input type="text" class='topssearch' name="" value="" />
							<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
							<div class="clear"></div>
						</form>
					</div>

					<div class="topleaders"><a href="/top_teams" onclick="return clickTopLeader(this, 'teams');">Top Teams</a></div>
					<div class="tophead">
						<h4>Top Teams</h4>
						<form class="searchfield" onsubmit="return topLeaderSearch(this, 'teams');">
							<div class='home_label'>Find:</div>
							<input type="text" class='topssearch' name="" value="" />
							<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
							<div class="clear"></div>
						</form>
					</div>

					<div class="topleaders"><a href="/top_school" onclick="return clickTopLeader(this, 'schools');">Top School Affils</a></div>
					<div class="tophead">
						<h4>Top School Affils</h4>
						<form class="searchfield" onsubmit="return topLeaderSearch(this, 'schools');">
							<div class='home_label'>Find:</div>
							<input type="text" class='topssearch' name="" value="" />
							<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
							<div class="clear"></div>
						</form>
					</div>

					<div class="topleaders"><a href="/top_greek_affils" onclick="return clickTopLeader(this, 'greeks');">Top Greek Affils</a></div>
					<div class="tophead">
						<h4>Top Greek Affils</h4>
						<form class="searchfield" onsubmit="return topLeaderSearch(this, 'greeks');">
							<div class='home_label'>Find:</div>
							<input type="text" class='topssearch' name="" value="" />
							<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
							<div class="clear"></div>
						</form>
					</div>

					<div class="topleaders"><a href="/top_cities" onclick="return clickTopLeader(this, 'cities');">Top Cities</a></div>
					<div class="tophead">
						<h4>Top Cities</h4>
						<form class="searchfield" onsubmit="return topLeaderSearch(this, 'cities');">
							<div class='home_label'>Find:</div>
							<input type="text" class='topssearch' name="" value="" />
							<input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/smallsearch.jpg" class="img" name="" value="" />
							<div class="clear"></div>
						</form>
					</div>
					<div class='top_leaders_loader' style='height:10px;position: absolute;z-index: 200;top: 290px;left: 290px;' class='clear'></div>
				</div>
				<!-- EOF topplayers -->
				<div class="overflow" id='top_leaders_list'>
					<?php echo $this->requestAction('/rankings/ajax_players_stats/');  ?>
				</div>
				<div id='top_loader' style='width:100%;text-align:center;display:none;padding-top:70px;'>
					<img src="/img/ajax-loader.gif" alt="" border="0">
				</div>
				<div class="clear"></div>
			</div>
			<!-- EOF mainbox -->	