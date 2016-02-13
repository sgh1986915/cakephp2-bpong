<?php echo $this->element('mce_init_simple', array('name' => 'TeamDescription')); ?>
<script type="text/javascript">
	//EOF Teammate Validation
 function findTeammates(){
     if (!$('#TeammateEmail').val() && !$('#TeammateLgn').val()  && !$('#TeammateLastName').val() )
        return false;
     $('#Loading').show();
     $('#UserInformation').hide();
     //var teamid = 'asdfasd';
    
    $.post('/teammates/findNewTeammates/',
        {'data[Teammate][email]':$('#TeammateEmail').val(),
          'data[Teammate][lgn]':$('#TeammateLgn').val(),
          'data[Teammate][last_name]':$('#TeammateLastName').val()
          ,'data[Team][id]':'<?php echo $this->request->data['Team']['id']; ?>'
        },function(responseText) {
            $('#Loading').hide('slow');
            
            $('#UserInformation').html(responseText);
            $('#UserInformation').show('slow');
        });
    return false;
 }  
//EOF ready

function HideTeammate(){

	if ( $("#SubmitButton").css('display') != "none" )
		 $("#SubmitButton").hide();
	if ( $("#ERROR").css('display')!= "none" )
      	 $("#ERROR").hide('slow');
	  $("#SubmitButton").hide();
      $("#UserInformation").hide(function(){ $('#Loading').show();});
}

function showResponse(responseText)  {
   		 $('#Loading').hide();
	  	 $("#SubmitButton").show();

	  if (responseText==""){
	  		$('#ERROR').show('slow');
	  } else {
	  		$('#UserInformation').html(responseText);
	  		$('#UserInformation').show();
	  }
}
</script>

 		<h2>Edit team</h2>

<div class="teams form p10">
<?php echo $this->Form->create('Team',array('enctype'=>"multipart/form-data",'url'=>'/nation/beer-pong-teams/edit-team/'.$this->request->data['Team']['slug'].'/'.$this->request->data['Team']['id']));?>
	<?php echo $this->Form->hidden('slug');?>
	<?php echo $this->Form->hidden('id');?>
	<fieldset>
		<div class="textarea"><label>Official image</label></div>
		<br />
<?
        if(count($images)){
            foreach($images as $img){
                ?>
                <img src="<?php echo IMG_MODELS_URL;?>/thumbs_<?php echo $img['Image']['filename'] ?>" border="0">
				<?php
    		   echo $this->Form->input('Image.'.$img['Image']['id'],array('type' => 'file','class'=>'file','label'=>'Image'));
    		   echo $this->Form->hidden('Image.'.$img['Image']['id'].'.prop',array('value'=>'Personal'));
            }
        }else{
    		    echo $this->Form->input('Image.new',array('type' => 'file','class'=>'file','label'=>'Image'));
    		    echo $this->Form->hidden('Image.new.prop',array('value'=>'Personal'));
        }	?>


	<?php
		echo $this->Form->input('name', array('size' => 70));
		if ($this->request->data['Team']['status']=='Completed')
			echo $this->Form->input('people_in_team',array('readonly'=>true));
		else
			echo $this->Form->input('people_in_team',array());
		echo $this->Form->input('description', array('label'=>false));
	?>
    </fieldset>
<?php echo $this->Form->end('Submit', array('class'=>'submit_gray'));?>
<!-- working with teams -->

<?php if ($canInviteMoreTeammates):?>
	<!-- Add new teammate-->
	<div class="tournaments form" style="border:#ccc solid 1px; padding:15px;margin-top:20px; margin-bottom:15px;position:relative;">
        <?php echo $this->Form->create('Teammate',array('id'=>'Teammate','name'=>'Teammate','default'=>false,'onsubmit'=>'findTeammates();'));?>
	 		<h3>Invite new teammate</h3>
	 		<?php echo $this->Form->hidden('team_id',array('value' => $this->request->data['Team']['id']));?>
         <div class="left" style="width:130px; padding-top:10px">Email:</div><div class="left" style="width:600px"><?php echo $this->Form->input('email',array('size' => 40,'label'=>false));?></div>
         <div class='clear'></div>
         <div class="left" style="width:130px; padding-top:10px">Username:</div><div class="left" style="width:600px"><?php echo $this->Form->input('lgn',array('size' => 40,'label'=>false));?></div>
         <div class='clear'></div>
         <div class="left" style="width:130px; padding-top:10px">Last Name:</div><div class="left" style="width:600px"><?php echo $this->Form->input('last_name',array('size' => 40,'label'=>false));?></div>  

			<div class="clear"></div><div class="clear"></div>
            <div id="ERROR" style="display: none;">Can't find such user</div>
			<div id="UserInformation" style="display: none;"><!-- This is for AJAX --></div>
	<div class='clear'/></div>
	<div id="Loading" style="display:none;float:left;clear:both;"><?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'TeammateLoading')) ?></div>
	<div class='clear'/></div>
	<?php echo $this->Form->end('Find', array('class'=>'submit_gray'));?>
	<div class='clear'></div>
   </div>
<?php endif;?>
<!-- Show all teammates-->
<?php if (!empty($teammates)):?>
<div style="padding-top:30px">
  <table>
    <tr>
      <th>Nick name</th>
      <th>First name</th>
      <th>Last name</th>
      <th>Status</th>
      <th>Invited</th>
      <th>&nbsp;</th>
    </tr>
<?php 
$i = 0;
foreach ($teammates as $teammate):
$class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
?>
	 <tr<?php echo $class;?>>
      <td> <a href="/users/view/<?php echo $teammate['User']['lgn']?>"><?php echo $teammate['User']['lgn']?></a></td>
      <td><?php echo $teammate['User']['firstname']?></td>
      <td><?php echo $teammate['User']['lastname']?></td>
      <td><?php echo $teammate['Teammate']['status']?></td>
      <td><?php echo $this->Time->niceDate($teammate['Teammate']['created'])?></td>

      <td><?php if ($teammate['Teammate']['status']=="Pending" && $teammate['User']['id']==$user['id']):?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/accept.gif" alt="Accept invitation to this team" title="Accept invitation to this team"/>', array('action'=>'accept', $this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false)); ?>
	      <?php endif; ?>
          <?php if ($teammate['Teammate']['status'] == 'Pending'): // if the user has access to edit the team, the should be able to delete
           ?>
            <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete this user from the team" title="Delete this user from team"/>', array('controller'=>'teammates','action'=>'delete', $this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false),'Are you sure you want to delete this user from the team?'); ?>
		  <?php endif;?>
		  <?php if (($teammate['Teammate']['status']=="Creator" || $teammate['Teammate']['status']=="Accepted" )&& $teammate['User']['id']==$user['id'] && $this->request->data['Team']['status']!="Deleted"):?>
		  	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete me from this team" title="Delete me from this team"/>', array('controller'=>'teammates','action'=>'delete',$this->request->data['Team']['id'],urlencode($teammate['User']['lgn'])), array('escape'=>false),'Are you sure you want to delete from this team?'); ?>
		  <?php endif;?>
      </td>
    </tr>
<?php endforeach;?>
  </table>
  </div>
<?php else:?>

	<div class="you_have_no">There are no teammates</div>
<?php endif;?>
<div class="clear"></div>

<!-- EOF teams -->

</div>