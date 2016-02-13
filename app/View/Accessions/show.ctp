<SCRIPT type=text/javascript>
function ChangeAccess(status_id,obj_id,access_type,access) {

				$.post("/accessions/changeAccess"
               ,{
                     obj_id      : obj_id
                    ,status_id   : status_id
                    ,access_type : access_type
                    ,access      : access
                }
               ,function(response){
                   //setTimeout("saveAjax('"+escape(response)+"')", 400);
                });

}

function SameAsDefault(status_id,obj_id,as_default) {
				
				$.post("/accessions/sameAsDefault"
               ,{
                     obj_id      : obj_id
                    ,status_id   : status_id
                    ,as_default : as_default
                }
               ,function(response){
                   setTimeout("location.reload(true);", 400);
                });
}

function ChangeCategory (obj_id,category_id) {

	$.post("/accessions/changeCategory"
            ,{
                  obj_id      : obj_id
                 ,category_id : category_id
             }
            ,function(response){
                setTimeout("location.reload(true);", 400);
             });
	
}

$(document).ready(function(){
	$('.top_scroll div').width(parseInt($('.scroll_box table').width()));
	
	$('.top_scroll').scroll(function(){
		$('.scroll_box').scrollLeft($('.top_scroll').scrollLeft());
	});
	$('.scroll_box').scroll(function(){ 
		$('.top_scroll').scrollLeft($('.scroll_box').scrollLeft());
	});
});
</SCRIPT>
<h2>Access</h2>
<div class='no_scroll'>
<table cellspacing="0" cellpadding="0" border="0">
	<tr>
	<th height="30" colspan="3">Security objects</th>	
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #909090;">Group</td>
		<td style="border-bottom: 1px solid #909090;">Status</td>
		<td style="border-bottom: 1px solid #909090;">Access&nbsp;type</td>
	</tr>
	<?php $prevStatusID = 0;?>
	<?php $prevGroupID  = $statuses[0]['Group']['id']?>
		
	<?php foreach ($statuses as $status):?>
		<?php $i = 0;?>
		<?php foreach ($accessTypes as $accesType=>$accessTypeName):?>
			
		<?php $isShowLine = $prevStatusID != $status['Status']['id']?true:false;?>
		<?php $isShowLineGroup = $prevGroupID != $status['Group']['id']?true:false;?>
		<?php $i++;?>
		
		<tr <?php if ($isShowLine) echo "style='border-top: 5px solid #909090;'"?>>
        		<td width="200"  style="<?php if ($isShowLineGroup) echo "border-top: 0px solid #909090;"?> ">
        			<?php echo $status['Group']['name']?>
        		</td>
        		<?php if ($isShowLine):?>
    				<td width="80" rowspan="5"  style="vertical-align:middle; <?php if ($status['Status']['id'] == $status['Group']['defstats_id']) echo 'color: #FF3333;';?> <?php if ($isShowLine && $prevStatusID) echo "border-top: 2px solid #909090;"?>">
    			        <?php echo $status['Status']['name']?>
    				</td>
    			<?php endif;//$isShowLine?>
    			
    			<td width="1" <?php if ($isShowLine && $prevStatusID) echo "style='border-top: 2px solid #909090;'"?>>
    		        <?php echo $accessTypeName?>
    			</td>
        	</tr>	  
            <?php $prevStatusID = $status['Status']['id'];?>
		    <?php $prevGroupID  = $status['Group']['defstats_id'];?>		
		<?php endforeach;//$accessTypes?>        	    
