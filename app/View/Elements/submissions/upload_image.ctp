<script type="text/javascript">
	$(document).ready(function(){
		$('#albumType').sSelect();
	});
</script>
<?php if (!isset($this->request->data['albumType'])) {
                $this->request->data['albumType'] = '0';
            }
            if (!isset($album_id)) {
                $album_id = '0';
            }
?>
<script type="text/javascript">
	var selectedAlbumID = <?php echo $album_id;?>;
	var imageLimit = 0;
	var imageUploaded = 0;
	var images = {};

    $(document).ready(function() {   	       
    	$("#uploadify").uploadify({
    		'uploader'       : '/js/uploadify/uploadify.swf',
    		'script'            : '/Images/saveFromSubmissions',
    		'cancelImg'     : '/img/cancel.png',
    		'scriptData'     : {'param1' : '<?php echo $userSession['id']?>', 'param2' : selectedAlbumID},
    		'width'            : 82,
			'height'           : 21,   	
			'hideButton'     : true,
			'wmode'          : 'transparent',   				
    		//'folder'         : '/uploads',
    		//'method'      : 'GET',
    		//'buttonImg'  : '/img/upload_button.gif',	
    		'onError'          : function () {$('#files_loader').hide();}, 
    		'fileDesc'         : 'Images (jpg, png, gif). Maximum size 1MB',
    		'fileExt'            : '*.jpg;*.png;*.gif;*.JPG',
    		'sizeLimit' 		   : '3100000',
    		'queueID'        : 'fileQueue',		
    		'auto'           : true,
    		'multi'          : true,
    		'onSelect' : function () {$('#files_loader').show();}, 
    		'onCancel' : function () {$('#files_loader').hide();}, 
    		'onComplete'     : function(event,queueID,fileObj,response,data)  {
				if (response > 0) {
					$('#albumType').attr('disabled', 'disabled');
					$('#albumID').attr('disabled', 'disabled');
					
					getCreatedImage(response);
					if (imageLimit) {						
						$('#uploadify_button').hide();
					}
				} else {
					//$('#files_loader').hide();
				}
    		}
    	});
    	$('#files_loader').hide();
    	$('#files_loader').html(contentLoading());
    	
    });
	function selectAlbumType() {
		$.ajaxSetup({cache:false});
		$('#create_album').html(contentLoading());
		$('#album_list').html('');
		$('#content_upload').hide();
		$('#uploadify_button').show();
		imageLimit = 0;
		switch($('#albumType').val()){
		case "itself":
			$('#upload_block').html('3)');	
			$('#create_album').html('');
			$('#content_upload').show();
			imageLimit = 1;
		    break;
		case "new":
			$('#upload_block').html('4)');	
			$('#create_album').load('/Albums/create_from_submissions/image/', function() {});	
		    break;
		case "existing":
			$('#upload_block').html('3)');	
			$('#album_list').load('/Submissions/album_list/image/', function() {
				$('#create_album').html('');
			});	
			break;
		case "0":
			$('#create_album').html('');
			$('#upload_block').html('3)');	
			break;			  
		}
	}
	function selectAlbumName() {
		selectedAlbumID = $('#albumID').val();
		if (selectedAlbumID == 0) {
			$('#content_upload').hide();			
		} else {
			$('#content_upload').show();	
		};
	}
	function getCreatedImage(id) {
		if (id == 'er1') {
			alert("Image error - incorrect file size");
		} else {
		var divID = 'upImage_' + id;
		$('#created_files').append('<div id="' + divID +'"></div>');	
		$('#' + divID).load('/Submissions/getCreatedImage/' + id + '/' + selectedAlbumID, function() {
			$('#files_loader').hide();
			$('#upload_continue').show();
			if (imageUploaded == 0) {
				var block_number = $('#upload_block').html();
				if (block_number == '3)') {
					block_number = 4;
				} else {
					block_number = 5;
				}	
				$('#image_info_text_' + id).html("<span class='submit_nums'>" + block_number + ")</span> <b>Enter a title, description, and tags</b><br class='clear'/");
			}
			imageUploaded = imageUploaded + 1;
		});	
		}
	}
	function submitContentForm() {
		$('#selectedAlbumID').val(selectedAlbumID);
		var someEmpty = 0;
		for (var key in images) {
			if (!$('#all_images' + images[key] +'Name').val()) {		
				someEmpty = 1; 
			}			
		}
		if (someEmpty == 1) {
			alert('Please specify titles of all images');
		} else {
			$('#submitContentForm').submit();
		}			

	}
</script>

<div class="submit_package"> <span class='submit_nums'>2)</span> <b>Do you want to create a new album for this image, add it to an existing album, or submit it by itself?</b> <?php echo $this->Form->input('albumType', array('id' => 'albumType', 'label' => false, 'div' => false, 'type' => 'select', 'class' => 'custom-select', 'onchange' => 'selectAlbumType();', 'options' => 
    array('0' => 'Select One', 'itself' => 'By Itself', 'new' => 'New Album', 'existing' => 'Existing Album'), 'selected' => $this->request->data['albumType'])); ?>
  <div style='margin-left:15px; margin-top:12px;'> <span class='submit_nums'>Note:</span> If you select "By Itself", you will only be able to upload one image at a time.
    <div id = 'album_list'>
      <?php if ($album_id && $this->request->data['albumType'] != 'itself'):?>
      <?php echo $this->element('/submissions/album_list'); ?>
      <?php endif;?>
    </div>
  </div>
</div>
<div id='create_album'></div>
<div id='content_upload' style='<?php if (!$album_id):?>display:none; <?php endif;?>padding-top:10px; padding-bottom:10px;clear:both;'  class="submit_package"> <span class='submit_nums' id='upload_block'>3)</span> <b>Upload image (jpg, png, gif). Maximum size 3 MB</b> <br class='clear/'>
  <div style='margin-left:15px;float:left;margin-top:10px;height:21px; clear:both; background-image: url(/img/upload_button.gif);' id='uploadify_button'>
    <input type="file" name="uploadify" id="uploadify" />
  </div>
  <div id="fileQueue"></div>
  <div class='clear'></div>
</div>
<div id='files_loader'></div>
<?php echo $this->Form->create('Images', array('id' => 'submitContentForm', 'url' => '/images/submissionsAdd/'));?> <?php echo $this->Form->hidden('album_id', array('div' => false, 'id' => 'selectedAlbumID', 'value' => $album_id)); ?>
<div id='created_files'></div>
</form>
<div id='upload_continue' class="submit_package" style='display:none'>
  <input type="button" value="Complete" class='red_button' onclick='submitContentForm();'>
</div>
<br class='clear' />
<br/>
<br/>
