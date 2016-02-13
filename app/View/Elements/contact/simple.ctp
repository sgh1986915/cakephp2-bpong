 <script type="text/javascript">
      $(document).ready(function(){
    		$("#<?php echo $name?>").validate({
    			rules: {
    					"data[<?php echo $name?>][email]": {
    					  required: true,
    					  email: true
    				    },
    				    "data[<?php echo $name?>][confirmEmail]": {
    						required: true,
    						email: true,
    						equalTo: "#<?php echo $name?>Email"
    					},
    					"data[<?php echo $name?>][subject]": {
      					  required: true
      				    },
      				    "data[<?php echo $name?>][body]": {
        					  required: true
        				}
    			},
    			messages: {
    			}
    		});
        
      });
  </script>
<?php echo  $this->Form->create($name,array('id'=>$name, 'url' => array ('controller'=>'pages','action'=>"contactnew"),'enctype'=>"multipart/form-data")); ?>
                	      <?php echo $this->Form->hidden ('type',            array('value'=>$name));?>                	      
                	      
                	      <?php echo  $this->Form->input('email',             array('size' => 20));?>
                	      <?php echo  $this->Form->input('confirmEmail',   array('size' => 20));?>
                	      <?php echo  $this->Form->input('subject',           array('size' => 100));?>
                	      <?php echo  $this->Form->textarea('body',          array('legend'=>'body'));?>
<?php echo $this->Form->end('Send'); ?>