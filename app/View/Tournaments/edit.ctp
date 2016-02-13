<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<?php echo $this->element('mce_init', array('name' => 'TournamentDescription,TournamentAgreement,TournamentThankyou')); ?><?php echo $this->Html->css('jquery.tabs'); ?><?php echo $this->Html->script(array('jquery.tabs.min.js')); ?><?php echo $this->Html->css(array('jquery.autocomplete'))
              .$this->Html->script(array('jquery.validate.js','jquery.autocomplete'))
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#tabsmenu').tabs();
	
    $('#PackagesInformation').load("/packages/view/Tournament/<?php echo $this->request->data['Tournament']['id']?>",{cache: false},
    												function(){
    												$('#PackagesInformation').slideDown("slow"); 	
    												tb_init('a.thickbox, area.thickbox, input.thickbox');
    												
    });

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
	$("#TournamentEditForm").validate({
		rules: {
			"data[Tournament][name]": "required",
			"data[Tournament][shortname]": "required",
			"data[Tournament][min_people_team]": {
													range: [1, 20]
			},
			"data[Tournament][max_people_team]": {
													range: [1, 20],
													GreaterThen :"#TournamentMinPeopleTeam"
													
			}
		},
		messages: {
			"data[Tournament][name]": "Please enter  name",
			"data[Tournament][shortname]": "Please enter short name"
		}
	});
	//EOF Validation
    //MANAGER VALIDATION
	$("#Manager").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: Hidemanager,
                    success: showResponse
                });
        },
		rules: {
				"data[Manager][email]": {
				  required: true,
				  email: true
			    }
		},
		messages: {
			"data[Manager][email]": "Please enter a valid email address"
		}
	});
	//EOF MANAGER Validation

	//Start data picker initiation
	//added by Edward
	$('.date-pick').datePicker()
				   .dpSetStartDate('01/01/2007')
				   .click(function(){$(this).attr("value",'')});

});
//EOF ready 
function Hidemanager(){
	 $("#SubmitButton").hide('slow');
      $("#ERROR").hide('slow');
      $("#ManagerInformation").hide(function(){ $('#Loading').show();});
}


function showResponse(responseText)  {

   		 $('#Loading').hide('slow');
 		 $("#SubmitButton").show('slow');

	  if (responseText==""){
	  		$('#ERROR').show('slow');
	  } else {
	  		$('#ManagerInformation').html(responseText);
	  		$('#ManagerInformation').show('slow');
	  }
}
</script>

