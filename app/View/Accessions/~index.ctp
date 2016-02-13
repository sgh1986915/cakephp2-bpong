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
</SCRIPT>
<?php if(!empty($accesses)):?>
<table  border="0">
<tr>
<td>
	<table  border="0">
	<tr>
		<td height="55">Security objects</td>
	</tr>
	<?php foreach ($accesses as $a): ?>
		<tr>
		 <td height="30"><?php echo $a['objects']['objectName'] ?></td>
		 </tr>
	<?php endforeach; ?>
	</table>
</td>
<td style="position: relative;	overflow: auto;	float: left;  width: 560px; " >

	<TABLE >
	<tr>

		<?php foreach ($groups as $group): ?>
		<td colspan="4" height="30"><a href="/accessions/accessGroup/<?php echo $group['Group']['id'] ?>" ><?php echo $group['Group']['name'] ?></a>
			<br>(<?php echo $group['Status']['name'] ?>)
		</td>
		<?php endforeach; ?>
	</tr>
	<tr>
		<?php foreach ($groups as $group): ?>
		<td style="background: #ADD8E6; width: 1px; padding-left: 5px;">Create</td>
		<td style="background: #ADD8E6; width: 1px;">Update</td>
		<td style="background: #90EE90; width: 1px;">Read</td>
		<td style="background: #90EE90; width: 1px;">List</td>
		<td style="background: #90EE90; width: 1px;">Delete</td>
		<?php endforeach; ?>
	</tr>
	<?php foreach ($accesses as $a): ?>
	<tr>
		<!-- <td><?php echo $a['objects']['objectName'] ?></td> -->
		<?php foreach ($groups as $group): ?>
			<td height="30" style="background: #ADD8E6; width: 1px; padding-left: 5px;">
			   <?php echo  $this->Form->select('c_'.$group['Group']['id'],$accessLevels,$a['access'.$group['Group']['id']]['c_'.$group['Group']['id']],array('onchange'=>"ChangeAccess(".$group['Group']['defstats_id'].','.$a['objects']['objectId'].",'c',this.value);",'id'=>"c_".$group['Group']['id'],'title'=>'Create Access for object:'.$a['objects']['objectName']." and group:".$group['Group']['name']),false); ?>
			</td>
			<td height="30" style="background: #ADD8E6; width: 1px;">
			    <?php echo  $this->Form->select('u_'.$group['Group']['id'],$accessLevels,$a['access'.$group['Group']['id']]['u_'.$group['Group']['id']],array('onchange'=>"ChangeAccess(".$group['Group']['defstats_id'].','.$a['objects']['objectId'].",'u',this.value);",'id'=>"u_".$group['Group']['id'],'title'=>'Update Access for object:'.$a['objects']['objectName']." and group:".$group['Group']['name']),false); ?>
			</td>
			<td height="30" style="background: #90EE90; width: 1px;">
			    <?php echo  $this->Form->select('r_'.$group['Group']['id'],$accessLevels,$a['access'.$group['Group']['id']]['r_'.$group['Group']['id']],array('onchange'=>"ChangeAccess(".$group['Group']['defstats_id'].','.$a['objects']['objectId'].",'r',this.value);",'id'=>"r_".$group['Group']['id'],'title'=>'Read Access for object:'.$a['objects']['objectName']." and group:".$group['Group']['name']),false); ?>
			</td>
			<td height="30" style="background: #90EE90; width: 1px;">
			    <?php echo  $this->Form->select('l_'.$group['Group']['id'],$accessLevels,$a['access'.$group['Group']['id']]['l_'.$group['Group']['id']],array('onchange'=>"ChangeAccess(".$group['Group']['defstats_id'].','.$a['objects']['objectId'].",'l',this.value);",'id'=>"l_".$group['Group']['id'],'title'=>'List Access for object:'.$a['objects']['objectName']." and group:".$group['Group']['name']),false); ?>
			</td>
			<td height="30" style="background: #90EE90; width: 1px;">
			    <?php echo  $this->Form->select('d_'.$group['Group']['id'],$accessLevels,$a['access'.$group['Group']['id']]['d_'.$group['Group']['id']],array('onchange'=>"ChangeAccess(".$group['Group']['defstats_id'].','.$a['objects']['objectId'].",'d',this.value);",'id'=>"d_".$group['Group']['id'],'title'=>'Delete Access for object:'.$a['objects']['objectName']." and group:".$group['Group']['name']),false); ?>
			</td>
		<?php endforeach; ?>
	</tr>
	<?php endforeach; ?>
	</TABLE>
</td>
</tr>
</table>
<?php endif; ?>