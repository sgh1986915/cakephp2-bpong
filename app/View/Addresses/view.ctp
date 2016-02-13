  		<?php if ($label):
  			$URL =  $modelName.'/'.$modelID.'/'.$ownerID.'/'.$label ;
  		 else:
  		    $URL =  $modelName.'/'.$modelID.'/'.$ownerID ;
  		 endif; ?>

<table id="address" >
    <?php foreach ($this->request->data as $address):?>
    	<tr id="<?php echo "addr_".$address['Address']['id'] ?>">
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo empty($address['Country']['name'])?"":$address['Country']['name'] ?></td>
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo $address['Address']['address'] ?></td>
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo $address['Address']['city'] ?></td>
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo empty($address['Provincestate']['name'] )?"":$address['Provincestate']['name'] ?></td>
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo $address['Address']['postalcode'] ?></td>
    		<?php if (!$label): ?>
    		<td style="background-color:#dfebfb; min-width:1px"><?php echo $address['Address']['label'] ?></td>
    		<?php endif; ?>
    		<td width="80px">
    			<?php if ($label): ?>
    				<a href="/addresses/edit/<?php echo $modelName."/".$modelID."/".$ownerID."/".$address['Address']['id']."/".$label;?>?&inlineId=EditAddress&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditAddress" title="Edit Address" >Edit</a>
    			<?php else: ?>
    				<a href="/addresses/edit/<?php echo $modelName."/".$modelID."/".$ownerID."/".$address['Address']['id'];?>?&inlineId=EditAddress&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditAddress" title="Edit Address" >Edit</a>
    			<?php endif; ?>

    			<?php if (!$label && ($address['Address']['label']!="Home"  || $homeCount>1)): ?>
    			<br/><a href="javascript: DeleteAddress(<?php echo $address['Address']['id']; ?>);" >Delete</a>
    			<?php endif; ?>
    		</td>
    	</tr>
    <?php endforeach; ?>
    </table>

<a href="/addresses/add/<?php echo $URL?>?&inlineId=NewAddress&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="NewAddress" title="New Address" >New Address</a>
