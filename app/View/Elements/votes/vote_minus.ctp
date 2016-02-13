<div class="voting" style='background: none;'>
    <div id="voting_minus_<?php echo $model;?>_<?php echo $modelId;?>">		     <div class='cont'>
		 <div class='neg' id="<?php echo $model;?>_votes_minus_<?php echo $modelId;?>">
			 <?php echo $votesMinus;?>
		 </div>
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
    	 <div class='cont' id="<?php echo $model;?>_arrows_minus_<?php echo $modelId;?>">
    		<div class='dw_vote <?php echo $down?>' <?php if(empty($down)):?> onclick="voting(-1,'<?php echo $model;?>',<?php echo $modelId;?>, 'minus')" <?php endif; ?> style="cursor:pointer;">&nbsp;</div>
    	</div>
    <?php else:?>
    	<div class='cont' >
    		<div class='dw_vote dis'  style="cursor:pointer;">&nbsp;</div>
    	</div>
    <?php endif;?>
    </div><span id='voting_loader_minus_<?php echo $model;?>_<?php echo $modelId;?>' style='display:none;'><img src='<?php echo STATIC_BPONG?>/img/autocomplete_indicator.gif' /></span>
</div>