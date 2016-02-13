<?php $nameLength = strlen($this->Formater->authorsName($user['User'], 1, $myProfile));?>
<div style='float:right;clear:both;'><?php echo $this->element('facebook_like');?></div>
<div class='clear'></div>
<script type="text/javascript" src='<?php echo STATIC_BPONG ?>/js/votes/votes.js'></script>
<script type="text/javascript">
	$(document).ready(function() {
		customPreLoadPiece("/albums/image_albums_list/<?php echo $userID;?>","#imageAlbumsList", 'paginationImageAlbums', 'imageAlbumsLoader');
		customPreLoadPiece("/albums/video_albums_list/<?php echo $userID;?>","#videoAlbumsList", 'paginationVideoAlbums', 'videoAlbumsLoader');
		customPreLoadPiece("/submissions/submits_list/<?php echo $userID;?>","#submitsList", 'paginationSubmits', 'submitsLoader');
	});
	function selectOrganization() {
		tb_show('selectOrganization','/organizations/ajaxSelectOrganization/?&amp;inlineId=selectOrganization&amp;height=500&amp;width=400&amp;modal=true;');
		return false;	
	}
</script>
<div id="selectHometown"></div>
<?php $this->pageTitle = $this->Formater->authorsName($user['User'], 1, $myProfile) . ' Public Profile'; ?>
<div class="profileWrapper">
		<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> <?php if ($nameLength < 16):?>Public<?php endif;?> Profile <?php if ($isAdmin): ?><a href="/users/settings/<?php echo $userID;?>">edit</a><?php endif; ?></h2>
		<div class='pub_prof' style='height:220px;'>
			<?php if ($user['User']['avatar']): ?>
				<?php echo $this->Image->avatar($user['User']['avatar'], false, 185, array('class' => 'ava_prof', 'style' => 'position: relative')); ?>
			<?php else:?>
				<img src="<?php echo STATIC_BPONG ?>/img/default_avatar_185px.jpg" class="ava_prof" style="position: relative" alt="" border="0">
			<?php endif; ?>
			<div class='cont'>
				<?php if ($user['User']['firstname']):?>
					<span class='nam'>
					<?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?>
					</span>
				<?php endif;?>
				<hr />
				<span>User Name: </span> <span class='nblue'><?php echo $user['User']['lgn']; ?></span><br/>
			<?php if ($user['User']['created'] && $user['User']['created'] != '0000-00-00'): ?>	
			 	<span>Joined: </span> <span class='nblue'><?php echo date('m/d/Y', strtotime($user['User']['created'])); ?></span><br/>
			<?php endif; ?>
			<?php if ($user['User']['birthdate'] && $user['User']['birthdate'] != '0000-00-00' && $this->Time->age($user['User']['birthdate']) > 5): ?>
				<span>Age: </span> <span class='nblue'><?php echo $this->Time->age($user['User']['birthdate']); ?></span><br/>
			<?php endif; ?>
			<?php if ($userStats['total_games']): ?>
				<span>Games Played: </span> <span class='nblue'><?php echo $userStats['total_games']; ?></span><br/>
			<?php endif; ?>
			<?php if ($userStats['average_wins']): ?>
				<span>Win %: </span> <span class='nblue'><?php echo (100 *$userStats['average_wins']); ?></span><br/>
			<?php endif; ?>	
			<?php if ($userStats['average_cupdif']): ?>
				<span>Average CD: </span> <span class='nblue'><?php if ($userStats['average_cupdif'] > 0) echo "+"; echo $userStats['average_cupdif']; ?></span><br/>
			<?php endif; ?>	
            <?php if ($user['User']['rating']): ?>
                <span>BPONG Rating:</span><span class='nblue'>
                <a href="<?php echo MAIN_SERVER.'/ratings/userHistory/'.$user['User']['id']; ?> " style="text-decoration: none"><?php  printf ("%1.0F", $user['User']['rating']);?></a></span>
            <?php endif; ?>   
			<?php if (isset($userRanking['Ranking']['rank'])): ?>
                <span>Rank:</span><span class='nblue'>
                <a href="<?php echo MAIN_SERVER.'/rankings/allusers/'; ?> " style="text-decoration: none"><?php echo $userRanking['Ranking']['rank'];?> out of <?php echo $userRanking['Rankinghistory']['numusers'];?></a></span>
            <?php endif; ?>
            </div>
			<div class='clear'></div>
		</div>
</div>
<div style='float:left;margin-left:10px;>
		<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> <?php if ($nameLength < 16):?>Recent<?php endif;?> Game Results <?php if (!empty($userChart['values']) && !empty($userChart['dates'])):?><a href="/users/stats/<?php echo $userID;?>">view all</a><?php endif;?></h2>
		<?php echo $this->element('/charts/user_profile_chart_little', array('userChart' => $userChart));?>	
