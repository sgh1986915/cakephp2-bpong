<script type="text/javascript">
	function findEvent() {
		var name = $('#venue_name').val();
		var address = $('#venue_address').val();
		if (name || address) {
			$('#obj_list').hide();
			$('#loader').show();
			$.post("/organizations_objects/find_venue/", { "name": name, "address": address, "organization_id": <?php echo $id;?>},
					   function(data) {
						$('#loader').hide();
						$('#obj_list').html(data);
						$('#obj_list').show();						
			});
		}
		return false;
	}
</script>

<h2 class='hr'>Add Venue</h2>
<br/><br/>
<?php echo $this->Form->input('Venue.name', array('style' => 'width:400px;', 'label' => 'Venue Name', 'id' => 'venue_name')); ?>
<?php echo $this->Form->input('Venue.address', array('style' => 'width:400px;', 'label' => 'Venue Address or City', 'id' => 'venue_address')); ?>
<br/><br/>
<div class="submit"><input type="submit" class="submit" value="Find" onclick='return findEvent();'></div>
<br/>
<div id='loader' style='text-align:center;display:none;width:100%;' >
	<img src="<?php echo STATIC_BPONG?>/img/ajax-loader.gif" alt="" border="0">
</div>

<div id='obj_list' style='text-align:center;width:100%;' ></div>

<br/><br/><br/><br/>