<script type="text/javascript">

$(document).ready(function() {
	$.ajaxSetup({cache:false});
    $("#affil_city").autocomplete("/cities/autocomplete/", {
        		width: 320,
        		max: 7,
        		cache:false,
        		highlight: false,
        		multiple: false,
        		scroll: true,
        		scrollHeight: 300,
        		cacheLength:0,
        		extraParams: {
        		       country_id: function() {return $("#affil_country_id").val();},
   					   state_id: function() {return $("#affil_provincestate_id").val();}
        		}
    });
    $("#affil_name").autocomplete("/schools/autocomplete/", {
		width: 320,
		max: 7,
		cache:false,
		highlight: false,
		multiple: false,
		scroll: true,
		scrollHeight: 300,
		cacheLength:0,
		extraParams: {
		       country_id: function() {return $("#affil_country_id").val();},
			   state_id: function() {return $("#affil_provincestate_id").val();},
			   city: function() {return $("#affil_city").val();}			   
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
    var affilCountryID = $("#affil_country_id").val() * 1;
	var affilStateID = $("#affil_provincestate_id").val() * 1;
	var affilCity = $("#affil_city").val();
		
	if (!affilName) {
		alert('Please specify School Name');
		return false;
	}
	if (!affilCity) {
		alert('Please specify City');
		return false;
	}
	if (!affilCountryID) {
		alert('Please specify Country');
		return false;
	}
	if (!affilStateID) {
		alert('Please specify State');
		return false;
	}
	
	$('#users_affils_block').html(showLoaderHtml());	
	$.ajaxSetup({cache:false});
	$.post("/users_affils/save_school", { 'name': affilName, 'country_id': affilCountryID, 'state_id': affilStateID, 'city': affilCity},
			   function(data) {
				$('#users_affils_block').load('/users_affils/usersProfileBlock/<?php echo $userID;?>');	   
	});
	
	tb_remove();
}
</script>
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Select Your School</h1>
<div class="whitebg nopad">
  <fieldset style='font-size: 15px;color: #333333;line-height: 20px;width:90% !important;'>
  Country<br/>
  <?php echo $this->Form->input('Affil.country_id',array('id'=>'affil_country_id','type' => 'select','style'=>'width:190px;', 'label' => false,'options' => $countries));?>
  State<br/>
  <?php echo $this->Form->input('Affil.provincestate_id',array('id'=>'affil_provincestate_id','type' => 'select', 'label'=> false, 'style' => 'width:190px;','options' => $states));?>
  City<br/>
  <?php echo $this->Form->input('Affil.city', array('style' => 'width:320px;', 'label' => false, 'div' => false, 'id' => 'affil_city'));?>
  <br/>
  School<br/>
  <?php echo $this->Form->input('Affil.name', array('style' => 'width:320px;', 'label' => false, 'div' => false, 'id' => 'affil_name'));?>
  <br/>
  
  <br/>
  <div class="submit"><input type="button" value="Submit" onclick = 'saveAffil();'></div>
  </fieldset>
    <br/>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>