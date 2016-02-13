<?php if (!$myProfile && empty($hometown) && empty($city) && empty($school) && empty($greek)):?>
No Affils
<?php else:?>
	<script type="text/javascript">
		function selectAffilHometown() {
			tb_show('selectHometown','/users_affils/ajaxSelectHometown/?&amp;inlineId=selectHometown&amp;height=500&amp;width=400&amp;modal=true;');
			return false;	
		}
		function selectAffilCity() {
			tb_show('selectCity','/users_affils/ajaxSelectCity/?&amp;inlineId=selectHometown&amp;height=500&amp;width=400&amp;modal=true;');
			return false;	
		}	
		function selectAffilSchool() {
			tb_show('selectSchool','/users_affils/ajaxSelectSchool/?&amp;inlineId=selectHometown&amp;height=500&amp;width=400&amp;modal=true;');
			return false;	
		}
		function selectAffilGreek() {
			tb_show('selectGreek','/users_affils/ajaxSelectGreek/?&amp;inlineId=selectHometown&amp;height=500&amp;width=400&amp;modal=true;');
			return false;	
		}
	</script>
	<style type="text/css">
		.profile_affil {float:left;width:183px;height:75px;margin-bottom:10px;}
		.none_click {color:#505050; font-weight:bold;}
		.affil_type {float:left;padding-left:0px;padding-top:12px;}
		.affil_image {float:left;padding:5px;padding-right:0px;}
		.affil_right {margin-right:10px;}
	</style>
	
	<?php if (!empty($hometown) || $myProfile):?>
	<div class="grey_block profile_affil affil_right">
		<div style='width:100%;'>
			<div class='affil_image'><?php if($myProfile):?><a href="#" onclick="return selectAffilHometown();" style='text-decoration:none;'><?php endif;?><img src="<?php echo STATIC_BPONG ?>/img/affils/hometown.png" border="0" /><?php if($myProfile):?></a><?php endif;?></div>
			<div class='affil_type'><?php if($myProfile):?><a href="#" onclick="return selectAffilHometown();" style='text-decoration:none;'><?php endif;?><span class='b' style='color:black;'>Hometown</span><?php if($myProfile):?></a><?php endif;?></div>
			<div class="clear"></div>
		</div>	
		<div style='width:100%;text-align:center;'>
			<?php if (!empty($hometown)):?><?php if($myProfile):?><a href="#" style='text-decoration:none;' onclick="return selectAffilHometown();"><?php endif;?><strong><?php echo $this->Formater->stringCut($hometown, 18, '...', 0);?></strong><?php if($myProfile):?></a><?php endif;?><?php else:?><a href="#" style='text-decoration:none;' onclick="return selectAffilHometown();"><span class='none_click' >None (click to select)</span></a><?php endif;?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if (!empty($city) || $myProfile):?>
	<div class="grey_block profile_affil <?php if (!$myProfile):?>affil_right<?php endif;?>">
		<div style='width:100%;'>
			<div class='affil_image' style='margin-left:5px;margin-right:10px;'><?php if($myProfile):?><a href="#" onclick="return selectAffilCity();" style='text-decoration:none;'><?php endif;?><img src="<?php echo STATIC_BPONG ?>/img/affils/city.png" border="0" /><?php if($myProfile):?></a><?php endif;?></div>
			<div class='affil_type'><?php if($myProfile):?><a href="#" onclick="return selectAffilCity();" style='text-decoration:none;'><?php endif;?><span class='b' style='color:black;'>Current City</span><?php if($myProfile):?></a><?php endif;?></div>
			<div class="clear"></div>
		</div>	
		<div style='width:100%;text-align:center;margin-top:8px;'>
			<?php if (!empty($city)):?><?php if($myProfile):?><a href="#" style='text-decoration:none;' onclick="return selectAffilCity();"><?php endif;?><strong><?php echo $this->Formater->stringCut($city, 18, '...', 0);?></strong><?php if($myProfile):?></a><?php endif;?><?php else:?><a href="#" style='text-decoration:none;' onclick="return selectAffilCity();"><span class='none_click' >None (click to select)</span></a><?php endif;?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if (!empty($school) || $myProfile):?>
	<div class="grey_block profile_affil affil_right">
		<div style='width:100%;'>
			<div class='affil_image' style='margin-left:5px;margin-right:5px;'><?php if($myProfile):?><a href="#" onclick="return selectAffilSchool();" style='text-decoration:none;'><?php endif;?><img src="<?php echo STATIC_BPONG ?>/img/affils/school.png" border="0" /><?php if($myProfile):?></a><?php endif;?></div>
			<div class='affil_type'><?php if($myProfile):?><a href="#" onclick="return selectAffilSchool();" style='text-decoration:none;'><?php endif;?><span class='b' style='color:black;'>School</span><?php if($myProfile):?></a><?php endif;?></div>
			<div class="clear"></div>
		</div>	
		<div style='width:100%;text-align:center;'>
			<?php if (!empty($school)):?><?php if($myProfile):?><a href="#" style='text-decoration:none;' onclick="return selectAffilSchool();"><?php endif;?><strong><?php echo $this->Formater->stringCut($school, 18, '...', 0);?></strong><?php if($myProfile):?></a><?php endif;?><?php else:?><a href="#" style='text-decoration:none;' onclick="return selectAffilSchool();"><span class='none_click' >None (click to select)</span></a><?php endif;?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if (!empty($greek) || $myProfile):?>
	<div class="grey_block profile_affil <?php if (!$myProfile):?>affil_right<?php endif;?>">
		<div style='width:100%;'>
			<div class='affil_image' style='margin-left:18px;margin-right:5px;'><?php if($myProfile):?><a href="#" onclick="return selectAffilGreek();" style='text-decoration:none;'><?php endif;?><img src="<?php echo STATIC_BPONG ?>/img/affils/greek.png" border="0" /><?php if($myProfile):?></a><?php endif;?></div>
			<div class='affil_type'><?php if($myProfile):?><a href="#" onclick="return selectAffilGreek();" style='text-decoration:none;'><?php endif;?><span class='b' style='color:black;'>Greek</span><?php if($myProfile):?></a><?php endif;?></div>
			<div class="clear"></div>
		</div>	
		<div style='width:100%;text-align:center;margin-top:8px;'>
			<?php if (!empty($greek)):?><?php if($myProfile):?><a href="#" style='text-decoration:none;' onclick="return selectAffilGreek();"><?php endif;?><strong><?php echo $this->Formater->stringCut($greek, 18, '...', 0);?></strong><?php if($myProfile):?></a><?php endif;?><?php else:?><a href="#" style='text-decoration:none;' onclick="return selectAffilGreek();"><span class='none_click' >None (click to select)</span></a><?php endif;?>
		</div>
	</div>
	<?php endif;?>
	<br class='clear'/>
<?php endif;?>