<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
      <?php if (empty($teams)):?>
      	<div style='padding-left:15px;font-size:14px;'>There are no teams assigned to this event.</div>
      <?php else:?>
	 	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<th class='first-red-th'>Name</th>
		    <th class='last-red-th'>Image</th>
		  </tr>
		<?php
			$i = 0;
			foreach($teams as  $team):
				$class = null;
				if ($i++ % 2 != 0) {
					$class = ' class="gray"';
				}
			?>
		  <tr <?php echo $class;?>>
			<td><a href="/nation/beer-pong-teams/team-info/<?php echo $team['Team']['slug'] ?>/<?php echo $team['Team']['id']; ?>"><?php echo $team['TeamsObject']['name'] ?></a></td>
			<td><?php if (!empty($team['Team']['PersonalImage']['filename'])):?><img src="<?php echo IMG_MODELS_URL.'/thumbs_'. $team['Team']['PersonalImage']['filename'];?>" /><?php endif;?></td>

		  </tr>
		<?php endforeach;?>
		  <!-- <tr class="gray"> -->
		</table>


      <?php endif;?>

	<div class="paginationTeams">
		<?php echo $this->element('simple_paging');?>
	</div>
