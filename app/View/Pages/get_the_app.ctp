<?php $this->set('title_for_layout', 'The Beer Pong mobile app');?>
<script type="text/javascript">
	$(document).ready(function(){
        $('.get_app_email1').focus(function(){if ($('.get_app_email1').val()=="Enter email address")$('.get_app_email1').val('');});
		$('.get_app_email1').blur(function(){if ($('.get_app_email1').val()=="")$('.get_app_email1').val("Enter email address");});

        $('.get_app_email2').focus(function(){if ($('.get_app_email2').val()=="Enter email address")$('.get_app_email2').val('');});
		$('.get_app_email2').blur(function(){if ($('.get_app_email2').val()=="")$('.get_app_email2').val("Enter email address");});
    });
	function chech_form (num) {
		if ($('.get_app_email' + num).val() == "" || $('.get_app_email' + num).val() == "Enter email address") {
			alert('Please specify your email.');
			return false;
		} else {
			return true;
		}
	}

</script>

<div class="mobileappbox">
	<h2  class='thin_h'>The Beer Pong mobile app<br /> <span>Everything you need to get involved!</span></h2>
	<ul>
		<li>
			Track your games
			<span>Record official game results for yourself or any number of opponents.</span>
		</li>
		<li>
			Find local players
			<span>Find players worth playing in your area and challenge them to a game.</span>
		</li>
		<li>
			See your rankings
			<span>See how your City, School, Fraternity, or other Organization stack up worldwide.</span>
		</li>
		<li>
			End rule disputes
			<span>Browse the full set of NBPL Official Rules.</span>
		</li>
		<li>
			Never miss a tournament
			<span>Get notified whenever a new tournament is added in your area.</span>
		</li>
	</ul>
	<div class="downloadbox">
		<a href="<?php echo ANDROID_APPLICATION_LINK; ?>" class="android"></a>
		<div class="ios">
			<b>Coming soon for iOS</b><br />
			Email me when it’s ready.
		</div>
		<form name="search" action="/Pages/send_iphone_request/" method="post" onsubmit = "return chech_form(1);">
		<?php echo $this->Form->input('Request.email', array('label'=>false, 'div' => false, 'class' => 'search get_app_email1', 'value' => 'Enter email address'));?>
		<input type="submit" class="bs_btn" value="Submit" />
		</form>
	</div>
	<!-- EOF downloadbox -->
	<div class="clear"></div>
</div>
<!-- EOF mobileappbox -->

<div class="col b47" style="padding-bottom:40px;">
	<h3  class='thin_h'>Easy to take part and stay up to date</h3>
	<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/screen.jpg" class="img" alt="" />
		<h4>Stay connected</h4>
		<p>The best part of the world of beer pong is the fact that millions of people play the game every day. With the NBPL Mobile App, you'll stay up to date with everything going on the beer pong world, as it's happening.</p>
	<div class="downloadbox">
		<a href="<?php echo ANDROID_APPLICATION_LINK; ?>" class="android"></a>
		<div class="ios">
			<b>Coming soon for iOS</b><br />
			Email me when it’s ready.
		</div>
		<form name="search" action="/Pages/send_iphone_request/" method="post" onsubmit = "return chech_form(2);">
		<?php echo $this->Form->input('Request.email', array('label'=>false, 'div' => false, 'class' => 'search get_app_email2', 'value' => 'Enter email address'));?>
		<input type="submit" class="bs_btn" value="Submit" />
		</form>
	</div>
	<!-- EOF downloadbox -->
	<!-- EOF slider -->
	<div class="clear"></div>
</div>
<!-- EOF b47 -->

	<div class="col b47 last">
		<h3  class='thin_h'>Features made to make the game even more cool</h3>
		<ul class="twocol">
			<li>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/find.jpg" class="img" alt="" />
				<div class="ltext24">
					<h4>Find games nearby</h4>
					<p>With the Pong Map, you can find players, events, venues, and recent games in your area.</p>
				</div>
			</li>
			<li>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/keep.jpg" class="img" alt="" />
				<div class="ltext24">
					<h4>Keep it real</h4>
					<p>QR Code Validation ensures legitimate results are recorded.</p>
				</div>
			</li>
			<li>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/stick.jpg" class="img" alt="" />
				<div class="ltext24">
					<h4>Help out the cause</h4>
					<p>You can affiliate with your school, greek organization, hometown, or city. Every time you play a game, you score points for your affiliations.</p>
				</div>
			</li>
			<li>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/check.jpg" class="img" alt="" />
				<div class="ltext24">
					<h4>See who's the best</h4>
					<p>With the NBPL Mobile App, you can view realtime leaderboards from around the globe.</p>
				</div>
			</li>
			<li>
				<img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/see.jpg" class="img" alt="" />
				<div class="ltext24">
					<h4>So much more</h4>
					<p>Browse events, see what table you're on, read the rules, and more. Get connected today.</p>
				</div>
			</li>
		</ul>
	</div>
	<!-- EOF b47 -->
	<div class="lstext twocol">
			<h3>About the NBPL</h3>
			<p>We understand the sanctity of the game of beer pong. In 1999 we created BPONG.COM, the home of beer pong on the web, and in 2005, we founded  the largest nationally-organized beer pong tournament in the world: The World Series of Beer Pong.  Now, in 2012, we’ve again raised the bar with the creation of the National Beer Pong League  <a href="#">more...</a></p>
	</div>
	<div class="lstext twocol">
		<h3  class='thin_h'>The Rules of Beer Pong</h3>
		<p>The rules of Beer Pong are designed with three purposes in mind:
			<ul class="list">
				<li>Fairness to all players</li>
				<li>Efficiency in running a maximum number of games simultaneously</li>
				<li>Minimization of possible disputes between participants</li>
			</ul>
			<a href="#">View the Official Rules of Beer Pong</a>
		</p>
	</div>
	<!-- EOF lstext -->

<div class="clear"></div>