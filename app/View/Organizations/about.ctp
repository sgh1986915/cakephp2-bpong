<?php if (!empty($organization['Image']['filename'])):?>
	<img style='margin-right:10px; float:left;' src="<?php echo IMG_MODELS_URL;?>/middle_<?php echo $organization['Image']['filename'];?>" alt="<?php echo $organization['Organization']['name'];?>" border="0" />
<?php endif;?>
<?php echo $organization['Organization']['about'];?>

<div style='float:right;font-size:14px;font-weight:bold;width:100%; text-align:right;border-top: 1px solid #CCCCCC;'>
	<?php if ($organization['Address']['city']):?><?php echo $organization['Address']['city'];?><?php endif;?><?php if (!empty($organization['Address']['Provincestate']['shortname']) && !empty($organization['Address']['city'])):?>,<?php endif;?>
	<?php if (!empty($organization['Address']['Provincestate']['shortname'])):?><?php if (is_numeric($organization['Address']['Provincestate']['shortname'])) { echo $organization['Address']['Provincestate']['name'];}else{ echo $organization['Address']['Provincestate']['shortname'];};?><?php endif;?>
</div>