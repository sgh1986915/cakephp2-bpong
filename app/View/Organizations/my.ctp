<h2>My Organizations</h2>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'Organization')); ?>
<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><strong>Image</strong></th>
      <th><?php echo $this->Paginator->sort('Name', 'organization.name', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('City', 'address.city', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('State', 'address.provincestate_id', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Number of Users', 'organization.count_users', array('sorter' => true));?></th>
      <?php /*?>
      <th><?php echo $this->Paginator->sort('Number of Teams', 'organization.count_teams', array('sorter' => true));?></th>
      <?php */ ?>
    </tr>
    <?php
$i = 0;
if (!empty($organizations)):
foreach ($organizations as $organization):
  $class = null;
  if ($i++ % 2 != 0) {
    $class = ' class="gray"';
  }
?>
    <tr<?php echo $class;?>>
    	<td style='padding:3px 0px;'>
    		<?php if (!empty($organization['Image']['filename'])):?>
	    		<a href="/o/<?php echo $organization['Organization']['slug'];?>">
	    			<img src="<?php echo IMG_MODELS_URL;?>/thumbs_<?php echo $organization['Image']['filename'];?>" alt="<?php echo $organization['Organization']['name'];?>" border="0" />
	    		</a>
    		<?php endif;?>
    	</td>
    	<td><a href="/o/<?php echo $organization['Organization']['slug'];?>"><?php echo $organization['Organization']['name'];?></a></td>
    	<td><?php if (!empty($organization['Address']['city'])):?><?php echo $organization['Address']['city'];?><?php endif;?></td>
    	<td><?php if (!empty($organization['Address']['Provincestate']['name'])):?><?php echo $organization['Address']['Provincestate']['name'];?><?php endif;?></td>
    	<td><?php echo intval($organization['Organization']['count_users']);?></td>
    	<?php /*?>
    	<td><?php echo intval($organization['Organization']['count_teams']);?></td>
    	<?php */ ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php else:?>
</table>
<div style='font-size:16px; text-align:center;margin:10px;'>There are no Organizations</div>
<?php endif;?>
<?php echo $this->element('simple_paging');?>
<span class="addbtn"><a class="addbtn" href="/organizations/add">Add Organization</a></span>