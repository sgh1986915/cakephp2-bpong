<?php foreach ($questions as $question): ?>
			<?php echo $question['Question']['question'];?><br />
			<?php if ($question['Question']['type']=="Radio"):?>
						<!-- Radio Buttons -->

						<div class="input text">
						<?php foreach ($question['Option'] as $option):
							if (!isset($this->request->data['Question']['Option'.$question['Question']['id']])) {
					  			$this->request->data['Question']['Option'.$question['Question']['id']] = array();
					  		}

						?>
							<input class="<?php echo "Question".$question['Question']['id']?>" name="data[Question][Option<?php echo $question['Question']['id']?>][]" id="OptionId<?php echo $option['id']?>" value="<?php echo $option['id']?>" <?php echo (isset($this->request->data['Question']) && in_array($option['id'],$this->request->data['Question']['Option'.$question['Question']['id']]))?" checked='checked' ":""?> type="radio"/>
							<?php echo $option['optiontext']?><br />
						<?php endforeach;?>
						<label class="error" style="display:none;" id="<?php echo "QuestionError".$question['Question']['id']?>">This question is required.</label>
						</div>

						<!-- EOF Radio Buttons -->

			<?php elseif ($question['Question']['type']=="Checkbox"):?>
					  <!-- Checkbox -->
					  <div class="input text">
					  <?php foreach ($question['Option'] as $option):
					  	if (!isset($this->request->data['Question']['Option'.$question['Question']['id']])) {
					  		$this->request->data['Question']['Option'.$question['Question']['id']] = array();
					  	}
					  ?>
							<input class="<?php echo "Question".$question['Question']['id']?>" name="data[Question][Option<?php echo $question['Question']['id']?>][]" id="OptionId<?php echo $option['id']?>" value="<?php echo $option['id']?>" <?php echo in_array($option['id'],$this->request->data['Question']['Option'.$question['Question']['id']])?" checked='checked' ":""?> type="checkbox"/>
							<?php echo $option['optiontext']?><br />
						<?php endforeach;?>
						<label class="error" style="display:none;" generated="true" for="OptionId<?php echo $option['id']?>" id="<?php echo "QuestionError".$question['Question']['id']?>">This question is required.</label>
					   </div>
						<!--EOF Checkbox -->
			<?php else:?>
				<!-- Select BOX-->
				<?php $Inputs = array();?>
				<div class="input text" style="width:200px">
<div style="float:left; width:200px; margin-bottom:0px">
				<select class="<?php echo "Question".$question['Question']['id']?>" name="data[Question][Option<?php echo $question['Question']['id']?>][]" id="OptionSelectId<?php echo $question['Question']['id']?>">
				<option value="">choose</option>
						<?php foreach ($question['Option'] as $option):?>
							<option <?php echo in_array($option['id'],$this->request->data['Question']['Option'.$question['Question']['id']])?" selected='selected' ":""?> value="<?php echo $option['id']?>"><?php echo $option['optiontext']?></option>
							<?php //if current option must have input - add option to the array with inputs?>
							<?php if ($option['type']=="Input") $Inputs[]=$option;?>
						<?php endforeach;?>
				</select>
</div>
				<div style="float:left; margin:0px; width:200px"><label class="error" generated="true" style="display:none;" for="OptionSelectId<?php echo $option['id']?>" id="<?php echo "QuestionError".$question['Question']['id']?>">This question is required.</label></div>
				</div>

				<?php if (!empty($Inputs)): //working with inputs?>

				<script type="text/javascript">
				$(document).ready(function() {
						$("#OptionSelectId<?php echo $question['Question']['id']?>").change(function(){
							$("div.Input_<?php echo $question['Question']['id']?>").hide();
							//if exist such input then show
							if ( $('#input_'+$(this).val()).length ){
								 $('#input_'+$(this).val()).show();
							}

						});
				});
				</script>
					<?php foreach ($Inputs as $Input):

						if (!isset($this->request->data['Question']['Option'.$question['Question']['id']])) {
					  		$this->request->data['Question']['Option'.$question['Question']['id']] = array();
					  	}
					?>
						<div id='input_<?php echo $Input['id']?>' class="Input_<?php echo $question['Question']['id']?>" <?php echo (isset($this->request->data['Question']) && in_array($option['id'],$this->request->data['Question']['Option'.$question['Question']['id']]))?"":" style='display:none;'"?>>
       					  <?php echo $this->Form->input('Question.input_'.$Input['id'],array('label'=>false));?>
       					 </div>
					<?php endforeach;?>

				<?php endif;//working with inputs?>
			<?php endif;?>
<?php endforeach;?>
