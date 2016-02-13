<div class="tournaments form">
  <fieldset>
  <legend>Tournament</legend>
  <h3>New tournament assigments</h3>
  <div class="box_check" style="width:500px;">
    <table border="0" cellspacing="0" cellpadding="0" style="width:auto !important">
      <tr>
        <td><?php echo $this->Form->create('Tournament',array('id'=>'Tournament','name'=>'Tournament','url'=>'/events/assignTournament/'.$this->request->data['Event']['id']));?> <?php echo $this->Form->select('Tournament.tournament',$tournaments,false,array('escape' => false),false);?></td>
        <td valign="middle"><label for="TournamentIsSatellite">Is sattelite</label></td>
        <td valign="middle"><?php echo $this->Form->checkbox('Tournament.is_satellite', array('label'=>false));?></td>
      </tr>
    </table>
  </div>
  <?php echo $this->Form->end('Assign');?>
  </fieldset>
  <?php if (!empty($this->request->data['Tournament'])):?>
  <div id="Tournaments"  class="details">
    <table>
      <tr>
        <th>Tournament</th>
        <th>Sattelite</th>
        <th></th>
      </tr>
      <?php foreach ($this->request->data['Tournament'] as $tournament):?>
      <tr>
        <td><?php echo $tournament['name'];?></td>
        <td><?php echo empty($tournament['EventsTournament']['is_satellite'])?'No':'Yes'?></td>
        <td><?php echo $this->Html->link('Remove', array('action'=>'removeTournament',$this->request->data['Event']['id'] ,$tournament['id']), null, 'Are you sure you want to remove ?'); ?></td>
      </tr>
      <?php endforeach;?>
    </table>
  </div>
  <?php endif;?>
</div>
