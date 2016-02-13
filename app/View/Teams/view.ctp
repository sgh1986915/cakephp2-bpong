<?php $this->pageTitle = 'Team Profile: ' . $this->request->data['Team']['name']; ?>
<div style='position: relative;margin-bottom:-30px;bottom:-2px; float:right;'><?php echo $this->element('facebook_like');?></div>
<div class="form">
<h2 class='hr'>Team Profile: <?php echo $this->request->data['Team']['name']?></h2>
<?php
    if (isset($alsoPlaysAs)):
?>
<div class="also_plays_as"><strong>Played in a recent tournament as: </strong><?php echo $alsoPlaysAs; ?></div><br />
<?php endif; ?>
<?php if(count($images)):?><div class="left" style="overflow: hidden; padding:0px;margin-right:15px;">	
            <?php foreach($images as $img){
                ?>
                <img src="<?php echo IMG_MODELS_URL?>/<?php echo $img['Image']['filename'] ?>" border="0" style="max-width:250px; border:1px solid #ccc">
				<?php
            }
       ?>
</div>
<?php endif;?>
<div class="left" style="text-align:left;width:270px;">
	<div class='clear' style='width:100%;'><?php echo $this->element('/teammates', array('teammates' => $teammates));?></div>
	<div class="stats-item">
		<div  class="left team_stats">Wins</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_wins']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Losses:</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_losses']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Average Cup Differential:</div>
		<div class="right"><?php if ($averageCupdif>0):?>+<?php endif;?><?php echo $averageCupdif;?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Average Win/Loss:</div>
		<div class="right"><?php echo $averageWin;?></div>
	</div>	
	
	<div class="stats-item">
		<div  class="left team_stats">Games played:</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_wins']+$this->request->data['Team']['total_losses']);?></div>
	</div>	
	
	
	<div style='margin-top:10px;'>		
		<a href="/teams/stats/<?php echo $this->request->data['Team']['slug'];?>/<?php echo $this->request->data['Team']['id'];?>">
			<img src="<?php echo STATIC_BPONG?>/img/buttons/full_stats_button.gif" border="0" />		
		</a>
	</div>
</div>


<div class="left" style="text-align:left;margin-left:20px;">
	<?php if (!empty($this->request->data['Team']['description'])):?>
	<strong>Team Statement:</strong><br/>
	<?php echo $this->request->data['Team']['description'];?>
	<?php endif;?>
</div>
<div class='clear'><br/></div>
<h2>Teammates</h2>
<?php if (!empty($teammates)):?>
  <table>
    <tr>
      <th>Nick name</th>
      <th>First name</th>
      <th>Last name</th>
    </tr>
<?php 
$i = 0;
foreach ($teammates as $teammate):
    	$class = '';
    	if ($i++ % 2 != 0) {
    		$class = ' class="alt"';
    	}
?>
	 <tr<?php echo $class;?>>
      <td class = 'center'><a href="/users/view/<?php echo urlencode($teammate['User']['lgn'])?>"><?php echo $teammate['User']['lgn']?></a></td>
      <td class = 'center'><?php if ($teammate['User']['show_details']): ?><a href="/users/view/<?php echo urlencode($teammate['User']['lgn'])?>"><?php echo $teammate['User']['firstname']; ?></a><?php else:?>-<?php endif;?></td>
      <td class = 'center'><?php if ($teammate['User']['show_details']): ?><a href="/users/view/<?php echo urlencode($teammate['User']['lgn'])?>"><?php echo $teammate['User']['lastname']; ?></a><?php else:?>-<?php endif;?></td>
      <?php /*?>
      <td><?php echo $teammate['Teammate']['status']?></td>
      <td><?php echo $this->Time->niceDate($teammate['Teammate']['created'])?></td>
      <td><?php if ($teammate['Teammate']['status']=="Pending" && $teammate['User']['id']==$user['id'] && ($this->request->data['Team']['status']=="Created" || $this->request->data['Team']['status']=="Pending")):?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/accept.gif" alt="Accept invitation to this team" title="Accept invitation to this team"/>', array('action'=>'accept', $this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/decline.gif" alt="Decline invitation to this team" title="Decline invitation to this team"/>', array('action'=>'decline', $this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false)); ?>
		  <?php endif;?>
		  <?php if (($teammate['Teammate']['status']=="Creator" || $teammate['Teammate']['status']=="Accepted" )&& $teammate['User']['id']==$user['id'] && $this->request->data['Team']['status']!="Deleted"):?>
		  	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete me from this team" title="Delete me from this team"/>', array('controller'=>'teammates','action'=>'delete',$this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false),'Are you sure you want to delete from this team?'); ?>
		  <?php endif;?>
      </td>
      <?php */?>
    </tr>
<?php endforeach;?>
  </table>

<?php else:?>
	There are no teammates;
<?php endif;?>


<!-- Assigments-->
<?php if (!empty($teamAssigments)):?>
<h2>Events team is assigned to</h2>
<table>
<tr>
	<th>Event</th>
	<th>Date</th>
	<th>Team name</th>
	<th>Wins</th>
	<th>Losses</th>
	<th>Cup Differential</th>
	<th>Rank</th>
</tr>
<?php 
$i = 0;
foreach($teamAssigments as $teamAssigment):
    	$class = '';
    	if ($i++ % 2 != 0) {
    		$class = ' class="alt"';
    	}

?>
<tr<?php echo $class;?>>
	<td>
	<?php if ($teamAssigment['TeamsObject']['model'] == 'Event'):?>
		<a href="/event/<?php echo $teamAssigment[$teamAssigment['TeamsObject']['model']]['id'];?>/<?php echo $teamAssigment[$teamAssigment['TeamsObject']['model']]['slug'];?>"><?php echo $teamAssigment[$teamAssigment['TeamsObject']['model']]['name'];?></a>	
	<?php else:?>
		<?php echo $teamAssigment[$teamAssigment['TeamsObject']['model']]['name'];?>	
	<?php endif;?>	
	</td>
	<td><?php echo date('m/d/Y', strtotime($teamAssigment[$teamAssigment['TeamsObject']['model']]['end_date']));?></td>	
	<td><?php echo $teamAssigment['TeamsObject']['name']?></td>
	<td class = 'center'><?php echo $teamAssigment['TeamsObject']['wins'];?></td>
	<td class = 'center'><?php echo $teamAssigment['TeamsObject']['losses'];?></td>
	<td class = 'center'><?php if ($teamAssigment['TeamsObject']['cupdif'] > 0) {echo "+";}?><?php echo $teamAssigment['TeamsObject']['cupdif'];?></td>
	<td class = 'center'><?php echo $teamAssigment['TeamsObject']['rank'];?></td>			
</tr>
<?php endforeach;?>
</table>
<?php if ($this->Paginator->numbers()):?>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php endif;?>
<?php endif;?>
<!-- EOF Assigments-->
</div>