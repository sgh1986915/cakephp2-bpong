<?php echo $this->Html->script('ui.datepicker'); ?>
 
<script type="text/javascript">
$(document).ready(function(){
    $('#datepicker').datepicker({ dateFormat: '<?php echo JS_DATE_FORMAT; ?>' });
    
    $('#getDate').click(function(){
        $('#datepicker').datepicker("show");
        return false;
    });  
});
</script>

<div class="input">
    <?php echo $this->Form->input("{$this->request->params['models'][0]}.date", array(
         'type' => 'text'   
        ,'label' => Date
        ,'size' => 10
        ,'div'  => false
        ,'id' => 'datepicker'
        ,'value' => isset($this->request->data[$this->request->params['models'][0]]['date']) 
                    ? $this->Time->format(CAKE_DATE_FORMAT, $this->request->data[$this->request->params['models'][0]]['date'])
                    : ''
    )) ?>
    <button id="getDate">*</button>
</div>
