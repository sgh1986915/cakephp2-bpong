<script type="text/javascript">
	function findEvent() {
		var name = $('#event_name').val();
		if (name) {
			$('#obj_list').hide();
			$('#loader').show();
			$.post("/organizations_objects/find_event/", { "name": name, "organization_id": <?php echo $id;?>},
					   function(data) {
						$('#loader').hide();
						$('#obj_list').html(data);
						$('#obj_list').show();						
			});
		}
		return false;
	}
</script>

<h2 class='hr'>Add Event</h2>
<br/><br/>
<?php echo $this->Form->input('Event.name', array('style' => 'width:400px;', 'label' => 'Event Name', 'id' => 'event_name')); ?>
<br/><br/>
<div class="submit"><input type="submit" class="submit" value="Find" onclick='return findEvent();'></div>
<br/>
<div id='loader' style='text-align:center;display:none;width:100%;' >
	<img src="<?php echo STATIC_BPONG?>/img/ajax-loader.gif" alt="" border="0">
</div>

<div id='obj_list' style='text-align:center;width:100%;' ></div>

<br/><br/><br/><br/>