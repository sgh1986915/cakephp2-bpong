<div class="users form informwho">
  <h2>User information</h2>
    <fieldset>
    <div class="input"><label>Registered:</label>
    <?php echo $this->Time->niceDate($user['User']['created']); ?></div>
    <div class="input"><label>Rate:</label>
    <?php echo $user['User']['rate']; ?> </div>
    <?php if (!empty($user['User']['show_details']) || $canSeeAllPar): ?>
    <div class="optional">
      <h4>Optional information</h4>
    </div>
    <?php if ($canSeeAllPar):?>
    <!-- admin information-->
    <div class="input"><label>ID number:</label>
    <?php echo $user['User']['id']; ?></div>
    <div class="input"><label>Email:</label>
    <?php echo $user['User']['email']; ?></div>
    <?php if (!empty($user['User']['birthdate'])): ?>
    <div class="input"><label>Birthdate:</label>
    <?php echo $this->Time->niceDate($user['User']['birthdate']); ?> </div>
    <?php endif;?>
    <?php if (!empty($user['Timezone']['name'])): ?>
    <div class="input"><label>Timezone:</label>
    <?php echo $user['Timezone']['name']; ?>  </div>
    <?php endif;?>
    <div class="input"><label>Last logged:</label>
    <?php echo $this->Time->niceDate($user['User']['last_logged']); ?>  </div>
    <div class="input"><label>Last logged ip:</label>
    <?php echo $user['User']['last_logged_ip']; ?>  </div>
    <!--  EOF admin information-->
    <?php endif;?>
    <div class="input"><label>First Name:</label>
    <?php echo $user['User']['firstname']; ?>  </div>
    <div class="input"><label>Middle Name:</label>
    <?php echo $user['User']['middlename']; ?>  </div>
    <div class="input"><label>Last Name:</label>
    <?php echo $user['User']['lastname']; ?>  </div>
    <div class="input"><label>Nick Name:</label>
    <?php echo $user['User']['lgn']; ?>  </div>
    <div class="input"><label>Gender:</label>
    <?php echo $user['User']['gender']; ?> </div>
    <?php endif; ?>
    </fieldset>
    <?php if ($canSeeAllPar && !empty($user['Phone'])): ?>
    <div class="optional">
      <h4>Phones</h4>
    </div>
    <table cellpadding="0" cellspacing="0">
      <tr>
        <th>type</th>
        <th>phone</th>
      </tr>
      <?php foreach ($user['Phone']as $phone):?>
      <tr >
        <td style="background-color:#dfebfb"><?php echo $phone['type'] ?></td>
        <td style="background-color:#dfebfb"><?php echo $phone['phone'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif;?>
    <!-- -->
  <?php if ($canSeeAllPar && !empty($user['Address'])): ?>
    <div class="optional">
      <h4>Addresses</h4>
    </div>
  <table id="address" cellpadding="0" cellspacing="0" >
    <tr>
      <th>Country</th>
      <th>Address</th>
      <th>City</th>
      <th>State</th>
      <th>Postalcode</th>
      <th>Label</th>
    </tr>
    <?php foreach ($user['Address']as $address):?>
    <tr >
      <td style="background-color:#dfebfb; min-width:1px"><?php echo empty($address['Country']['name'])?"":$address['Country']['name'] ?></td>
      <td style="background-color:#dfebfb; min-width:1px"><?php echo $address['address'] ?></td>
      <td style="background-color:#dfebfb; min-width:1px"><?php echo $address['city'] ?></td>
      <td style="background-color:#dfebfb; min-width:1px"><?php echo empty($address['Provincestate']['name'] )?"":$address['Provincestate']['name'] ?></td>
      <td style="background-color:#dfebfb; min-width:1px"><?php echo $address['postalcode'] ?></td>
      <td style="background-color:#dfebfb; min-width:1px"><?php echo $address['label'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif;?>
  <?php if ($canSeeAllPar && isset($teams) && !empty($teams)): ?>
    <div class="optional">
      <h4>User teams</h4>
    </div>
  <table cellpadding="0" cellspacing="0" >
    <tr>
      <th>Team name</th>
      <th>created</th>
    </tr>
    <?php foreach ($teams as $team):?>
    <tr >
      <td style="background-color:#dfebfb"><a href="/nation/beer-pong-teams/team-info/<?php echo $team['Team']['slug'] ?>/<?php echo $team['Team']['id'] ?>"> <?php echo $team['Team']['name'] ?></a></td>
      <td style="background-color:#dfebfb"><?php echo $this->Time->niceDate($team['Team']['created']); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif;?>
  <br/>
  <!-- -->
  <?php if ($canSeeAllPar && isset($signups) && !empty($signups)): ?>
    <div class="optional">
      <h4>User signups</h4>
    </div>
  <table cellpadding="0" cellspacing="0">
    <tr>
      <th>Signup date</th>
      <th>Signed up for</th>
      <th>Status</th>
      <th>Paid</th>
      <th>Discount</th>
      <th>Total</th>
      <th>Rest</th>
      <th></th>
    </tr>
    <?php foreach ($signups as $signup): ?>
    <tr>
      <td><?php echo $this->Time->niceDate($signup['Signup']['signup_date']) ?> </td>
      <td><?php echo $signup[$signup['Signup']['model']]['name'] ?> </td>
      <td><?php echo $signup['Signup']['status'] ?></td>
      <td> $<?php echo sprintf("%.2f", $signup['Signup']['paid']) ?></td>
      <td> $<?php echo sprintf("%.2f", $signup['Signup']['discount'])?></td>
      <td> $<?php echo sprintf("%.2f", $signup['Signup']['total']) ?></td>
      <td><?php $rest = floatval($signup['Signup']['total'])-floatval($signup['Signup']['discount'])-floatval($signup['Signup']['paid']);?>
        <?php if ($rest<0):
        $rest = min(abs($rest),floatval($signup['Signup']['paid']));
        if ($rest>0)
          $rest = -1*$rest;
         endif;
     ?>
        <?php echo $rest>=0?'$'.sprintf("%.2f",$rest):'-$'.sprintf("%.2f",abs($rest));?></td>
      <td><?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View signup details" title="View signup details"/>', array('controller'=>'signups' , 'action'=>'signupDetails', $signup['Signup']['id']), array('escape'=>false)); ?> </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif;?>
</div>
