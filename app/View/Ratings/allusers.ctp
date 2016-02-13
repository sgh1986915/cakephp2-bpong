<div class="events index">
<div> <?php echo $this->Form->create('Rating',array('id'=>'RatingFilter','name'=>'RatingFilter','action'=>'allusers'));?>
  <h2>BPONG Player World Ratings - current as of <?php echo Date("M d, Y"); ?></h2>
    <fieldset>
    <?php echo $this->Form->input('RatingFilter.gender',array('type' => 'select','label'=>'Gender','options' => array('Both'=>'Both','M'=>'M','F'=>'F')));?>
	<?php echo $this->Form->input('RatingFilter.name',array('label'=>'Search for Name'));?>
    </fieldset>
<div class="clear"></div>
  <?php echo $this->Form->end('Filter');?>
</div>


<table cellpadding="0" cellspacing="0">
<tr >
    <th>Rank</th>
    <th>Name</th>
    <th>Rating</th>
</tr>
<?php
    $pageSize = $this->request->params['paging'][key($this->request->params["paging"])]['options']['limit']; 
    $currentRank = $pageSize * ($this->Paginator->current()-1) + 1;
    $currentCount = $currentRank;
    $currentRating = 100000;
?>
<?php foreach ($userRatings as $userRating):?>
    <tr>
        <td>
            <?php 
                if ($userRating['Rating']['rating'] < $currentRating) {
                    $currentRating = $userRating['Rating']['rating'];
                    $currentRank = $currentCount;
                }
            echo $currentRank; ?>
        </td>
        <td>
            <a href="/u/<?php echo $userRating['User']['lgn']; ?>"><?php echo $userRating['User']['firstname'].' '.
               $userRating['User']['lastname']; ?> </a>
        </td>
        <td align="center">
            <a href="/ratings/userHistory/<?php echo $userRating['User']['id'];?>">    
                <?php printf("%1.0F",$userRating['Rating']['rating']); $currentCount++;?>
            </a>
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