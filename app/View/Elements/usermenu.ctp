<div class="logmenu">
	<?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
		
			<a class='upload_log' href='/submit/'><img alt='' src="<?php echo STATIC_BPONG?>/img/upload_tmp.png" style='margin-top:12px;' /></a>
			<a class='upload_log' href='#' id="MyBpong"><img alt='' src="<?php echo STATIC_BPONG?>/img/home_tmp.png" style='margin-top:12px;'/></a>
		
		<?php if ($userSession['avatar']):?>
			<a class='ava_log' href='/u/<?php echo $userSession['lgn'];?>'><?php echo $this->Image->avatar($userSession['avatar'], true, 40, array('style'=> 'margin-top:8px;border: 2px solid #255C9F;'));?></a>
		<?php endif;?>		
		
		<p class='sgn'>
			<span>Posting as:</span><br />
			<?php echo $userSession['lgn']; ?><br />
			<?php  $this->Html->link('Profile',   '/u/' . $userSession['lgn']); ?>
			<a href="/u/<?php echo $userSession['lgn'];?>" title="profile" >profile</a> | <a href="<?php echo MAIN_SERVER;?>/logout" >log out</a>
		</p>
		
	<?php else:  ?>
		<a class='ava_log' href='#'><img alt='' src="<?php echo STATIC_BPONG?>/img/no_photo_tmp.png" style='margin-top:4px;' /></a>
		<p class='not_sgn'>
			you are not signed in<br />
			<a href="<?php echo MAIN_SERVER;?>/users/login/?&inlineId=login&amp;height=300&amp;width=400&amp;modal=true;" class="thickbox" id="login" title="Sign In" >sign In</a> | <a href="<?php echo MAIN_SERVER;?>/registration" >new user</a>
			<br/>
			<a href="<?php echo MAIN_SERVER;?>/users/fb_connect" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/fb-icon-small.jpg"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/fb_connect" >login with Facebook</a>
		</p>
		
	<?php endif; ?>
</div>