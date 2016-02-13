<?php
	$this->set('title_for_layout', "The National Beer Pong League");
?>
<?php echo $this->element('/home/top_slider');?>
      <?php if ($this->Session->check("Tournament")): $_tournament =  $this->Session->read("Tournament");endif;?>
			<div class="col lcol31">
				<h3 class='thin_h'>Get the NBPL Mobile App</h3>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/phone.jpg" class="img" alt="" />
				<div class="ltext20">
					<p>The NBPL Mobile App is everything you need to get involved with the League & keep your finger on the pulse of the game worldwide.</p>
					<ul class="beerlist">
						<li>Track your games</li>
						<li>Find local players</li>
						<li>See your rankings</li>
						<li>End rule disputes</li>
						<li>Never miss a tournament</li>
					</ul>
					<a href="<?php echo ANDROID_APPLICATION_LINK; ?>" class="bluebtn">DOWNLOAD FOR ANDROID</a>
				</div>
				<div class="clear"></div>
				<div class="row" style="position:relative; top:-10px;">
					<a href="/get_the_app">Learn more</a>&nbsp;|&nbsp;<a href="/get_the_app">Contact you when iPhone app arrives</a>
				</div>
			</div>
			<!-- EOF lcol -->
			<div class="col ccol">
				<h3  class='thin_h'>The World Series of Beer Pong is fast approaching!</h3>
				<img src="http://bc8754860dd7c694b38d-1eaf8097c9b8f126537edc8977671f2e.r58.cf2.rackcdn.com/WSOBP%20VIII.png" class="img" style="width:379px;height:191px;" alt="WSOBP" />
				<div class="ltext20">
					<?php if (!empty($_tournament['remain_to_signup']) && $_tournament['remain_to_signup']>0):?>
					<div class="daystosign">
						<h4>Only <?php echo $_tournament['remain_to_signup'];?> <?php echo $this->Language->pluralize($_tournament['remain_to_signup'], 'Day', 'Days')?> to Sign-up <span>for WSOBP VIII</span></h4>
					</div>
					<?php endif;?>
					<p><a href = "<?php echo MAIN_SERVER.'/wsobp'; ?>"><b>The World Series of Beer Pong</b></a>
	is the largest, longest-running organized beer pong (aka Beirut) tournament in the world, created by beer pong players, for beer pong players. Last years event drew over 1,000 participants from 45 U.S. States and 5 Canadian Provinces, offering the largest payout in beer pong history of $65,000.</p>
					<a href="/wsobp" class="bluebtn" style="margin: 0px;">RESERVE YOUR SPOT TODAY</a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="big row" style="padding-top:0px;"><a href="/wsobp/beer-pong-teams">WSOBP 2012 Teams</a>&nbsp;|&nbsp;<a href="/wsobp/official-rules-of-the-world-series-of-beer-pong">Official rules</a>&nbsp;|&nbsp;<a href="/wsobp/gallery">Gallery</a>&nbsp;|&nbsp;<a href="/wsobp/pricing">Pricing</a></div>
			</div>
			<!-- EOF ccol -->
			<div class="clear"></div>
	<div class="llcol">
			<?php echo $this->element('/home/google_map');?>

			<!-- EOF b63 -->

			<div class="textbox">

				<div class="row">
					<h3  class='thin_h'>What's new at BeerPong.COM</h3>
					<div class="simplecol">
						<h4>WSOBP VII Promo Video</h4>
						<a href="http://www.youtube.com/watch?feature=player_embedded&v=TYqnVpQ7bfQ" target="_blank"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/video_b.jpg" alt="" /></a>
					</div>
					<?php echo $this->element('/home/new_stuff');?>
				</div>
				<!-- EOF row -->

				<div class="lstext">
					<div class="row">
						<h3  class='thin_h'>The Rules of Beer Pong</h3>
						<p>The rules of Beer Pong are designed with three purposes in mind:
							<ul class="list">
								<li>Fairness to all players</li>
								<li>Efficiency in running a maximum number of games simultaneously</li>
								<li>Minimization of possible disputes between participants</li>
							</ul>
							<a href="/vault/general-beer-pong-rules">View the Official Rules of Beer Pong</a>
						</p>
					</div>
					<!-- EOF row -->
				</div>
				<!-- EOF lstext -->

				<?php echo $this->element('/home/blogpost');?>
			<div class="clear"></div>
			</div>
			<!-- EOF textbox -->
	</div>
	<!-- EOF llcol -->
	<div class="rlcol">
			<?php echo $this->element('/home/store_slider');?>
			<div class="col b30">
			<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="http://www.facebook.com/bpong" width="300" height="285" show_faces="true" border_color="white" stream="false" header="false"></fb:like-box>
			</div>
			<!-- EOF b30 -->
			<?php echo $this->element('/home/forumposts');?>
	</div>
	<!-- EOF rlcol -->
	<div class="clear"></div>
		<?php echo $this->element('/home/leaderboards');?>
	<div class="clear"></div>
				<?php /*echo $this->element('/home/feeds');*/?>
					<?php echo $this->element('/home/little_slider');?>
					<div class="row" style='float:left;width:300px;margin-left:20px;'>
					<h3  class='thin_h'>About the NBPL</h3>
					<p>We understand the sanctity of the game of beer pong. In 1999 we created BPONG.COM, the home of beer pong on the web, and in 2005, we founded  the largest nationally-organized beer pong tournament in the world: The World Series of Beer Pong.  Now, in 2012, we’ve again raised the bar with the creation of the National Beer Pong League  <a href="/about_nbpl">more...</a></p>
					</div>
