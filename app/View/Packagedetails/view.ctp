<?php if(!empty($this->request->data)): ?>
<table id="address" style="width:500px">
    <?php foreach ($this->request->data as $package):?>
    	<tr id="<?php echo "package_".$package['Package']['id'] ?>">
    		<td><?php echo $package['Package']['name'] ?></td>
    		<td>	<a href="/packages/edit/<?php echo $modelName."/".$modelID."/".$package['Package']['id'];?>?&inlineId=EditPackage&amp;height=500&amp;width=450&amp;modal=true;" class="thickbox" id="EditPackage" title="Edit Package" >Edit</a>
    			&nbsp;

    			<a href="javascript: DeletePackage(<?php echo $package['Package']['id']; ?>);" >Delete</a>
			</td>
    	</tr>
    <?php endforeach; ?>
    </table>
<?php endif; ?>
<a href="/packages/add/<?php echo $modelName."/".$modelID;?>?&inlineId=NewPackage&amp;height=500&amp;width=450&amp;modal=true;" class="thickbox" id="NewPackage" title="New Package" >New Package</a>
