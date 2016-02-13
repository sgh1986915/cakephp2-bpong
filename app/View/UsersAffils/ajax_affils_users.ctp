<?php $this->Paginator->options(array('url' => $this->passedArgs));?>

<?php if (empty($users)):?>
      <div style='padding-left:15px;font-size:14px;'>There are no players</div>
      <?php else:?>
 	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<th style='text-align:center;' class='first-red-th'>Rank</th>
	  	<th style='text-align:center;'>Player</th>
	  	<th style='text-align:center;'>W</th>
	  	<th>L</th>
	    <th class='last-red-th'>CD</th>	   
	  </tr>
	<?php
		$i = 0;
		foreach($users as  $user):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
		?>
	  <tr <?php echo $class;?>>
		<td><?php echo $user['Ranking']['rank'];?></td>	  
		<td><a href="/u/<?php echo $user['User']['lgn'];?>"><?php echo $user['User']['lgn'];?></a></td>
		<td><?php echo $user['User']['total_wins'];?></td>
		<td><?php echo $user['User']['total_losses'];?></td>
		<td><?php echo $user['User']['total_cupdif'];?></td>
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>     	
<?php endif;?>

	<div class="paginationGames">
		<?php echo $this->element('simple_paging');?>
	</div>
