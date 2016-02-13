<?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
	<?php if ($userSession['avatar']):?>
	<div class="logavatar">
		<a href="/u/<?php echo $userSession['lgn'];?>" class='top_avatar'><?php echo $this->Image->avatar($userSession['avatar'], true, 185, array('style'=> 'border:0px;height:51px;border: 1px solid #05A7FF;'));?></a>
	</div>
	<?php endif;?>
	<div class="loggedinfo">
		Logged in as:<br />
		<div class="usermenu">
			<a href="/u/<?php echo $userSession['lgn'];?>" class="menu"><?php echo $userSession['lgn'];?></a><br />
			<div class="ppanel" style='<?php if (!empty($userSession['avatar'])):?>right: 85px;<?else:?>right: 30px;<?php endif;?>'>
			<?php echo $this->element('nbpl_usersubmenu');?>
			</div>
		</div>
		<div class="clear"></div>
		<!-- EOF usermenu -->
		<a href="/submit/" class="bs_btn">Upload Content</a>
	</div>			
<?php else:  ?>
	<a href="<?php echo MAIN_SERVER;?>/users/fb_connect" class="fbook">Login with Facebook</a>
	<a href="<?php echo MAIN_SERVER;?>/users/login/?&inlineId=login&amp;height=300&amp;width=400&amp;modal=true;" class="thickbox" id="login" title="Sign In" >Login</a>
	<a href="<?php echo MAIN_SERVER;?>/registration">Sign up</a>
<?php endif; ?>