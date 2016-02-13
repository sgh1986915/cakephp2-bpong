<div class="voting">
    <div id="voting_double_<?php echo $model;?>_<?php echo $modelId;?>">		
         <div class='sum' id="<?php echo $model;?>_sum_<?php echo $modelId;?>">
             <?php echo intval($votesPlus - $votesMinus);?>
         </div>            
    <?php if ($this->Access->getAccess($canVote))://can vote?>  
                                			
    	<?php $up = ""; $down = "";  $curUserId = $this->Access->getLoggeduserId();
    	    if (isset($votes) && !empty($votes[$modelId])) :
				if ($votes[$modelId] >0):
					$up = " voted "; $down = " dis ";
				else:
					$up = " dis "; $down = " voted ";
				endif;
             elseif($this->Access->getAccess("OWNER",$ownerId) || $curUserId == VISITOR_USER || !$curUserId):    
				$up = " dis "; $down = " dis ";
    	     endif;?>
    	 <div class='cont' id="<?php echo $model;?>_arrows_double_<?php echo $modelId;?>">
    		<div class='up_vote <?php echo $up?>'   <?php if(empty($up)):?>   onclick="voting(1,'<?php echo $model;?>',<?php echo $modelId;?>, 'double')" <?php endif; ?> style="cursor:pointer;" >&nbsp;</div>
    		<div class='dw_vote <?php echo $down?>' <?php if(empty($down)):?> onclick="voting(-1,'<?php echo $model;?>',<?php echo $modelId;?>, 'double')" <?php endif; ?> style="cursor:pointer;">&nbsp;</div>
    	</div>
    <?php else:?>
    	<div class='cont' >
    		<div class='up_vote dis'  style="cursor:pointer;" >&nbsp;</div>
    		<div class='dw_vote dis'  style="cursor:pointer;">&nbsp;</div>
    	</div>
    <?php endif;?>	
    	 <div class='cont'>
    		 <div class='pos' id="<?php echo $model;?>_votes_plus_<?php echo $modelId;?>">
    			 <?php echo $votesPlus;?>
    		 </div>
    		 <div class='neg' id="<?php echo $model;?>_votes_minus_<?php echo $modelId;?>">
    			 <?php echo $votesMinus;?>
    		 </div>
    	 </div>
    </div><span id='voting_loader_double_<?php echo $model;?>_<?php echo $modelId;?>' style='display:none;'><img src='<?php echo STATIC_BPONG?>/img/round_middle_loader.gif' /></span>
</div>