<script>
	function fbs_click(u) {
		t="World Series of Beer Pong Satellite Tournament";
		window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');
		return false;
	}
</script>
<style>
html .fb_share_button { display: -moz-inline-block; display: inline-block; height: 15px; width: 15px; border: 1px solid #d8dfea; background: url(http://static.ak.fbcdn.net/images/share/facebook_share_icon.gif?2:26981) no-repeat; }
</style>
<?php if ($this->Session->check("Tournament")):
                            $_tournament =  $this->Session->read("Tournament");
                            if (!empty($_tournament['name'])) :
                                echo "<h2>".$_tournament['name']." Satellite Tournaments<sup>TM</sup></h2>";  
                            else:?>
<h2>World Series of Beer Pong VI Satellite Tournaments<sup>TM</sup></h2>
<?php endif;                            
            else:?>
<h2>World Series of Beer Pong VI Satellite Tournaments<sup>TM</sup></h2>
<?php endif;?>
<div> </div>
<!-- gmaps -->
<div style="text-align: center">
  <iframe style="border: 1px dotted #ccc; width: 700px; height: 300px; padding: 0px; margin: 0px" vspace="0" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps/ms?hl=en&amp;gl=us&amp;ptab=2&amp;ie=UTF8&amp;oe=UTF8&amp;s=AARTsJrxIak7Lys4V_ahgP3xL4b0Nu7MoA&amp;msa=0&amp;msid=108864696476394828852.000453d7b86c986d8b04c&amp;ll=36.031332,-97.03125&amp;spn=42.156578,87.890625&amp;z=3&amp;output=embed"></iframe>
</div>
<!-- /gmaps -->
<br  />
<!-- filter -->
<?php echo $this->Form->create('',array('id'=>'SatFilter','name'=>'SatFilter','action'=>'index'));?>
<fieldset>
<table border="0" cellspacing="0" cellpadding="0" class="boxes">
  <tr>
    <td class="labeltd"><label for="SatFilterState">State</label></td>
    <td class="select"><select name="data[SatFilterState]"  id="SatFilterState" >
        <?php if ($currstate != ''):?>
        <option value="<?php echo $currstate; ?>"><?php echo $currstate; ?></option>
        <?php endif;?>
        <option value="">All states</option>
        <?php if (!empty($states)):?>
        <?php foreach($states as $state):?>
        <option value="<?php echo $state['provincestates']['shortname']; ?>"><?php echo $state['provincestates']['shortname']; ?></option>
        <?php endforeach;?>
        <?php endif;?>
      </select></td>
  </tr>
  <tr>
    <td class="labeltd"><label for="8">Show inactive events</label></td>
    <td><input type="checkbox" name="data[SatFilterActive]" <?php if ($show == 'showall') { echo 'checked=true'; }?> id="8"/></td>
  </tr>
</table>
</fieldset>
<?php echo $this->Form->end('Filter');?>
<!-- /filter -->
<div class="clear"></div>
<table style="background-color: #efefef">
  <tbody>
    <!-- header begins -->
    <tr>
      <th>City</th>
      <th>State</th>
      <th>Tournament</th>
      <th>Date</th>
      <th>Venue</th>
      <th>Share</th>
    </tr>
    <!-- header ends -->
    <!-- satellites begin -->
    <?php if (!empty($results)):?>
    <?php foreach($results as $result):?>
    <tr>
      <td valign="top" style="text-align: center;" class="style3"><?php echo $result['addresses']['city']; ?></td>
      <td valign="top" style="text-align: center;" class="style3"><?php echo $result['provincestates']['shortname']; ?></td>
      <td valign="top" style="text-align: center;"><a class="style3" href="<?php echo MAIN_SERVER;?>/events/view/<?php echo $result['events']['slug']; ?>"><?php echo $result['events']['name']; ?></a></td>
      <td valign="top" style="text-align: center;" class="style3"><?php echo $this->Time->niceDate($result['events']['start_date']); ?></td>
      <td valign="top" style="text-align: center;"><a class="style3" href="<?php echo MAIN_SERVER;?>/venues/view/<?php echo $result['venues']['slug']; ?>"><?php echo $result['venues']['name']; ?></a>
      <td valign="top" style="text-align: center;"><a href="http://www.facebook.com/share.php?u=<?php echo MAIN_SERVER;?>/events/view/<?php echo $result['events']['slug']; ?>" class="fb_share_button" onclick="return fbs_click('<?php echo MAIN_SERVER;?>/events/view/<?php echo $result['events']['slug']; ?>')" target="_blank" style="text-decoration: none"></a></td>
    </tr>
    <?php endforeach;?>
    <?php else:?>
    <tr>
      <td colspan="4" valign="top" style="text-align: center;" class="style3">Sorry, but there are no any events for current tournament</td>
    </tr>
    <?php endif;?>
    <!-- satellites end -->
  </tbody>
</table>
