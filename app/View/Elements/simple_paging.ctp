<?php if($this->Paginator->numbers() > 1): ?>
    <div style='width:100%;text-align:center;' class='no_underline'>
    	<br/>
    	pages: <?php echo $this->Paginator->prev('<< prev');?> <?php echo $this->Paginator->numbers(array('separator' => '&nbsp;&nbsp;'));?> <?php echo $this->Paginator->next('next >>');?><br/>
    </div>
<?php endif;?>