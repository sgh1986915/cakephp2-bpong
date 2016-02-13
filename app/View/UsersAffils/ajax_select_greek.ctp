<script type="text/javascript">

$(document).ready(function() {
	$.ajaxSetup({cache:false});
    $("#affil_name").autocomplete("/greeks/autocomplete/", {
        		width: 320,
        		max: 7,
        		cache:false,
        		highlight: false,
        		multiple: false,
        		scroll: true,
        		scrollHeight: 300,
        		cacheLength:0,
        		extraParams: {
        		       type: function() {return $("#affil_type").val();}
        		}
    });
    
    // Country click
	$("#affil_country_id").change(function(){
		  $("#affil_provincestate_id").html('<option>Loading...</option>');
		  $.ajaxSetup({cache:false});
		  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){

				$("#affil_provincestate_id").html(options);
				$('#affil_provincestate_id option:first').attr('selected', 'selected');
			})

		});  				   				   			   				   
});

function saveAffil () {
	var affilName = $('#affil_name').val();
    var affilType = $("#affil_type").val();
	if (!affilName) {
		alert('Please specify Greek Name');
		return false;
	}
	if (!affilType) {
		alert('Please specify Greek Type');
		return false;
	}
	
	$('#users_affils_block').html(showLoaderHtml());	
	$.ajaxSetup({cache:false});
	$.post("/users_affils/save_greek", { 'name': affilName, 'type': affilType},
			   function(data) {
				$('#users_affils_block').load('/users_affils/usersProfileBlock/<?php echo $userID;?>');	   
	});
	
	tb_remove();
}
</script>
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Select Your Greek</h1>
<div class="whitebg nopad">
  <fieldset style='font-size: 15px;color: #333333;line-height: 20px;width:90% !important;'>
  Greek Type<br/>
  <?php echo $this->Form->input('Affil.type',array('id'=>'affil_type','type' => 'select', 'label'=> false, 'style' => 'width:190px;','options' => array('Fraternity' => 'Fraternity', 'Sorority' => 'Sorority')));?>
  Greek Name<br/>
  <?php echo $this->Form->input('Affil.name', array('style' => 'width:320px;', 'label' => false, 'div' => false, 'id' => 'affil_name'));?>
  <br/>  <br/>
  <div class="submit"><input type="button" value="Submit" onclick = 'saveAffil();'></div>
  </fieldset>
    <br/>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>