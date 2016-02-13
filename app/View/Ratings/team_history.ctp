<div class="events index">
<h2>History of Team Rating - <?php echo $team['Team']['name']; ?></h2>
<div>
*BR = Team Rating before game   <br />
*OR = Opponents Rating before game   <br />
*WT = Game Weighting    <br /> 
*FR = Team Final Rating
</div>
<table cellpadding="0" cellspacing="0">
<tr >
    <th>BR</th>
    <th>Opponent</th>
    <th>Event</th>
    <th>Result</th>
    <th>CD</th>
    <th>OR</th>
    <th>WT</th>
    <th>Change</th>
    <th>FR</th>
    
</tr>
<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
<?php foreach ($ratingChanges as $ratingChange):?>
    <?php
        if ($ratingChange['Game']['winningteam_id'] == $ratingChange['Game']['team1_id']) 
            $winningTeam = $ratingChange['Game']['Team1'];
        else
            $winningTeam = $ratingChange['Game']['Team2'];
        if ($ratingChange['Game']['team1_id'] == $ratingChange['Ratinghistory']['team_id']) {
            $opponent = $ratingChange['Game']['Team2'];
            $myTeam = $ratingChange['Game']['Team1'];
        } else {
            $opponent = $ratingChange['Game']['Team1'];
            $myTeam = $ratingChange['Game']['Team2'];           
        }
            
    ?>
    <tr>
        <td><?php echo $ratingChange['Ratinghistory']['before']; ?></td>  
        <td><a href="/nation/beer-pong-teams/team-info/<?php echo $opponent['slug'].'/'.
               $opponent['id']; ?>"><?php echo $opponent['name']; ?> </a></td>
        <td><a href="/event/<?php 
            echo $ratingChange['Game']['Event']['id'].'/'.
            $ratingChange['Game']['Event']['slug']; ?>"><?php
             echo $ratingChange['Game']['Event']['shortname']; ?></a></td>
        <td><?php if ($myTeam == $winningTeam) echo 'Win'; else echo 'Loss'; ?></td>
        <td><?php echo $ratingChange['Game']['cupdif']; ?></td>
        <td><?php 
            foreach ($ratingChange['Game']['Ratinghistory'] as $thisGamesHistory) {
                if ($thisGamesHistory['model']=='Team' && $thisGamesHistory['team_id'] == $opponent['id'])
                    echo $thisGamesHistory['before'];
            }
        ?></td>
        <td><?php echo $ratingChange['Ratinghistory']['weight']; ?></td>
        <td><?php 
            $change = $ratingChange['Ratinghistory']['after'] - $ratingChange['Ratinghistory']['before']; 
            if ($change > 0) echo "+";
            echo $change;
        ?></td>
        <td>
            <?php echo $ratingChange['Ratinghistory']['after']; ?></td>
    </tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
    <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
    <?php echo $this->Paginator->numbers();?>
    <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
    <?php echo $this->element('pagination'); ?>
</div>