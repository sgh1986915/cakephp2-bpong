<script type="text/javascript">
$(document).ready(function() {
   $("#RankingInputPlayerRating").change(function(){ return calculateRating();  });
$("#RankingInputOpponentsAverageRating").change(function(){ return calculateRating();  });
$("#RankingInputWinner").change(function(){ return calculateRating();  });
$("#RankingInputCupdif").change(function(){ return calculateRating();  });
calculateRating();
});
function calculateRating(){
     $('#Loading').show();
     $('#CalculatedResult').hide('fast');
    
    $.post('/rankings/calculateRatingChange',{
        'data[Ranking][player_rating]':$('#RankingInputPlayerRating').val(),
        'data[Ranking][opponents_average_rating]':$('#RankingInputOpponentsAverageRating').val(),
        'data[Ranking][winner]':$('#RankingInputWinner').val(),
        'data[Ranking][cupdif]':$('#RankingInputCupdif').val()},
        function(responseText) {
             $('#Loading').hide();
              $("#SubmitButton").show();

             $('#CalculatedResult').html(responseText);
             $('#CalculatedResult').show();  
        });
    
    return false;
}
</script>
This will let you see the effect of games on your rating. This assumes a game weighting of 100% (i.e. The WSOBP). Any game that goes to overtime is treated as having a cup differential of 1.
<fieldset>
    <?php
        echo $this->Form->input('RankingInput.player_rating', array('label' => 'Player Rating','value'=>5000));
        echo $this->Form->input('RankingInput.opponents_average_rating', 
            array('label' => 'Opponents Avg Rating',
                'value'=>5000));
                
        echo $this->Form->input('RankingInput.winner',
            array('type' => 'select', 
                'label' => 'Result',
                'value'=>1,
                'options' => array(-1 => 'Choose',0=>'Lost',1=>'Won')));
    
        echo $this->Form->input('RankingInput.cupdif',array(
            'type'=>'select',
            'value'=>1,
            'options'=>$possibleCupDifs,
            'label'=>'Cup Dif'));
        ?>
            <div id="CalculatedResult" style="display: none; margin: 0px 100px;"><!-- Ajax  --></div>
</fieldset>
    <div id="Loading" style="display:none; margin: 0px 100px;">
        <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?>
    </div>
    <div class="heightpad"></div>
    