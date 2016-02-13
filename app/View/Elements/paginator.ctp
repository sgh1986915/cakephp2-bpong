<div class="pagination">

	<div class="counter">
	   <?php echo 'Page '. $this->Paginator->counter() ?>
	</div>

	<form action="" method="post">
    <div class="resultsperpage">
		<?php echo $this->Form->input(	'limit', array(
			'type'       => 'select'
           ,'label'      => 12
           ,'options'    => $limit_values
           ,'selected'   => $selected
           ,'onchange' => 'this.form.submit()'
           ,'showEmpty'  => false			
		));//input ?>
    </div>
	</form>

</div>
