<script type="text/javascript">
function customLoadPiecePostdata1(href, divName, divPaging, divLoader,data) {
           $.ajaxSetup({cache:false});
           $("." + divLoader).html(showLoaderHtml());
           $.post(href,data,function(result) {
               $(divName).html(result);
               $("." + divLoader).html('');
               var divPaginationLinks = divName+" ." + divPaging +" a";
               $(divPaginationLinks).click(function() {
                   var thisHref = $(this).attr("href");
                   customLoadPiecePostdata1(thisHref, divName, divPaging, divLoader,data);
                   return false;
               });
           });
}

function customPreLoadPiecePostdata1(href, divName, divPaging, divLoader,data) {
       var divPaginationLinks = divName+" ." + divPaging +" a";
       $(divPaginationLinks).click(function() {
         var thisHref = $(this).attr("href");
           customLoadPiecePostdata1(thisHref, divName, divPaging, divLoader,data);
           return false;
       });
}

$(document).ready(function() {
    $('#RankingFilterFirstname').val("");
    $('#RankingFilterLastname').val(""); 
    $('#RankingFilterGender').val("");   
    customPreLoadPiecePostdata1("/rankings/allusersajax","#allusersajax",'paginationRankings','paginationLoader',
            {});
});

function handleFormSubmit() {
    $('#rankingsLoader').show();
    $('#allusersajax').html("");
    var dataToPost = {
       firstname:$('#RankingFilterFirstname').val(),
       lastname:$('#RankingFilterLastname').val(),
       gender:$('#RankingFilterGender').val()
    };
    
    $.post("<?php echo MAIN_SERVER.'/rankings/allusersajax';?>",dataToPost,function(data) {
        $('#rankingsLoader').hide();
        $('#allusersajax').html(data);
        customPreLoadPiecePostdata1("/rankings/allusersajax","#allusersajax",'paginationRankings','paginationLoader',
            dataToPost);            
    });
    return false;
}
</script>
<div> <?php echo $this->Form->create('Ranking',array('id'=>'RankingFilter','name'=>'RankingFilter','onsubmit'=>'return handleFormSubmit();','default'=>false));?>
  <h2>BPONG Player World Rankings - current as of <?php echo Date("M d, Y"); ?></h2>
      Total Players: <?php echo $numusers; ?><br />
      <a href="<?php echo MAIN_SERVER.'/rankings';?>">How are these rankings calculated?</a>
    <fieldset>
    <?php echo $this->Form->input('RankingFilter.gender',array('type' => 'select','label'=>'Gender','options' => array('Both'=>'Both','M'=>'M','F'=>'F')));?>
     <?php echo $this->Form->input('RankingFilter.firstname',array('label'=>'First Name'));?>
     <?php echo $this->Form->input('RankingFilter.lastname',array('label'=>'Last Name'));?>

    </fieldset>
<div class="clear"></div>
  <?php echo $this->Form->end('Filter');?>
</div>
<div id="rankingsLoader" style="display: none;">
    <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
</div>  
<div id='allusersajax'>
    <?php echo $this->requestAction('/rankings/allusersajax/0'); ?>
</div>  
<div class='paginationLoader' style='height:10px;' class='clear'></div>  
