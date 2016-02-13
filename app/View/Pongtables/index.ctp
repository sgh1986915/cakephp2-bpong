<div class="actions p10_all">
	<ul>
		<?php if( $CreatedTable ): ?>
			<li><span class="addbtn"><?php echo $this->Html->link('Create table', array('controller'=> 'pongtables', 'action'=>'add'), array('class'=>'addbtn')); ?></span></li>
		<?php endif; ?>
	</ul>
</div>
<hr /><br />

<?php if (!empty($tables)):?>
	<?php	foreach ($tables as $value) :?>
		<div style="width:800px">
		   	<div style="float:left; width:500px">
		   		<?php foreach ($value['Tableimage'] as $value2) :?>
				<a href="<?php echo IMG_MODELS_URL;?>/<?php echo $value2['filename']; ?>" title="" class="thickbox">
		   			<?php echo $this->Html->image(IMG_MODELS_URL . "/thumbs_" . $value2['filename'], array('alt' => $value2['alt'], 'title' => $value2['name'])); ?>
		   		</a>
				<?php endforeach; ?>
			</div>
		    <?php
		    	$current_user = null;
		    	if ( empty($value['Pongtable']['user_id']) ) {
		    		$current_user = $value['Pongtable']['submittedby'];
		    	} else {
		    		$current_user = $value['User']['lgn'];
		    	}
			?>
			<div style="width:250px; float:right"><span class="head">Submitted By:</span>
		    	<span class="descr">
			            	<?php if (empty($current_user)) {
										echo  "No user info";
									} else {
										echo $current_user;
									}
							?>
				</span>
			<br />
			<span class="head">Location:</span>
			<span class="descr">
				<?php
					$address = "";
					$city = "";
					$state = "";
					$country = "";
					if ( !empty ( $value['Address'] ['id'] ) ) {
						$address = $value['Address']['address'];
						$city = $value['Address']['city'];
						$state = $value['Address']['Provincestate']['name'];
						$country = $value['Address']['Country']['name'];
					} else {
						$address = $value['Pongtable']['address'];
					}
					$address_array = compact(array('address','city','state','country'));
					$address_string = "";
					foreach ($address_array as $key => $newval) {
						if (empty($newval)) {
							unset( $address_array[ $key ] );
						}
					}
					if ( !empty( $address_array ) ) {
						$address_string = implode(", ", $address_array);
					}

					if (!empty($address_string)) {
						echo $address_string;
					} else {
						echo "Unknown";
					}
				?>
			</span>
			<br />
			<?php
				//echo $this->Html->link( "Edit", array('controller' => 'pongtables', 'action' => 'edit', $value['Pongtable']['id'] ) );
				//echo $this->Html->link('Delete', array('controller' => 'pongtables', 'action' => 'delete', $value['Pongtable']['id'] ), null, sprintf('Are you sure you want to delete # %s?', $value['Pongtable']['id']));

                $editimage = $this->Html->image(STATIC_BPONG."/img/smalledit.gif",array('style' => 'padding:5px;', 'alt' => 'edit'));
	            $deleteimage = $this->Html->image(STATIC_BPONG."/img/smalldelete.gif",array('style' => 'padding:5px;', 'alt' => 'delete'));

	            if($UpdatedTable=='ALL' || ($UpdatedTable=='OWNER' && $userID == $value['Pongtable']['user_id'])){
	            	echo $this->Html->link( $editimage, array('controller' => 'pongtables', 'action' => 'edit', $value['Pongtable']['id'] ), array('escape'=>false));
	            }
	            if($DeletedTable=='ALL' || ($DeletedTable=='OWNER' && $userID == $value['Pongtable']['user_id'])){
	            	echo $this->Html->link( $deleteimage, array('controller' => 'pongtables', 'action' => 'delete', $value['Pongtable']['id'] ), array('escape'=>false), sprintf('Are you sure you want to delete "%s"?', $value['Pongtable']['id']));
	            }

				unset($deleteimage);
				unset($editimage);

				?>

		</div>
		<div class="clear"></div>
		</div>
		<?php if (!empty($value['Pongtable']['title'])): ?>
			<span class="head">Title</span>
		    <span class="descr">
				<?php echo  $value['Pongtable']['title']; ?>
			</span>
		<?php endif; ?>

		<?php if (!empty($value['Pongtable']['description'])): ?>
		<div class="clear"></div>
		<div class="tabledescr">
			<?php
				echo html_entity_decode($this->Bbcode->convert_bbcode( $value ['Pongtable'] ['description'] ), ENT_QUOTES, 'UTF-8');
			?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

<div class="clear"></div>
<div class="paging2">
	<?php echo $this->Paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled2'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next('next'.' >>', array(), null, array('class'=>'disabled2'));?>
</div>