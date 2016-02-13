<script type="text/javascript">
$(document).ready(function() {

    $.validator.addMethod("slug", function(value, element) {
        return this.optional(element) || /^[a-z0-9\-\_]+$/i.test(value);
    }, "Only letters, numbers and underscore.");


    //MANAGER VALIDATION
	$("#organizationForm").validate({
		rules: {
				"data[Organization][name]": {
				  required: true
			    },
				"data[Organization][slug]": {
					  required: true,
					  slug: true
				}

		},
		messages: {
			"data[Organization][name]": "Please specify Name",
			"data[Organization][slug]": {
				required: "Please specify this field.",
				slug: "Only letters, numbers and underscore."
			}
		}
	});


//Country click
$("#address_country").change(function(){
	  $("#address_state").html('<option>Loading...</option>');
	  $.ajaxSetup({cache:false});
	  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
			$("#address_state").html(options);
			$('#address_state option:first').attr('selected', 'selected');
		})

	});
});
</script>
<?php echo $this->element('mce_init', array('name' => 'data[Organization][about]')); ?>


<?php echo $this->Form->create('Organization', array('type' => 'file', 'id' => 'organizationForm'));?>
<?php echo $this->Form->hidden("id");?>
<?php echo $this->Form->input("name");?>

<div class="input text" style='float:left;'><?php echo $this->Form->input("slug", array('style' => 'margin-top:10px;', 'div' => false, 'label' => 'BPONG.COM URL for this page'));?></div>
<div style='font-size: 15px;line-height: 23px;float:left;margin-left:15px;'>I.e., if you want this page to be located <br/> at www.bpong.com/o/jacks-league, type jacks-league.</div>
<div class='clear'></div>

<?php echo $this->Form->input("web_address");?>
<?php if (!empty($organization['Image']['id'])):?>
   <div style='margin-left:150px;padding:10px;'>
		<?php echo $this->Html->image(IMG_MODELS_URL . '/thumbs_' . $organization['Image']['filename'], array( 'border' => '0' )); ?>
   </div>
<?php endif;?>
<?php if (!empty($organization['Image']['id'])):?>
   	<?php echo $this->Form->input( 'Image.' . $organization['Image']['id'], array('type' 	=> 'file', 'class'	=> 'file', 'label'	=> 'Image') );?>
	<?php echo $this->Form->hidden('Image.' . $organization['Image']['id'] . '.prop', array('value' => 'Personal'));?>
<?php else:?>
	<?php echo $this->Form->input( 'Image.new', array('type' => 'file', 'class' => 'file', 'label' => 'Image'));?>
	<?php echo $this->Form->hidden('Image.new.prop', array('value' => 'Personal'));?>
<?php endif;?>

<?php echo $this->Form->input("about", array('label' => 'About Organization'));?>
<br/>

<?php
		if (!empty($organization['Address']['id'])) {
			echo $this->Form->hidden('Address.id');
		}
		echo $this->Form->input('Address.country_id', array('id' => 'address_country'));
		echo $this->Form->input('Address.provincestate_id', array('label' => 'State', 'id' => 'address_state', 'selected' => $this->request->data['Address']['provincestate_id'], 'options' => $provincestates));
		echo $this->Form->input('Address.city');
		echo $this->Form->input('Address.address');
		echo $this->Form->input('Address.postalcode', array('label' => 'Postal Code'));
?>



<div class="submit">
    <input type="submit" value="Submit" />
</div>

<?php echo $this->Form->end(); ?>