</div>
	<br class='clear'/>
	<!-- Affil block -->
		<div style='float:left;<?php if ($myProfile):?>width:405px;<?php else:?>width:100%;<?php endif;?>''>
			<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Affils</h2>
			<div id='users_affils_block'>
				<div id="videoAlbumsList"><?php echo $this->requestAction('/users_affils/usersProfileBlock/' . $userID);  ?></div>
			</div>
		</div>
		<?php if ($myProfile):?>
		<div style='float:left;width:485px;margin-left:30px;'>
			<h2 class='hr'>My NBPL ID</h2>
			<?php if (empty($user['User']['qr_image'])):?>
			QR code not generated yet
			<?php else:?>
			<div style='width:100%;'>
			<img src="<?php echo IMG_QRCODES;?>/<?php echo $user['User']['qr_image'];?>" border="0" style='height:190px;float:left;'>
			<div style='float:left;width:280px;padding-left:10px;padding-top:5px;'>
				Your NBPL ID is used to validate your identity for
				mobile games. To use it, you simply scan it using 
				the NBPL Mobile App. <br/><br/>
				If you lose your NBPL code or it is stolen, click
				"Regenerate" to re-create a new ID image. The
				old ID code will then be invalid. 
			</div>
			<a class='grey_button_link' href="/users/sendme_qrcode" style='margin:10px 10px;'>Email to me</a>	
			<a class='grey_button_link' href="/users/regenerate_my_qrcode" onclick="return confirm('Are you sure?');" style='margin:10px 0px 10px 10px'>Regenerate</a>		
			</div>
			<?php endif;?>
		</div>
		<?php endif;?>		
		<br class='clear'/>
		<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Organizations</h2>	
			<?php 
			$org_i = 0; 
			if (empty($organizations) && !$myProfile):?>
			No Organizations
			<?php else:?>
				<?php 
					foreach ($organizations as $organization):
					$org_i++;
					?>
					<div class="grey_block" style='padding-left:0px; float:left;width:256px;<?php if ($org_i ==1 || (3 % $org_i)):?>margin-right:10px;<?php endif;?>height:120px;margin-bottom:10px;'>
						<table style='width:100%;background:none;height:115px;margin-bottom:0px;'><tr>				
						<?php if (!empty($organization['Image']['filename'])):?>
							<td style='padding-left:2px;padding-right:2px;'>
							<a href="/o/<?php echo $organization['Organization']['slug'];?>" style='text-decoration:none;'><img src="<?php echo IMG_MODELS_URL;?>/thumbs_<?php echo $organization['Image']['filename'];?>" alt="<?php echo $organization['Organization']['name'];?>" border="0" /></a>
							</td>
						<?php endif;?> 		
						<td style='padding-left:2px; padding-right:2px;'>				
							<a href="/o/<?php echo $organization['Organization']['slug'];?>" style='text-decoration:none;'><span style="font-size:15px;" class="b"><?php echo $this->Formater->stringCut($organization['Organization']['name'], 36, '...', 0);?></span></a>
							<br/>	
							<span class='b'><?php if ($organization['Address']['city']):?><?php echo ucwords(strtolower($organization['Address']['city']));?><?php endif;?><?php if (!empty($organization['Address']['Provincestate']['shortname']) && !empty($organization['Address']['city'])):?>,<?php endif;?>
							<?php if (!empty($organization['Address']['Provincestate']['shortname'])):?><?php if (is_numeric($organization['Address']['Provincestate']['shortname'])) { echo $organization['Address']['Provincestate']['name'];}else{ echo $organization['Address']['Provincestate']['shortname'];};?><?php endif;?></span>
							<div style='width:100%;margin-top:10px;font-size:80%;'>
							<span class='nblue'><?php echo $organization['Organization']['count_users'];?></span> <?php echo $this->Language->pluralize($organization['Organization']['count_users'], 'member', 'members'); ?>
							<br/>
							Created by <a style='text-decoration:none;' href="/u/<?php echo $organization['Creator']['lgn'];?>"><?php echo $organization['Creator']['lgn'];?></a>
							</div>
						</td>
						</tr></table>
					</div>			
				<?php endforeach;?>
			<?php endif;?>			
			<?php if ($myProfile):?>
			<div class="grey_block" style='padding-left:0px; width:256px;float:left;text-align:center;height:120px;<?php if (!$org_i || (3 % ($org_i+1))):?>margin-right:10px;<?php endif;?>margin-bottom:10px;'>
				<br/><br/>
		    	<a class="add_link" href="/organizations/add"><span>Create a new Organization</span></a>
    		</div>
			<div class="grey_block" style='padding-left:0px; width:256px;float:left;text-align:center;height:120px;margin-bottom:10px;'>
				<br/><br/>
		    	<a class="add_link" href="#" onclick="return selectOrganization();" ><span>Join an Organization</span></a>
    		</div>   		
    		
			<?php endif;?>
		<br class='clear'/>
		<br class='clear'/>
	<!-- EOF Affl block -->

		<?php if (empty($teams)):?>
		<br class='clear'/>	
		<?php else:?>
			<h2 class='hr' style='margin-bottom:10px;'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Teams</h2>
			<?php foreach ($teams as $team):?>
				<span class='b' style='font-size:15px;'><?php echo $team['Team']['name'];?></span> 
				&nbsp;&nbsp;<a href="/teams/stats/<?php echo $team['Team']['slug'];?>/<?php echo $team['Team']['id'];?>" style='text-decoration:none;'>full results</a> <span class='nblue'>|</span> <a href="/nation/beer-pong-teams/team-info/<?php echo $team['Team']['slug'];?>/<?php echo $team['Team']['id'];?>" style='text-decoration:none;'>team profile</a>				
				<div class='grey_block'>
					<?php foreach ($team['User'] as $teammate):
							if (isset($teammate['Address'][0])) {
								$homeAddress = $teammate['Address'][0];
							} else {
								$homeAddress = array();	
							}
					?>
					
					<div style='float:left;<?php if (!empty($homeAddress['city'])):?>padding-top:7px;<?php endif;?>'><a href="<?php echo '/u/' . $teammate['lgn'];?>"><?php echo $this->Image->avatar($teammate['avatar']);?></a></div>
					<div style='float:left;margin-left:10px;margin-right:15px;'>
						<strong><?php echo $this->Formater->userName($teammate, 1);?></strong>
						<br/>
						<a href="<?php echo '/u/' . $teammate['lgn'];?>" style='text-decoration:none;'><?php echo $teammate['lgn'];?></a>
						<?php if (!empty($homeAddress['city'])):?><br/><span style='font-size:11px;'><?php echo ucwords(strtolower($homeAddress['city']));?></span><?php endif;?><?php if (!empty($homeAddress['Provincestate']['shortname'])):?>, <span style='font-size:11px;'><?php echo $homeAddress['Provincestate']['shortname'];?></span><?php endif;?>
					</div>
					<?php endforeach;?>
					<div style='float:left;width:400px;padding:0px;height:100%;'>
						<table style='background:none;margin-bottom: 0px;'>
							<tr>
								<td align='right' height="25px">Wins:</td><td><strong><?php echo intval($team['Team']['total_wins']);?></strong></td>
								<td align='right'>Average Cup Diff:</td><td><strong><?php if ($team['Team']['averageCupdif'] >0):?>+<?php endif; ?><?php echo $team['Team']['averageCupdif'];?></strong></td>
								<td align='right'>Total games:</td><td><strong><?php echo intval($team['Team']['total_wins'] + $team['Team']['total_losses']);?></strong></td>
							</tr>
							<tr>
								<td align='right' height="25px">Losses:</td><td><strong><?php echo intval($team['Team']['total_losses']);?></strong></td>
								<td align='right'>Win %: </td><td><strong><?php echo 100 * $team['Team']['averageWin'];?></strong></td>
								<td></td><td></td>
							</tr>
						</table>
					</div>
					<br class='clear'/>
				</div><br class='clear'/>
			<?php endforeach;?>	
		<?php endif;?>
	
	<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Photo Albums</h2>
	<div id="imageAlbumsList"><?php echo $this->requestAction('/albums/image_albums_list/User/' . $userID . '/1'); ?></div>
	<div class='imageAlbumsLoader' style='height:10px;' class='clear'></div>
	
	<div class='clear'></div>
	
	<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Video Albums</h2>	
	<div id="videoAlbumsList"><?php echo $this->requestAction('/albums/video_albums_list/User/' . $userID . '/1');  ?></div>
	<div class='videoAlbumsLoader' style='height:10px;' class='clear'></div>
	
	<div class='clear'></div>
	
	<div style='float:right;height:20px;top:20px;position:relative;'>&nbsp;<span class='submitsLoader'></span></div>
	<h2 class='hr'><?php echo $this->Formater->authorsName($user['User'], 1, $myProfile); ?> Submissions</h2>
	<div id = "submitsList"><?php echo $this->requestAction('/submissions/submits_list/' . $userID); ?></div>
	<div class='submitsLoader' style='height:20px;'></div>
	