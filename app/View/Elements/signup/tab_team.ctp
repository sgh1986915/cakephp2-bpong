<script type="text/javascript">
$(document).ready(function() {
	<?php if (!empty($new_created_team_id)) { ?>
	$('#team_tab_content').html(showLoaderHtml());
		$.post('<?php echo "/teams/ajax_assign_from_signup/". $signupDetails['Signup']['model'] . '/' . $signupDetails['Signup']['model_id'] . '/'. $new_created_team_id . '/' . $signupDetails['Signup']['id'];?>', function(data) {
				$('#team_tab_content').html(data);
		});		
		
	<?php }?>
    // validate signup form on keyup and submit
            $("#assign_form").validate({
        		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                	beforeSubmit: beforeSubmitAssign,
                    success: showResponseAssign
                });
        }
        });
});

function showResponseAssign(responseText)  {
	$('#team_tab_content').html(responseText);	  
}

function beforeSubmitAssign(){
	if ($('#TournEventTeamId').val() < 1) {
		$('#team_select_error').show();
		return false;
	} else {
		$('#team_select_error').hide();
		$('#team_tab_content').html(showLoaderHtml());
		return true;
	}
}


function loadTeamForm() {
	$('#team_tab_content').html(showLoaderHtml());
	$('#team_tab_content').load('/teams/create_and_assign/Event/<?php echo $signupDetails['Signup']['model_id'];?>/<?php echo $signupDetails['Signup']['id'];?>/<?php echo $signupDetails[$signupDetails['Signup']['model']]['people_team'];?>', function() {
		  
	});
	return false;
}
function ajaxAssignTeammate(teamID, userLogin) {
	$('#team_tab_content').html(showLoaderHtml());
	$.post('/teammates/assign/' + teamID +'/' + userLogin + '/1/<?php echo $signupDetails['Signup']['id'];?>', 
        function(data) {
            if (data.hasOwnProperty("duplicate")) {
                var teamidtouse = data["duplicate"]["mergedid"];
                var teamnametouse = data["duplicate"]["deletedteamname"];
                $.post('<?php echo "/teams/ajax_assign_from_signup/". $signupDetails['Signup']['model'] . '/' . $signupDetails['Signup']['model_id']; ?>/' + data["duplicate"]["mergedid"] +'/<?php echo $signupDetails['Signup']['id'];?>/' + 
                    teamnametouse, function(data) {
                    $('#team_tab_content').html(data);
                });
            }                                            
            else {
               $.post('<?php echo "/teams/ajax_assign_from_signup/". $signupDetails['Signup']['model'] . '/' . $signupDetails['Signup']['model_id']; ?>/' + teamID +'/<?php echo $signupDetails['Signup']['id'];?>', function(data) {
                    $('#team_tab_content').html(data);});
            }
        });
	return false;
	
}
function selectTeam() {
	$('#team_select_error').hide();
    if ($('#TournEventTeamId').val() > 0) {
        $('#Loading').show();
        $('#TeammatesForSelectedTeam').hide('slow');
        $('#alternateNameBlock').show();  
                                                                             
        $.post('/teammates/ajax_get_teammates'+'/'+$('#TournEventTeamId').val(),{},function(responseText) {
             $('#Loading').hide('slow');
              
             $('#TeammatesForSelectedTeam').html(responseText);
             $('#TeammatesForSelectedTeam').show('slow');
            });
        return false;
    }
    else {
        $('#TeammatesForSelectedTeam').hide();
        $('#alternateNameBlock').hide();
    }
}
</script>
<div id='team_tab_content'>
<?php if ($isteamAssigned):?>
	<h3 class="new">The Team "<?php echo $this->Html->link($team[0]['Teamsobject']['name'], array('controller' => 'teams', 'action'=>'view', $team[0]['Team']['slug'], $team[0]['Team']['id']), array('escape'=>false)); ?>" is now registered to play in <?php echo $signupDetails[$signupDetails['Signup']['model']]['name']; ?></h3>
	<?php if (!empty($teammates)):?>
	<strong>Teammates:</strong>
		<?php foreach ($teammates as $teammate):?>
			<a href="/u/<?php echo $teammate['User']['lgn']?>"><?php echo $teammate['User']['lgn']?></a>;&nbsp; 
		<?php endforeach;?>
	<?php endif;?>
	<br />
	<br />
	<a href="/teams/remove_from_signup/<?php echo $signupDetails['Signup']['id'];?>/<?php echo $team[0]['Team']['id'];?>">I would like to use a different team. Please remove this team from the event.</a>
