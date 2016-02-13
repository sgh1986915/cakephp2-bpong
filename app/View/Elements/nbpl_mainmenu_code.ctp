<?php
$currentMenuTag  = 'Default';
$subMenu   = 0;
$subMenuPoint  = 0;
$url = strtolower($this->request->here);
$action = strtolower('/' . $this->request->params['controller'] . '/' . $this->request->action);

$configs = array();
$pageDetected = 0;
$match = 0;
if ($this!=='/'){
	foreach ($MENU as $menuKey => $subMenus): if ($menuKey!='Default'):
		 if (isset($MENU[$menuKey]['config'])) {
		 	$configs[$menuKey] = $MENU[$menuKey]['config']; 	
		 	unset($MENU[$menuKey]['config']);	
		 	unset($subMenus['config']);
		 } else {
		 	$configs[$menuKey] = $MENU['Default']['config'];	
		 } 		
		 foreach ($subMenus as $subMenuValue => $mainValue){
			 foreach ($mainValue as $subKey => $subValue):
			 	$MENU[$menuKey][$subMenuValue][$subKey] = $subValue = array_merge($configs[$menuKey], $MENU[$menuKey][$subMenuValue][$subKey]);
	 			if (!empty($menu_detect_var) && !$pageDetected) {		 			
		 			if (!empty($subValue['menu_detect_var']) && $menu_detect_var == $subValue['menu_detect_var']) {
			 			$match = 1;
		 				$pageDetected = 1;
		 			}	
		 		} elseif (!empty($subValue['urls']) && !$pageDetected){					
			 		foreach ($subValue['urls'] as $urls):
			 			if (($this->request->params['controller'] == "pages" && $this->request->action !='home') || !empty($subValue['detect_by_url'])) {
				 			$thisUrl = $url;
				 			$match = preg_match('/^'.preg_quote($urls, '/').'$/', $thisUrl);	
				 		} else {
				 			$thisUrl = $action;	
				 			$match = preg_match('/^'.preg_quote($urls, '/').'/', $thisUrl);					 						 			
				 		}
			 			if ($match){
			 				$pageDetected = 1;
			 				break;
			 			}
			 		endforeach;
		 		}
	 			if ($match){
	 				$currentMenuTag = $menuKey;
	 				$subMenu  = $subMenuValue;
	 				$subMenuPoint  = $subKey;
	 				$pageDetected = 1;
	 				$match = 0;
	 			}
			 endforeach;
		 }
	endif; endforeach;
}
// $currentMenuTag = $currentMenuTag;
// $subMenu
/*
if (!isset($MENU[$currentMenuTag]['config'])) {
	$MENU[$currentMenuTag]['config'] = $MENU['Default']['config'];
} else {
	foreach ($MENU['Default']['config'] as $key => $value) :
		if (!isset($MENU[$currentMenuTag]['config'][$key])) {
			$MENU[$currentMenuTag]['config'][$key] = $value;		
		}
	endforeach;
}
foreach ($MENU[$currentMenuTag]['config'] as $key => $value) :
	if (!isset($MENU[$currentMenuTag][$subMenu][$subMenuPoint][$key])) {
		$MENU[$currentMenuTag][$subMenu][$subMenuPoint][$key] = $value;		
	}
endforeach;
*/
// MAIN COL INSTALLATION !!!!!
//echo $currentMenuTag;
if (!empty($MENU[$currentMenuTag][$subMenu][$subMenuPoint])) {
	$pageInfo = $MENU[$currentMenuTag][$subMenu][$subMenuPoint];	
} else {
	$pageInfo = $MENU['Default']['config'];
}
$col_class = '';
if (!empty($pageInfo['main_col'])) {
	if (!empty($pageInfo['left_menu'])) {
		$col_class = 'col maincol';		
	} else {
		$col_class = 'col fullcol';				
	}	
}
// EOF MAIN COL INSTALLATION !!!!!
?>	
<?php if ($pageInfo['left_menu']):?>
<script type="text/javascript">$(document).ready(function(){jQuery('.leftmenu').stickyfloat({duration: 400});});</script>
<?php endif;?>
		<div class="nav">
			<ul>
				<?php 
				$i=0;
				foreach ($topMenus as $menuName => $menuVals):
					$i++;
				?>			
				<li <?php if(!empty($menuVals['class'])):?>class="<?php echo $menuVals['class'];?>" <?php endif;?>><a href="<?php echo $menuVals['link'];?>" class="<?php if ($currentMenuTag == $menuName):?> on<?php endif?><?php if ($i == 1):?> first<?php endif;?>"><?php echo $menuVals['title'];?></a>
				<?php if (empty($menuVals['hide_submenu']) && !empty($MENU[$menuName])):?>	
					<div class="submenu">
						<?php if (!empty($configs[$menuName]['column_name_1']) || !empty($configs[$menuName]['column_name_2']) || !empty($configs[$menuName]['column_name_3'])):?>
						<div class="headers">
							<?php for ($i=1; $i<=3;$i++):?>
								<?php if (!empty($configs[$menuName]['column_name_' . $i])):?>
									<h4 <?php if (empty($configs[$menuName]['column_name_2']) && empty($configs[$menuName]['column_name_3'])):?> style='width:500px !important;' <?php endif;?>><?php echo $configs[$menuName]['column_name_' . $i];?></h4>
								<?php endif;?>
							<?php endfor;?>													
							<div class="clear"></div>
						</div>
						<?php endif;?>	
						<?php foreach ($MENU[$menuName] as $submenuIndex => $submenuPoints):
						?>				
							<ul>
							<?php foreach ($submenuPoints as $key => $value): ?>
								<?php if (!empty ($value['link']) && !empty($value['title'])): ?>
									<li><a href="<?php echo $value['link'] ?>"><?php echo $value['title'] ?></a></li>
								<?php endif; ?>
							<?php endforeach;?>
							</ul>
						<?php endforeach;?>
					</div>
					<?php endif;?>
					</li>	
				<?php endforeach;?>
			</ul>				
		</div>
	</div>
	<!-- EOF header -->
	<div id="content">
			<?php if (!empty($pageInfo['breadcrumbs'])):?>
				<div class="breadcrumps" style='<?php if (!empty($pageInfo['top_info_block']) || !empty($pageInfo['top_info_block_element'])):?>padding-bottom:5px;<?php endif;?>'>
					<?php if ($pageInfo['parent_title'] && $pageInfo['parent_link']):?>
						<a href="<?php echo $pageInfo['parent_link'];?>"><?php echo $pageInfo['parent_title'];?></a> / 
					<?php endif;?>
					<?php if ($pageInfo['title']):?>
						<span class='black'><?php echo $pageInfo['title'];?></span>
					<?php endif;?>
				</div>
				<?php endif;?>	
			<?php if (!empty($pageInfo['top_info_block'])):?>
				<?php echo $pageInfo['top_info_block'];?>
			<?php endif;?>
			<?php if (!empty($pageInfo['top_info_block_element'])):?>
				<?php echo $this->element($pageInfo['top_info_block_element']);?>
			<?php endif;?>
			
			<!-- FLASH !!!! -->
			<?php if ($this->Session->check('Message.flash')):?>
	          <?php echo $this->Session->flash();?>
	        <?php  endif;  ?>
			<!-- EOF FLASH -->
			<div class="contentbox" style='position:relative;'>					
				<?php if ($pageInfo['left_menu']):?>
				<div class="leftmenu" style='position:absolute;margin-left:10px;'>
						<?php if ($i>1 && 1 == 2):?><div class="mhr"></div><?php endif;?>
						<ul class="last">
						<?php 
							$subMenuNums = count($MENU[$currentMenuTag]);
							$i=0;
							foreach ($MENU[$currentMenuTag] as $submenuIndex => $submenuPoints):
							$i++;
							?>
							<?php if (!empty($configs[$currentMenuTag]['column_name_'. $submenuIndex])):?>
								<li><h4><?php echo $configs[$currentMenuTag]['column_name_'. $submenuIndex];?></h4></li>
							<?php endif;?>
							<?php foreach ($submenuPoints as $key => $value): ?>
					           		<?php if (!empty ($value['link'])): ?>
					            					    <li <?php if ($subMenu == $submenuIndex && $subMenuPoint == $key):?>" class='on' <?php endif;?>><a <?php echo !empty($value['param'])?" ".$value['param']." ":"" ?> href="<?php echo $value['link'] ?>"><?php echo $value['title'] ?></a></li>
					            	<?php endif; ?>
				            <?php endforeach; ?>
						<?php endforeach;?>
						</ul>
						<?php 
						/*<ul>
							<li><h4><a href="">Galleries</a></h4></li>
							<li><a href="">Videos</a></li>
							<li><a href="">Teams</a></li>
							<li><a href="">Custom tables</a></li>
						</ul>
						<div class="hr"></div>
						<ul class="last">
							<li><h4><a href="">Info</a></h4></li>
							<li><a href="">Learn the rules</a></li>
							<li><a href="">About the National Beer Pong League</a></li>
							<li class="on"><a href="">Take part in a free event</a></li>
							<li><a href="">Vision & Pillars</a></li>
							<li><a href="">Season & Structure</a></li>
						</ul>*/
						?>
					</div>
					<div style='width:200px; height:1px;'></div>
				<?php endif;?>
				<?php if (!empty($pageInfo['main_col'])):?><div class="<?php echo $col_class;?>"><?php endif;?>
					<?php echo $content_for_layout ?>
				<?php if (!empty($pageInfo['main_col'])):?></div><?php endif;?>
			<div class="clear"></div>
			</div>
		</div>
	<!-- EOF content -->