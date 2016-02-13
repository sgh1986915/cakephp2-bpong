<div class="events index">
<h2>Team Rankings</h2>
<table cellpadding="0" cellspacing="0">
<tr >
    <th>Rank</th>
    <th>Name</th>
    <th>Rating</th>
</tr>
<?php foreach ($teamRankings as $teamRanking):?>
    <tr>
        <td>
            <?php echo $teamRanking['Ranking']['rank']; ?>
        </td>
        <td>
            <a href="/nation/beer-pong-teams/team-info/<?php echo $teamRanking['Team']['slug'].'/'.
               $teamRanking['Team']['id']; ?>"><?php echo $teamRanking['Team']['name']; ?> </a>
        </td>
        <td align="center">
            <a href="/ratings/teamHistory/<?php echo $teamRanking['Team']['id']; ?>"><?php  echo $teamRanking['Ranking']['rating']; ?></a> 
        </td>
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