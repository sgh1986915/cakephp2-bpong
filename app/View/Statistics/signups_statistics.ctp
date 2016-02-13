
  <h3>  Signup statistics for the <?php echo $modelInformation[$modelName]['name']?> </h3>

 <p>
   Number of individuals that are partially paid: <?php echo $partlyPaidIndividualSignups;?> ;<br/>
</p>
 <p>
   Number of teams that are partially paid: <?php echo $partlyPaidTeamSignups; ?>
 </p>

  <p>
  Number of individuals that are fully paid: <?php echo $paidIndividualSignups; ?>;<br/>
</p>
  <p>
  Number of teams that are fully paid: <?php echo $paidTeamSignups; ?>;<br/>
</p>
<p>
  Number of players we are expecting to have if everyone pays the remaining balance: <?php echo $finalPlayerCount; ?>;<br />
</p>  
<p>
  Number of outstanding Free Promocodes: <?php echo $promocodesCount; ?></p>
<p>
  Amount of money we are expecting to collect if the people pay the remaining balance: $<?php echo sprintf("%01.2f",$willPaid[0][0]['willpaid']);?>;<br/>
</p>
<p>
  Amount of money that is still to be collected: $<?php echo sprintf("%01.2f",$paymentRemaining[0][0]['paymentremaining']);?><br/>
  </p>
<p>
  Number of teams entered into the tournament: <?php echo $teamsCount; ?>;<br/>
</p>

<?php if ( !empty($modelInformation[$modelName]['is_room'])):?>
<p>
  Number of complete rooms: <?php echo $roomsCount?>;<br/>
</p>
<?php endif;?>
<p>
  <?php
    $model = $this->request->params['pass'][0];
    $model_id = $this->request->params['pass'][1];
    echo $this->Html->link("Not Paid users","/statistics/notPaidUsersCsv/$model/$model_id"); ?><br/>
</p>

<?php if ( !empty($modelInformation[$modelName]['is_room'])):?>
<p>
  <?php
    echo $this->Html->link("Not Room Added Signups", "/statistics/notRoomConfirmedCsv/$model/$model_id"); ?><br/>
</p>
<?php endif;?>

<p>
  <?php
    echo $this->Html->link("Not Team Complete Signups", "/statistics/notTeamAddedCsv/$model/$model_id"); ?><br/>
</p>
<p>
    <?php echo $this->Html->link("Not Team added at all Signups","/statistics/notTeamAddedAtAllCsv/$model/$model_id"); ?><br />
</p>
<p>
  <?php
    echo $this->Html->link("Master list", "/statistics/masteListCsv/$model/$model_id"); ?><br/>
</p>
<p>
  <?php
    echo $this->Html->link("Combine report", "/statistics/combineCsv/$model/$model_id"); ?><br/>
</p>
<p>
  <?php
    echo $this->Html->link("Rooming list", "/casinos/getCsv/$model/$model_id"); ?><br/>
</p>
