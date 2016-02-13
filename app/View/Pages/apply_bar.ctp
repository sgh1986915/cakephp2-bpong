<?php $this->set('title_for_layout', 'Apply to be an NBPL Bar');?>
<script type="text/javascript">
$(document).ready(function() {
// validate signup form on keyup and submit
    $("#contact_form").validate({
        rules: {
            "data[Contact][your_name]": "required",
            "data[Contact][bar_name]": "required",
            "data[Contact][bar_adddress]": "required",
            "data[Contact][decision_maker]": "required",
            "data[Contact][telephone]":"required",
            "data[Contact][question]": "required",
            "data[Contact][email]": {
                required: true,
                email: true
            }
        },
        messages: {
            "data[Contact][email]": "Please enter a valid email address"
        }
    });
    //EOF Validation
});
</script>
<div class="col maincol bigtext not">                
    <h2>Apply to be an NBPL Bar<br />
        <span>Interested in becoming an NBPL Bar? Fill out this form and we'll get back to you immediately!</span>
        </h2>
      <?php echo $this->Form->create('Contact', array('id' => 'contact_form', 'name'=>'contact_form', 'url' => '/Pages/send_apply_bar', 'method' => 'post', 'class' => 'inl'));?>
        <?php 
        echo $this->Form->input('your_name', array('label' => '*Your name (Fields marked * are mandatory)', 'val' => ''));
        echo $this->Form->input('email', array('label' => '*Email address'));
        echo $this->Form->input('bar_name', array('label' => '*Bar name'));    
        echo $this->Form->input('bar_adddress', array('label' => '*Bar address'));
        echo $this->Form->input('bar_city_state',array('label'=>'*City/State'));
        echo $this->Form->input('bar_manager_name',array('label'=>'*Name of Bar Decision Maker'));
        echo $this->Form->input('telephone', array('label' => '*Telephone number'));
        echo $this->Form->input('question', array('type' => 'textarea', 'label' => '*Why the bar is perfect for Beer Pong'));
        ?>
        <div class="submit">
                <input type="submit" class="btn" value="APPLY" name="" />
        </div>
    </form>
</div>