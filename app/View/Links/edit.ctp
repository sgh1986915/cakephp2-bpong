<script type="text/javascript">
	function submitContentForm() {
		var someEmpty = 0;
		if (!$('#link_title').val()) {
			someEmpty = 1;
			alert('Please specify Title');
		}	
		if (!$('#link_url').val()) {
			someEmpty = 1;
			alert('Please specify Url');
		}	
		if (someEmpty == 1) {

		} else {
			$('#submitContentForm').submit();
		}			

	}
</script>

<h2>Edit Link</h2>
<?php echo $this->Form->create('Link', array('id'=>'submitContentForm','url'=>'/links/edit/' . $id));?>
  <?php echo $this->Form->input('id');?>
  <label class='image_label'>* Title</label>
  <?php echo $this->Form->input('title', array('id' => 'link_title', 'div' => false, 'type' =>'text', 'label' => false));?>
  <div class="clear"></div>
  <label class='image_label'>* Url</label>
  <?php echo $this->Form->input('url', array('id' => 'link_url', 'div' => false, 'type' =>'text', 'label' => false));?>
  <div class="clear"></div>
  <label class='image_label'>Description</label>
  <?php echo $this->Form->input('description', array('div' => false, 'type' =>'textarea', 'label' => false));?>
  <div class="clear"></div>
  <div class="submit">
    <input type="button" value="Save" class='red_button' onclick='submitContentForm();'>
  </div>
  </form>
  <div class="clear"></div>