<?php endforeach;//$objects ?>		
	
	
</table>
</div>
<!-- ================== -->
<div class='top_scroll'><div>&nbsp;</div></div>
<div class='scroll_box'>
<table cellspacing="0" cellpadding="0" border="0">
	<!-- Header for objects -->
	<tr>		
	<?php foreach ($objects as $obj): ?>
		 <th height="30"><?php echo $obj['Obj']['name'];?></th>
	<?php endforeach; ?>
	</tr>
	
	<!-- Header for the group/ statuses -->
	<tr>
		<?php foreach ($objects as $obj): ?>
		 <td style="border-bottom: 1px solid #909090;">
		 <?php echo  $this->Form->select($obj['Obj']['id'],
        		 			                          $categories,
        		 			                          $obj['Obj']['category_id'],
        		 			                          array('onchange'=>"ChangeCategory(".$obj['Obj']['id'].",this.value);",
        		 			                          		'id'=>$obj['Obj']['id'].'_category'
        		 			                          ),false); ?>&nbsp;
		 </td>
	    <?php endforeach; ?>
	</tr>
	
	<?php $prevStatusID = 0;?>
	<?php $prevGroupID  = $statuses[0]['Group']['id']?>
		
	<?php foreach ($statuses as $status):?>
		<?php $i = 0;?>
		<?php foreach ($accessTypes as $accesType=>$accessTypeName):?>
			
		<?php $isShowLine = $prevStatusID != $status['Status']['id']?true:false;?>
		<?php $isShowLineGroup = $prevGroupID != $status['Group']['id']?true:false;?>
		<?php $i++;?>
		
		<tr <?php if ($isShowLine) echo "style='border-top: 5px solid #909090;'"?>>
    		<!-- 
        		<td width="100"  style="<?php if ($isShowLineGroup) echo "border-top: 2px solid #909090;"?> ">
        			<?php echo $status['Group']['name']?>
        		</td>
        		<?php if ($isShowLine):?>
    				<td width="80" rowspan="5"  style="vertical-align:middle; <?php if ($status['Status']['id'] == $status['Group']['defstats_id']) echo 'color: #FF3333;';?> <?php if ($isShowLine && $prevStatusID) echo "border-top: 2px solid #909090;"?>">
    			        <?php echo $status['Status']['name']?>
    				</td>
    			<?php endif;//$isShowLine?>
    			
    			<td width="1" <?php if ($isShowLine && $prevStatusID) echo "style='border-top: 2px solid #909090;'"?>>
    		        <?php echo $accessTypeName?>
    			</td>
			 -->	
				<?php //pr($permissions)?>
        		<?php foreach ($objects as $obj): ?>
        		
        			<?php $asDefault = false;?>
        		   
        		    <td width="1"  <?php if ($isShowLine && $prevStatusID) echo "style='border-top: 2px solid #909090;'"?> >
        		 	
        		     <?php if (isset($permissions[$status['Status']['id']][$obj['Obj']['id']][$accesType])):?>
        		     
        		 			<?php echo  $this->Form->select($accesType.'_'.$status['Status']['id']."_".$obj['Obj']['id'],
        		 			                          $accessLevels,
        		 			                          $permissions[$status['Status']['id']][$obj['Obj']['id']][$accesType],
        		 			                          array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$obj['Obj']['id'].",'$accesType',this.value);",
        		 			                          		'id'=>$accesType.'_'.$status['Status']['id']."_".$obj['Obj']['id']
        		 			                          ),false); ?> &nbsp;
        		 	<?php else:?>
        		 		    <?php $asDefault = true;?>	
        		 			- || -
        		 	<?php endif;?>
        		 	<?php if ($i==5 && $status['Status']['id'] != $status['Group']['defstats_id']):?>
						<input title="The same as default"  type="checkbox" onclick="SameAsDefault(<?php echo $status['Status']['id'].','.$obj['Obj']['id']; ?>,this.checked);" name="<?php echo 'asdef_'.$status['Status']['id']."_".$obj['Obj']['id'] ?>" id="<?php echo 'asdef_'.$status['Status']['id']."_".$obj['Obj']['id'] ?>" <?php echo $asDefault?"checked='checked'":"" ?>>
				    <?php endif;//?>
        		 	
        		 </td>
        	    <?php endforeach;//$objects ?>        	      	    
        	            	    
        	</tr>	  
            <?php $prevStatusID = $status['Status']['id'];?>
		    <?php $prevGroupID  = $status['Group']['defstats_id'];?>
			  
		<?php endforeach;//$accessTypes?>        	    
		
	<?php endforeach;//$statuses?>
</table>
</div>