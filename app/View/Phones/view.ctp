<?php echo $this->Form->hidden('Phone.cnt');?>
<?php if(!empty($this->request->data)): ?>
<table id="address" style="width:500px">
    <?php foreach ($this->request->data as $phone):?>
    	<?php if (!empty($phone['Phone']['phone'])):?>
    	<tr id="<?php echo "phone_".$phone['Phone']['id'] ?>">
    		<td style="background-color:#dfebfb"><?php echo $phone['Phone']['type'] ?></td>
    		<td style="background-color:#dfebfb"><?php echo $phone['Phone']['phone'] ?></td>
    		<td>	<a href="/phones/edit/<?php echo $modelName."/".$modelID."/".$ownerID."/".$phone['Phone']['id'];?>?&inlineId=EditPhone&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditPhone" title="Edit Phone" >Edit</a>
    			&nbsp;

    			<a href="javascript: DeletePhone(<?php echo $phone['Phone']['id']; ?>);" >Delete</a>
			</td>
    	</tr>
    	<?php endif;?>
    <?php endforeach; ?>
    </table>
<?php endif; ?><br style="clear: both;">
<a href="/phones/add/<?php echo $modelName."/".$modelID."/".$ownerID;?>?&inlineId=NewPhone&amp;width=400&amp;modal=true;" class="thickbox" id="NewPhone" title="New Phone" >New Phone</a>