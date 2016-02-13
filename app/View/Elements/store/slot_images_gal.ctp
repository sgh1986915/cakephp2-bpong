<script type="text/javascript">
function showSlotBigImage(imageID) {
	if (!imageID || imageID == 'null') {
		imageID = $('.first_image').attr('id');
		imageID = imageID.replace('img_', '');
	}
	if (!galIsloading) {
		galIsloading = true;
		//alert(imageID);
		var newHeight = $("#slot_image_" + imageID).height();
		//alert(oldHeight + ' - ' + newHeight);

		$('.slot_gall_image').removeClass('slot_gall_image_red');
		$('#img_' + imageID).addClass('slot_gall_image_red');

		$('#slot_image_' + selectedImage).fadeOut('400', function() {
			$('.store_gall').hide();
			if (selectedImage !='loader') {
				$('#slot_image_loader').show();
			}
			$("#img").animate({height: newHeight}, 600 , function() {
				$('#slot_image_loader').hide();
				$("#slot_image_" + imageID).fadeIn('400', function () {
					galIsloading = false;
				});
			});

		selectedImage = imageID;
			//$("#price").scrollBottom(10, 70);
		});
	}
	return false;
}
function changeBigImage() {
	if (selectedImage !='loader') {
		var nextImage = $('#slot_image_' + selectedImage).next('.store_gall').attr('id');
		if (!nextImage) {
			nextImage = $('#big_image_link > .store_gall:first').attr('id');
		}
		if (nextImage) {
			nextImage = nextImage.replace('slot_image_', '');
			showSlotBigImage(nextImage);
		}
	}
	return false;
}
</script>
<div style='width:100%;'>

<?php
$i = 0;
foreach ($images as $img): $i++;?>
	<div class = "slot_gal_div">
		<a id="img_<?php echo $img['Image']['id'];?>" class='slot_gall_image <?php if ($i == 1){ echo 'first_image';}?>' style='background-image:url("<?php echo IMG_ALBUMS_URL;?>/slot_thumb_<?php echo $img['Image']['filename'];?>");' href="#" onclick="return showSlotBigImage(<?php echo $img['Image']['id'];?>);"> </a>
	</div>
<?php endforeach;?>
</div>