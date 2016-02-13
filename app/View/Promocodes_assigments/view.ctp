<h3>Promocode currently assigned to:</h3>
<?php if (!empty($assigments)):?>
<table id="address" >
  <?php foreach ($assigments as $assigment):?>
  <tr id="<?php echo "assigment_".$assigment['PromocodesAssigment']['id'] ?>">
    <td style="background-color:#dfebfb; min-width:1px"><?php if ($assigment['PromocodesAssigment']['model']=="All"):?>
      All Tournaments and Events
      <?php else:?>
      <?php if ($assigment['PromocodesAssigment']['model_id']==-1):?>
      All <?php echo $assigment['PromocodesAssigment']['model'];?>s
      <?php else:?>
      "<?php echo $assigment[$assigment['PromocodesAssigment']['model']]['name'];?>" <?php echo $assigment['PromocodesAssigment']['model'];?>
      <?php endif;?>
      <?php endif;?>
    </td>
    <td style="background-color:#dfebfb; min-width:1px"><a href="javascript: DeleteAssigment(<?php echo $assigment['PromocodesAssigment']['id']; ?>);" >Delete</a> </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif;?>
<span class="addbtn"><a href="/promocodes_assigments/add/<?php echo $promocodeID;?>?inlineId=NewAssigment&amp;height=500&amp;width=450&amp;modal=true;" class="thickbox addbtn" id="NewAssigment" title="New Address" >New Assigment</a></span> 