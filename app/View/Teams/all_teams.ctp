<?php echo $this->Form->create('Team',array('id'=>'TeamFilter','name'=>'TeamFilter','action'=>'allTeams'));?>

<fieldset>
<?php echo $this->Form->input('TeamFilter.name',array('label'=>'Team name'));?>
</fieldset>
<?php echo $this->Form->end('Filter');?> <br />
<?php if (!empty($teams)): ?>
<table border="0" class="teams">
  <?php $i =0;?>
  <?php foreach ($teams as $team): ?>
  <?php if ($i==0):?>
  <tr>
    <td>
      <?php endif;?>
      <?php $i++;?>
      <div class="team_wsobp"> 
        <a href="/nation/beer-pong-teams/team-info/<?php echo $team['Team']['slug'] ?>/<?php echo $team['Team']['id'] ?>"><img src="<?php echo !empty($team['PersonalImage']['filename'])?IMG_MODELS_URL.'/thumbs_'.$team['PersonalImage']['filename']:STATIC_BPONG.'/img/tmb_no_image.gif'?>" /></a><br />
        <a href="/nation/beer-pong-teams/team-info/<?php echo $team['Team']['slug'] ?>/<?php echo $team['Team']['id'] ?>"> <?php echo $team['Team']['name'] ?></a> </div>
      <?php if($i==5):?>
      <?php $i = 0?>
    </td>
  </tr>
  <?php endif;?>
  <?php endforeach; ?>
  <?php if($i >0):?>
  </td>
  
  </tr>
  
  <?php endif;?>
</table>
<div class="paging"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<?php else:?>
<BR/>
<div class="you_have_no">There are no teams matching the criteria.</div>
<?php endif;?>
