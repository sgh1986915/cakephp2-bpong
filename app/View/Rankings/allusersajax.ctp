
<table cellpadding="0" cellspacing="0">
<tr >
    <th>Global Rank</th>
    <th>Name</th>
    <th>Rating</th>
</tr>
<?php
    $pageSize = $this->request->params['paging'][key($this->request->params["paging"])]['options']['limit']; 
    $currentRank = $pageSize * ($this->Paginator->current()-1) + 1;
    $currentCount = $currentRank;
    $currentRating = 100000;
?>
<?php foreach ($userRankings as $userRanking):?>
    <tr>
        <td align="center">
            <?php //this is the global rank
            echo $userRanking['Ranking']['rank']; ?>
        </td>
        <td>
            <a href="/u/<?php echo $userRanking['User']['lgn']; ?>"><?php echo $userRanking['User']['firstname'].' '.
               $userRanking['User']['lastname']; ?> </a>
        </td>
        <td align="center">
            <a href="/ratings/userHistory/<?php echo $userRanking['User']['id'];?>">    
                <?php printf("%1.0F",$userRanking['Ranking']['rating']); $currentCount++; ?>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
<div class="paginationRankings">
        <?php echo $this->element('simple_paging');?>
</div>              
     
