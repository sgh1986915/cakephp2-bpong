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

</SCRIPT>

<h2>Access for the group: <?php echo $group['Group']['name'] ?> </h2>
<?php if(!empty($accesses)):?>

<table  border="0">
<tr>
<!-- Show security objects -->
<td>
	<table  border="0">
	<tr>
		<td height="55">Security objects</td>
	</tr>
	<?php foreach ($accesses as $a): ?>
		<tr>
		 <td height="30"><?php echo $a['objects']['objectName'];?></td>
		 </tr>
	<?php endforeach; ?>
	</table>
</td>
<!-- Show security objects -->

<td style="position: relative;	overflow: auto;	float: left;  width: 560px; " >

<!-- Show statuses -->
	<TABLE >
	<tr>
		<?php foreach ($statuses as $status): ?>
		<td colspan="5" height="30">
			<?php echo $status['Status']['name'] ?>
		</td>
		<?php endforeach; ?>
	</tr>
<!-- EOF showing statuses -->
	<tr>
		<?php foreach ($statuses as $status): ?>
		<td style="background: #ADD8E6; width: 1px; padding-left: 5px;">Create</td>
		<td style="background: #ADD8E6; width: 1px;">Update</td>
		<td style="background: #90EE90; width: 1px;">Read</td>
		<td style="background: #90EE90; width: 1px;">List</td>
		<td style="background: #90EE90; width: 1px;">Delete</td>
		<?php if ($status['Status']['id']!=$group['Group']['defstats_id']):?>
		<td>Default</td>
		<?php endif; ?>
		<?php endforeach; ?>
	</tr>

<?php foreach ($accesses as $a): ?>
<tr>
	<?php ?>
	<?php foreach ($statuses as $status): ?>

	   <?php if ( strlen($a['access'.$status['Status']['id']]['c_'.$status['Status']['id']])==0
				   && strlen($a['access'.$status['Status']['id']]['r_'.$status['Status']['id']])==0
				   && strlen($a['access'.$status['Status']['id']]['u_'.$status['Status']['id']])==0
				   && strlen($a['access'.$status['Status']['id']]['l_'.$status['Status']['id']])==0
				   && strlen($a['access'.$status['Status']['id']]['d_'.$status['Status']['id']])==0
				):
		//If access such as default
			$asDefault = true;
		else:
			$asDefault = false;
		endif; ?>

		<?php if ($asDefault): ?>
		<td style="background: #ADD8E6;" height="30" >--||--</td>
		<td style="background: #ADD8E6;" height="30">--||--</td>
		<td style="background: #ADD8E6;" height="30">--||--</td>
		<td style="background: #90EE90;" height="30">--||--</td>
		<td style="background: #90EE90;" height="30">--||--</td>
		<?php else: ?>
		<td height="30" <?php echo $status['Status']['id']==$group['Group']['defstats_id']?' style="background:#ddd; "':'style="background: #ADD8E6;"'?> >
			<?php echo  $this->Form->select('c_'.$status['Status']['id']."_".$a['objects']['objectId'],$accessLevels,$a['access'.$status['Status']['id']]['c_'.$status['Status']['id']],array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$a['objects']['objectId'].",'c',this.value);",'id'=>'c_'.$status['Status']['id']."_".$a['objects']['objectId'],'title'=>'Create Access for object:'.$a['objects']['objectName']." and status:".$status['Status']['name']),false); ?>
		</td>
		<td  height="30" <?php echo $status['Status']['id']==$group['Group']['defstats_id']?' style="background:#ddd; "':'style="background: #ADD8E6;"'?> >
			<?php echo  $this->Form->select('u_'.$status['Status']['id']."_".$a['objects']['objectId'],$accessLevels,$a['access'.$status['Status']['id']]['u_'.$status['Status']['id']],array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$a['objects']['objectId'].",'u',this.value);",'id'=>'u_'.$status['Status']['id']."_".$a['objects']['objectId'],'title'=>'Update Access for object:'.$a['objects']['objectName']." and status:".$status['Status']['name']),false); ?>
		</td>
		<td height="30" <?php echo $status['Status']['id']==$group['Group']['defstats_id']?' style="background:#ddd; "':'style="background: #90EE90;"'?> >
		    <?php echo  $this->Form->select('r_'.$status['Status']['id']."_".$a['objects']['objectId'],$accessLevels,$a['access'.$status['Status']['id']]['r_'.$status['Status']['id']],array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$a['objects']['objectId'].",'r',this.value);",'id'=>'r_'.$status['Status']['id']."_".$a['objects']['objectId'],'title'=>'Read Access for object:'.$a['objects']['objectName']." and status:".$status['Status']['name']),false); ?>
		</td>
		<td height="30" <?php echo $status['Status']['id']==$group['Group']['defstats_id']?' style="background:#ddd; "':'style="background: #90EE90;"'?> >
		    <?php echo  $this->Form->select('l_'.$status['Status']['id']."_".$a['objects']['objectId'],$accessLevels,$a['access'.$status['Status']['id']]['l_'.$status['Status']['id']],array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$a['objects']['objectId'].",'l',this.value);",'id'=>'l_'.$status['Status']['id']."_".$a['objects']['objectId'],'title'=>'List Access for object:'.$a['objects']['objectName']." and status:".$status['Status']['name']),false); ?>
		</td>
		<td height="30" <?php echo $status['Status']['id']==$group['Group']['defstats_id']?' style="background:#ddd; "':'style="background: #90EE90;"'?> >
		    <?php echo  $this->Form->select('d_'.$status['Status']['id']."_".$a['objects']['objectId'],$accessLevels,$a['access'.$status['Status']['id']]['d_'.$status['Status']['id']],array('onchange'=>"ChangeAccess(".$status['Status']['id'].",".$a['objects']['objectId'].",'d',this.value);",'id'=>'d_'.$status['Status']['id']."_".$a['objects']['objectId'],'title'=>'Delete Access for object:'.$a['objects']['objectName']." and status:".$status['Status']['name']),false); ?>
		</td>
		<?php endif; ?>

		<?php if ($status['Status']['id']!=$group['Group']['defstats_id']):?>
		<td  height="30"><input title="The same as default for object: <?php echo $a['objects']['objectName']?> status: <?php echo  $status['Status']['name']?>" style="width: 1px;" type="checkbox" onclick="SameAsDefault(<?php echo $status['Status']['id'].','.$a['objects']['objectId']; ?>,this.checked);" name="<?php echo 'asdef_'.$status['Status']['id']."_".$a['objects']['objectId'] ?>" id="<?php echo 'asdef_'.$status['Status']['id']."_".$a['objects']['objectId'] ?>" <?php echo $asDefault?"checked='checked'":"" ?>>
		</td>
		<?php endif; ?>
	<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</TABLE>
</td>
</tr>
</table>
<?php endif; ?>