<h2>Edit Tournament</h2>
<fieldset>
<div id="tabsmenu">
  <ul>
    <li><a href="#fragment-1"><span>General</span></a></li>
    <li><a href="#fragment-2"><span>Signup</span></a></li>
    <li><a href="#fragment-3"><span>Packages</span></a></li>
    <li><a href="#fragment-4"><span>Stats</span></a></li>
    <li><a href="#fragment-5"><span>Manager</span></a></li>
  </ul>
  <div id="fragment-1"> <?php echo $this->Form->create('Tournament', array('name'=>'TournamentEditForm','id'=>'TournamentEditForm','url'=>"/tournaments/edit/".$this->request->data['Tournament']['slug']));?>
    <?php
	        echo $this->Form->hidden('slug');
		    echo $this->Form->hidden('id');
		    echo $this->Form->hidden('URL');
		    echo $this->Form->input('name');
		    echo $this->Form->input('shortname');
	    ?>
    <div class="text_descript"> <?php echo $this->Form->input('description');?></div>
    <?php 
		    echo $this->Form->input('start_date',array('type'=> 'text','class' => 'date-pick dp-applied', 'readonly' => true));
		    echo $this->Form->input('end_date',array('type'=> 'text', 'class' => 'date-pick dp-applied', 'readonly' => true,  'error' => 'End date must be later then start date'));
		    echo $this->Form->input('url');
		?>
    <div class="clear"></div>
    <?php echo $this->Form->end('Submit');?> </div>
  <div id="fragment-2"> <?php echo $this->Form->create('Tournament', array('name'=>'TournamentEditForm','id'=>'TournamentEditForm2','url'=>"/tournaments/edit/".$this->request->data['Tournament']['slug']));?>
    <?php
	        echo $this->Form->hidden('slug');
		    echo $this->Form->hidden('id');
		    echo $this->Form->hidden('URL');
		    echo $this->Form->hidden('start_date');
		    echo $this->Form->hidden('end_date');
		 ?>
    <div class="text_descript"><?php echo $this->Form->input('agreement');?></div>
    <div class="text_descript"><?php echo $this->Form->input('thankyou');?></div>
    <table border="0" cellspacing="0" cellpadding="0" class="boxes">
      <tr>
        <td class="labeltd">Signup required</td>
        <td><?php echo $this->Form->input('signup_required',array('label'=>false));?></td>
      </tr>
      <tr>
        <td class="labeltd">Shown on front</td>
        <td><?php echo $this->Form->input('shown_on_front',array('label'=>false));?></td>
      </tr>
      <tr>
        <td class="labeltd">Multi team</td>
        <td><?php echo $this->Form->checkbox('multi_team', array ('label'=>false) );?></td>
      </tr>
      <tr>
        <td class="labeltd">Min people per team</td>
        <td><?php echo $this->Form->input('min_people_team', array('label'=>false));?></td>
      </tr>
      <tr>
        <td class="labeltd">Max people per team</td>
        <td><?php echo $this->Form->input('max_people_team', array('label'=>false));?></td>
      </tr>
      <tr>
        <td colspan="2"><?php echo $this->Form->input('finish_signup_date',array('type'=> 'text', 'class' => 'date-pick dp-applied', 'readonly' => true));?></td>
      </tr>
      <tr>
        <td class="labeltd">Choosing rooms</td>
        <td><?php echo $this->Form->input('is_room', array ('type'=>'checkbox','label'=>false) );?></td>
      </tr>
    </table>
    <?php echo $this->Form->end('Submit');?> </div>
  <div id="fragment-3">
    <div id="PackagesInformation" style="display: none;" class="details">
      <!-- Please don't remove this DIV it's for AJAX -->
    </div>
  </div>
  <div id="fragment-4">Will be soon </div>
  <!-- Features TAB -->
  <div id="fragment-5"> <?php echo $this->Form->create('Manager',array('id'=>'Manager','name'=>'Manager','url'=>'/managers/findByEmail'));?>
    <h3>Add new Manager</h3>
    <fieldset>
    <?php
        		echo $this->Form->hidden('Manager.model', array('value' => 'Tournament'));
        		echo $this->Form->hidden('Manager.model_id', array('value' => $this->request->data['Tournament']['id']));
        
        		echo $this->Form->input('email');
        		?>
    <div id="ERROR" style="display: none;">Can't find such user.</div>
    <div id="ManagerInformation" style="display: none;"></div>
    </fieldset>
    <div id="SubmitButton"> <?php echo $this->Form->end('Submit');?> </div>
    <div class="heightpad"></div>
    <div id="Loading" style="display:none"> <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?> </div>
    <h4>Current Managers:</h4>
    <table>
      <tr>
        <th>Email</th>
        <th>Login</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Confirmed</th>
      </tr>
      <?php foreach ($managers as $manager): ?>
      <tr>
        <td><?php echo $manager['User']['email'] ?></td>
        <td><?php echo $manager['User']['lgn'] ?></td>
        <td><?php echo $manager['User']['firstname'] ?></td>
        <td><?php echo $manager['User']['lastname'] ?></td>
        <td><?php echo empty($manager['Manager']['is_confirmed'])?"No":"Yes"?></td>
        <?php if($manager['Manager']['user_id'] == $userID && $manager['Manager']['is_owner']==0): ?>
        <td><a  onclick="return confirm(&#039;Are you sure you want to remove?&#039;);"  href="/managers/remove/Tournament/<?php echo $this->request->data['Tournament']['id']."/".$manager['Manager']['user_id'];?>">Remove me</a></td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</fieldset>
<!-- tab -->
