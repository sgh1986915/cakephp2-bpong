<h2>All signups</h2>

<?php echo $this->Form->create('Signup',array('id'=>'SignupFilter','name'=>'SignupFilter','action'=>'showAllSignups'));?>
<fieldset>
<?php echo $this->Form->input('SignupFilter.searchby',array('type' => 'select','label'=>'Search By','options' => array('AND'=>'AND','OR'=>'OR')));?>
<?php echo $this->Form->input('SignupFilter.eventid',array('type'=>'text','label'=>'Event ID','size'=>15)); ?>  
<?php echo $this->Form->input('SignupFilter.model');?>  
<?php echo $this->Form->input('SignupFilter.status');?>
<?php echo $this->Form->input('SignupFilter.for_team',array('type' => 'select','label'=>'Type','options' => array('all' => 'All', '0' => 'Personal', '1' => 'For entire team')));?>
<?php echo $this->Form->input('SignupFilter.user_email',array('label'=>'User email','size'=>15));?>
<?php echo $this->Form->input('SignupFilter.user_lastname',array('label'=>'User lastname (Like)','size'=>15));?>
<?php echo $this->Form->input('SignupFilter.user_login',array('label'=>'User login (Like)','size'=>15));?>
<?php echo $this->Form->input('SignupFilter.user_id',array('label'=>'User id','size'=>5, 'type' => 'text'));?>
</fieldset>
<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if (!empty($signups)): ?>
<table>
<tr>
  <th class="widthth"><?php echo $this->Paginator->sort('signup_date');?></th>
  <th>User</th>
  <th class="widthmin">Signed up for</th>
  <th><?php echo $this->Paginator->sort('status');?></th>
  <th><?php echo $this->Paginator->sort('paid');?></th>
  <th><?php echo $this->Paginator->sort('discount');?></th>
  <th><?php echo $this->Paginator->sort('total');?></th>
  <th>Rest</th>

  <th style='width:110px;'></th>
</tr>
<?php 
$i = 0;
foreach ($signups as $signup): 
    	$class = '';
    	if ($i++ % 2 != 0) {
    		$class = ' class="alt"';
    	}
?>
<tr<?php echo $class;?>>
  <td class="fs11"> <?php echo $this->Time->niceDate($signup['Signup']['signup_date']) ?> </td>
  <td class="fs11"> <a href="/users/view/<?php echo $signup['User']['lgn'] ?>"><?php echo $signup['User']['lgn'] ?></a></td>
  <td> <a href="/event/<?php echo $signup['Signup'][$signup['Signup']['model']]['id']; ?>"><?php echo $signup['Signup'][$signup['Signup']['model']]['name'] ?> </a></td>
  <td> <?php echo $signup['Signup']['status'] ?> <?php if ($signup['Signup']['for_team']):?>(for entire team)<?php endif;?></td>
  <td> $<?php echo sprintf("%.2f", $signup['Signup']['paid']) ?></td>
  <td> $<?php echo sprintf("%.2f", $signup['Signup']['discount'])?></td>
  <td> $<?php echo sprintf("%.2f", $signup['Signup']['total']) ?></td>
  <td> <?php $rest = floatval($signup['Signup']['total'])-floatval($signup['Signup']['discount'])-floatval($signup['Signup']['paid']);?>
     <?php if ($rest<0):
        $rest = min(abs($rest),floatval($signup['Signup']['paid']));
        if ($rest>0)
          $rest = -1*$rest;
         endif;
     ?>
     <?php echo $rest>=0?'$'.sprintf("%.2f",$rest):'-$'.sprintf("%.2f",abs($rest));?></td>

  <td> <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View signup details" title="View signup details"/>', SECURE_SERVER . '/signups/signupDetails/' . $signup['Signup']['id'], array('escape'=>false)); ?>
    <?php if ($signup['Signup']['user_id'] == $signup['User']['id']): ?>
        <?php if ($signup['Signup']['status']!="cancelled"):?>
           <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_with_refund.gif" alt="Cancell This Signup with Refund" title="Cancel this signup with Refund"/>', SECURE_SERVER . '/signups/cancelWithRefundForm/'.$signup['Signup']['id'].'?inlineId=cancelWithRefundForm&amp;height=300&amp;width=342&amp;modal=true;" class="thickbox" id="cancelWithRefundForm" title="Cancell with signup"', array('escape'=>false)); ?>
           <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Cancel This Signup" title="Cancel this signup"/>', array('action'=>'cancell', $signup['Signup']['id']), array('escape'=>false),'Are you sure you want to cancel this sign up?'); ?>
         <?php endif;?>

         <?php if ($rest<0):?>
           <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/refund.gif" alt="Refund This Signup" title="Refund this signup"/>', SECURE_SERVER . '/signups/refund/' . $signup['Signup']['id'], array('escape'=>false),'Are you sure you want to refund this sign up?'); ?>
         <?php endif;?>
         <a href="<?php echo MAIN_SERVER.'/signups/transferringForm/'.$signup['Signup']['id'].'?&inlineId=transferringForm&amp;height=150&amp;width=342&amp;modal=true;'?>" class="thickbox" id="transferringForm" title="Transferring to another user">
            <img src = "<?php echo STATIC_BPONG.'/img/transferring_signup.gif'?>" alt="Transferring to another user" title="Transferring to another user" />
         </a>
            
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
  <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
  <?php echo $this->Paginator->numbers();?>
  <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
  <?php echo $this->element('pagination'); ?>
</div>

<?php else:?>
  There are no signups for such criteria.
<?php endif;?>