<?php if (!$teamIsCompleted):?>
	<?php if (!empty($teamInfoForSignup['waiting_for_signup'])):?>
		<br/>
		<p class="you_have_no2"><br/>Some of the members of this team have not yet signed up for this event. 
		Your registration will not be complete until your teammate(s) all complete their registrations!<br/><br/></p>
	<?php endif;?>
	<?php if (!empty($teamInfoForSignup['waiting_for_accept'])):?>
		<?php if ($userIsPendingOnTeam): ?>
			<br/>
			<p class="you_have_no2"><br/>You have been invited to join Team <?php echo $team[0]['Team']['name'];?>. 
			Click <a href="<?php echo MAIN_SERVER.'/teams/accept/'.$team[0]['Team']['id'].'/'.$userIsPendingOnTeam['lgn'].'/'.$signupDetails['Signup']['id'];?>">here</a> to accept this invitation.
			<br/><br/></p>
		<?php else:?>
			<br/>
			<p class="you_have_no2"><br/>Some of the members of this team have not yet accepted their teammate invitation.
			Your team is not completed!<br/><br/>
			</p>
			<br/>
		<?php endif;?>
	<?php endif;?>	
<?php endif;?>

<?php elseif ($peopleinteam == 1):?>
    <a href="<?php echo MAIN_SERVER.'/signups/completeSignup/'.$signupDetails['Signup']['id'];?>">Click here to add a Singles Team to the Event</a>
<?php else: ?>
	<h3 class="new">Assign an existing team to this Event</h3>
	<?php if ($signupDetails['Signup']['status']!='paid' ):?>
		<br/>
		You can not assign your team to this Event: Please complete Payment first.
		<br/><br/><br/><br/><br/><br/>
	<?php else:?>
    Note: You can choose to use a different team name for this event 
		<br/>
		
		<?php echo $this->Form->create('TournEvent',array('id' => 'assign_form', 'url'=>"/teams/ajax_assign_from_signup/". $signupDetails['Signup']['model'] . '/' . $signupDetails['Signup']['model_id'] . '/0/' . $signupDetails['Signup']['id']));?>
		<?php echo $this->Form->select("team_id", array('0' => 'Select Team') + $teams, intval($new_created_team_id), array('escape' => false,'style'=>'float:left;width:300px;', 'onchange' => 'selectTeam();'), false); ?>
        <label for="TournEventTeamId" generated="true" class="error" id='team_select_error' style='display:none;'>Please select your team</label>
        <br/><br/>
		<div id="TeammatesForSelectedTeam" style="display: none;"><!-- Ajax  --></div>
        <div id="Loading" style="display:none">
            <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
        </div>  
        <div id="alternateNameBlock" style="display: none"><br/>
        Enter Team Name to use for this Event (leave blank to use the same name):<br /> 
        <?php echo $this->Form->input("alternate_name",array('label'=>'')); ?></div>
        <br />
        <div class="submit" style='float:left;'>
		<?php echo $this->Form->end('Assign', array('div' => false));?>
		</div>
		<div style='float:left;padding-top:7px;padding-left:20px;color: #D61C20;font-weight:bold;font-size:17px;'>
		OR
		</div>
		<div style='float:left;padding-left:15px;'>
			<a href="#" onclick='return loadTeamForm();'><img src="<?php echo STATIC_BPONG?>/img/buttons/create_new_team.gif" style='margin-top:2px;'/></a>
		</div>
        <br /><br /><br /><br />
        <div style="text-align: left;">*Note: Each team member must create his own BeerPong.com account.</div>
	<?php endif;?>
<?php endif;?>
</div>