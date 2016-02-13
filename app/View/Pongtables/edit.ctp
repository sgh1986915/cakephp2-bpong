<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript">
	XBB.textarea_id = 'PongtableDescription'; // id of a textarea
	XBB.area_width = '550px';
	XBB.area_height = '200px';
	XBB.state = 'plain'; // 'plain' or 'highlight'
	XBB.lang = 'en_utf8'; //
</script>
<script type="text/javascript">
<!--
$(document).ready( function () {
	$("img[name='Delete']").click(function () {
		var current_object = this;
		var currentphotoID = this.id;

		$(current_object).hide();
		$.post("/pongtables/deleteimage", { imageID: currentphotoID }, function ( response ) {
																	if (response == "1") {
																		$("#photo_"+currentphotoID).hide();
																	}else {
																		alert('Image was not deleted.');
																		$(current_object).show();
																	}
															});
	});
});

//-->
</script>
<div class="actions">
	<ul>
		<li><span class="backbtn"><?php echo $this->Html->link('Back to list', array( 'action'=> 'index' ), array('class'=>'backbtn'));?></span></li>
		<?php if($Deleted): ?>
			<li><?php echo $this->Html->link('Delete', array('action'=>'delete', $this->Form->value('Pongtable.id')), array('class'=>'delbtn'), sprintf('Are you sure you want to delete # %s?', $this->Form->value('Pongtable.id'))); ?>
   		<?php endif; ?>
        </li>
	</ul>
</div>

<?php echo $this->Form->create ( 'Pongtable', array ( 'type' => 'file' ) );?>
	<?php
		if(!empty($this->request->data['Tableimage'])){
			echo "<div>";
			foreach($this->request->data['Tableimage'] as $index => $imagefile):
	?>
		<div id="photo_<?php echo $imagefile['id']; ?>" style="float:left;">
			<a href="<?php echo IMG_MODELS_URL;?>/<?php echo $imagefile['filename']; ?>" title="" class="thickbox">
				<?php echo $this->Html->image(IMG_MODELS_URL . "/thumbs_" . $imagefile['filename']); ?>
			</a>
			<?php echo $this->Html->image(STATIC_BPONG."/img/delete.gif", array("alt" => 'Delete', 'name'=> 'Delete', 'id' => $imagefile['id']));?>
		</div>
	<?php
			endforeach;
			echo "</div>";
		}
	echo $this->Form->input('Image.new',array('type' => 'file','class'=>'file','label'=>__('Add Image')));

	echo $this->Form->input ( 'title' );
	echo $this->Form->input ( 'Address.address', array( 'type' => 'text' ));
	echo $this->Form->input ( 'Address.city' );
	echo $this->Form->input ( 'Address.provincestate_id' );
	echo $this->Form->input ( 'Address.country_id' );
	echo $this->Form->input ( 'description' );
	echo $this->Form->input ( 'analysis' );

	if ( $access_to_aproove ) {
		echo $this->Form->input ( 'is_aprooved', array('type' => 'checkbox') );
	}
	?>
<?php echo $this->Form->end ( 'Submit' );?>
<script type="text/javascript">
	XBB.init();
</script>
