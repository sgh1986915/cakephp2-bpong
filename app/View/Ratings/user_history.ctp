<div class="events index">
<h2>History of Player Rating - <?php echo $user['User']['lgn']; ?></h2>
<div>
*OR = Average of Opponents Teammates Ratings before game   <br />
*WT = Game Weighting    <br /> 
<a href="<?php echo MAIN_SERVER; ?>/rankings">How does this work?</a> <br/>
</div>
<table cellpadding="0" cellspacing="0">
<tr >
    <th>Before</th>  
    <th>Team</th>
    <th>Opponent</th>
    <th>Event</th>
    <th>Result</th>
    <th>CD</th>
    <th>OR</th>
    <th>WT</th>
    <th>Change</th>
    <th>After</th>
    
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
        $myTeammatesTotalRating = 0;
        $myTeammatesCount = 0;
        $myOpponentsTotalRating = 0;
        $myOpponentsCount = 0;
        foreach ($ratingChange['Game']['Ratinghistory'] as $gameRatingChange) {
            if ($gameRatingChange['model'] == 'User') {
                if ($gameRatingChange['team_id']==$myTeam['id']) {
                    $myTeammatesTotalRating += $gameRatingChange['before'];
                    $myTeammatesCount++;
                } else {
                    $myOpponentsTotalRating += $gameRatingChange['before'];
                    $myOpponentsCount++;
                }
            }
        }    
    ?>
    <tr>
         <td><?php echo $ratingChange['Ratinghistory']['before']?></td>    
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment']): ?>
        		<a href="/nation/beer-pong-teams/team-info/<?php echo $ratingChange['Team']['slug'].'/'.
               	$ratingChange['Team']['id']; ?>"><?php echo $ratingChange['Team']['name']; ?> </a>
        	<?php else: ?>
        		Participation Adjustment
        	<?php endif; ?>       	
        </td>
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment']): ?>
	        <a href="/nation/beer-pong-teams/team-info/<?php echo $opponent['slug'].'/'.
               $opponent['id']; ?>"><?php echo $opponent['name']; ?> </a>
        	<?php endif;?>
        </td>
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment']): ?>
        	<a href="/event/<?php 
            echo $ratingChange['Game']['Event']['id'].'/'.
            $ratingChange['Game']['Event']['slug']; ?>">
            <?php if ($ratingChange['Game']['Event']['shortname'])
                    echo $ratingChange['Game']['Event']['shortname']; 
                  else
                    echo $ratingChange['Games']['Event']['name'];        
            ?>
            </a>
        	<?php endif;?>
        </td>
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment']) {
        	if ($myTeam == $winningTeam) echo 'Win'; 
        	else echo 'Loss'; 
        }?></td>
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment'])
        	echo $ratingChange['Game']['cupdif']; ?></td>
        <td><?php if (!$ratingChange['Ratinghistory']['adjustment']) {
        	if ($myOpponentsCount == 0)
 		       	echo '5000';
            else
               printf ("%1.0F", $myOpponentsTotalRating / $myOpponentsCount);
        }
           ?></td>
        <td><?php echo $ratingChange['Ratinghistory']['weight']; ?></td>
        <td><?php 
            $change = $ratingChange['Ratinghistory']['after'] - $ratingChange['Ratinghistory']['before']; 
            if ($change > 0) echo "+";
            echo $change;
        ?></td>
        <td><?php echo $ratingChange['Ratinghistory']['after']; ?></td>
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