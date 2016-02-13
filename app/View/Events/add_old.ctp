<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>


<?php echo $this->element('mce_init', array('name' => 'EventDescription,EventAgreement,EventThankyou')); ?>
<script type="text/javascript">
	$(document).ready(function() {

		//Add new methode - Max people per team should be greater then min people per team
		jQuery.validator.addMethod("GreaterThen", function(value, element,params) {

					if (value != "" && $(params).val() != "") {
						if (parseInt(value) < parseInt($(params).val())) {
							return false;
						} else {
							return true;
						}

					} else {
						return true;
					}

		}, "Max people per team should be greater than Min people.");

		// validate signup form on keyup and submit
		$("#EventAddForm").validate({
			rules: {
				"data[Event][name]": "required",
				"data[Event][start_date_]": "required",
				"data[Event][min_people_team]": {
													range: [1, 20]
			},
			"data[Event][max_people_team]": {
													range: [1, 20],
													GreaterThen :"#EventMinPeopleTeam"
			}

			},
			messages: {
				"data[Event][name]": "Please enter event name"

			}
		});
		//EOF validation


		$('.date-pick').datePicker().dpSetStartDate('01/01/2007').click(function(){$(this).attr("value",'')});


		$('#EventSignupRequired').click(function(){
			if ($('#SignDate').attr('class') == 'upper') {
				$('#SignDate').slideDown();
				$('#SignDate').removeClass('upper');
				$('#SignDate').addClass('bottom');
			} else {
				$('#SignDate').slideUp();
				$('#SignDate').removeClass('bottom');
				$('#SignDate').addClass('upper');
			}
		})
	});
	//EOF ready
</script>


<div class="events form p10">
	<?php echo $this->Form->create('Event',array('enctype'=>"multipart/form-data")); ?>
		<fieldset>
			<legend>Add Event</legend>
			<?php
				echo $this->Form->input('name', array('size' => 70));
				echo $this->Form->input('description', array('label' => false));
			?>
			<div class="text_descript"><?php echo $this->Form->input('agreement');?></div>
			<div class="text_descript"><?php echo $this->Form->input('thankyou');?></div>
			<?php
				echo $this->Form->input('start_date_', array('type' => 'text', 'size' => 10 ,'class' => 'date-pick dp-applied', 'readonly' => true));
				echo $this->Form->input('start_time', array('type' => 'time', 'selected' => '01:00:00'));
				echo $this->Form->input('end_date_', array('type' => 'text', 'size' => 10, 'class' => 'date-pick dp-applied', 'readonly' => true));
				echo $this->Form->input('end_time', array('type' => 'time', 'selected' => '01:00:00'));
			?>
			<div class="box_check">
				<label>Signup Required</label>
				<div class="check_"><?php echo $this->Form->input('signup_required', array('label' => false)); ?></div>
			</div>
		   	  <div class="box_check">
			  <label>Multi team</label><div class="check_"><?php echo $this->Form->checkbox('multi_team', array ('label'=>false) );?></div>
        	</div>
		<?php echo $this->Form->input('min_people_team', array('size' => 10,'label'=>'Min people per team'));?>
		<?php echo $this->Form->input('max_people_team', array('size' => 10,'label'=>'Max people per team'));?>

			<div id="SignDate" class="upper" style="display: none; clear: both">
			<?php
				echo $this->Form->input('finish_signup_date',array('type' => 'text', 'size' => 10, 'class' => 'date-pick dp-applied', 'readonly' => true));
			?>
				<div class="box_check">
					<label>Approve Required</label>
					<div class="check_"><?php echo $this->Form->input('approve_required', array('label' => false)); ?></div>
				</div>
			</div>
			<?php
				echo $this->Form->input('Tournament.tournament');
			?>
			<div class="box_check">
				<label>Is sattelite</label>
				<div class="check_" style=" margin-top: 0">
					<?php echo $this->Form->checkbox('EventTournament.is_satellite', array('label' => false)); ?>
				</div>
			</div>
			<?php
				echo $this->Form->input('url', array('size' => 30, 'value' => 'http://'));
			?>
			<div style="width:150px">
        		<label style="float:left">Choosing rooms</label><div style="width:17px; float:right; padding:0px; margin:-8px 0"><?php echo $this->Form->input('is_room', array ('type'=>'checkbox','label'=>false) );?></div>
            </div>

			<div class="textarea"><label>Official image</label></div>
			<?php
				echo $this->Form->input('Image.new',array('type' => 'file', 'class' => 'file', 'label' => 'Image'));
				echo $this->Form->hidden('Image.new.prop',array('value' => 'Personal'));
			?>
		</fieldset>
		<div class="left35">
			<?php //echo $this->Form->input('Eventfeature', array('multiple' => 'checkbox')); ?>
			<div class="textarea"><label>Features</label></div>
			<?php
				$checked_array = array();
				if (!empty($this->request->data['Eventfeature'])) {
					foreach ($this->request->data['Eventfeature'] as $checkedvalue) {
						$checked_array = $checkedvalue;
					}
				}
			?>
			<div class="p10" style="padding-left: 100px">
				<?php foreach ($eventfeatures as $id => $value): ?>
					<div class="box" style="width: 250px">
						<label style="background: none; width: 200px" for="EventfeatureEventfeature<?php echo $id; ?>"><?php echo $value; ?></label>
						<div class="check">
							<?php echo $this->Form->checkbox('Eventfeature.id.['.$id.']', array(
								'value' => $id,
								'name' => 'data[Eventfeature][Eventfeature][]',
								'checked' => (in_array($id, $checked_array) ? 'checked' : false),
								'id' => 'EventfeatureEventfeature'.$id,
								'label' => false
							)); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php echo $this->Form->end('Submit'); ?>
</div>
<?php if ($buttonlist): ?>
<div class="actions">
	<ul>
		<li><span class="ie6_btn"><?php echo $this->Html->link('List Events', array('action'=>'index'), array('class'=>'addbtn4')); ?></span></li>
	</ul>
</div>
<?php endif; ?>