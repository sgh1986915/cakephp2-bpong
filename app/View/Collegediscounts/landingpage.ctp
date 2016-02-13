<script type="text/javascript">
tb_pathToImage = "";
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/
$.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var check = false;
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please enter a <?php echo $schooldisplayname; ?> email address."
);


    $("#EmailInput").validate({
        rules: {
            "data[User][useremail]": { required:true, email:true },
        },
        messages: {
            "data[User][useremail]": "Please enter a valid email address!"
        }
    });
    //Email
    //$validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
    
    //EOF Validation


});

</script>
<?php $this->pageTitle = $schooldisplayname.' Student Discount for The World Series of Beer Pong'; ?>
<div class="loginboxmain" style="margin-top: 30px;width: 500px;" align="center">We are pleased to provide entrance into the World Series of Beer Pong VII to all <?php echo $schooldisplayname; ?> Students 
for only $<?php echo $price; ?> per person<?php if (!$includesHotel) echo '*'?>. To get started, please enter your <?php echo $schooldisplayname; ?> email address*<?php if (!$includesHotel) echo '*'?>:<br /><br />
 <?php echo $this->Form->create('User',array('id'=>'EmailInput','name'=>'EmailInput','url'=>'/collegediscounts/submitemail/'.$school.'/'.$source));?>
  <fieldset class="loginpad">
  <?php
        echo $this->Form->hidden('URL');?>
        <div align="center">
  <?php    echo $this->Form->input('useremail',array('legend'=>'','label'=>false, 'id'=> 'useremail'));?>
  </div>
  <br /> <div align="center">
  <?php echo $this->Form->end(array('value' => 'GO','class'=>'submit'));?>
  </div>
  </fieldset>
  <div class="clear"></div>
  <?php if (!$includesHotel): ?>*Price does not include a hotel stay package. For discounted hotel rooms, please call us at (702)-723-6572.<br /> <br />
  *<?php endif; ?>*At least one team member must be a<?php if ($an) echo 'n'; ?> <?php echo $schooldisplayname; ?> student. Either player can sign the team up.<br /><br />
  The World Series of Beer Pong VII takes place <b>January 2nd, 3rd, and 4th</b> at <b>The Flamingo Hotel and Casino</b> on the Las Vegas Strip. For more info on the WSOBP, click <a href="<?php echo MAIN_SERVER; ?>/wsobp">here.</a>   
  <br /><br />
  Got questions? Call us at (702) 723-6572.
</div>