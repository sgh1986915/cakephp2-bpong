<script type="text/javascript">
var startTimeOfNextRound = "<?php echo $startTimeOfNextRound; ?>";
var currentRound = "<?php echo $currentRound; ?>";
var lastUpdateTime = "<?php echo $currentDateTime; ?>";
var count = 0;
$(document).ready(function() {
    window.setInterval(refreshTime, 1000);   
});

function refreshData() {
    $.post("<?php echo MAIN_SERVER.'/events/currentstatus/'.$event['Event']['id'].'/1';?>" 
    ,function(data) {
        startTimeOfNextRound = data["startTimeOfNextRound"];
        currentRound = data["currentRound"];
        lastUpdateTime = data["currentDateTime"];
        refreshTime();
    } 
    );
    
} 
function refreshTime() {
    
    if (count >= 10) {
        refreshData();
        count=0;
    }
    count++;
    
    var currentTime = new Date();
    var currentSeconds = currentTime.valueOf() / 1000;               
    //var timeTillNext = startTimeOfNextRound - currentTime.UTC();
    var timeTillNext =  startTimeOfNextRound - currentSeconds;
    
    var minutes = Math.floor(timeTillNext / 60);
    var seconds = Math.floor(timeTillNext - minutes * 60);
    var secondsString = seconds.toString();
    if (seconds == 0)
        secondsString = "00";
    else if (seconds < 10)
       secondsString = "0"+seconds.toString();
    var displayString = "Time until Next Round: " + minutes.toString()+":"+secondsString;
    
    $('#timeTillNextDiv').html(displayString);                  
    $('#currentDateDiv').html("Current Round: "+currentRound);
    $('#updateTime').html("<b>This is accurate as of "+ lastUpdateTime + "</b>");
}
</script>
<h2><?php echo $event['Event']['name']; ?></h2>
<div id="currentDateDiv">Current Round: </div>   
<div id="timeTillNextDiv">Time until Next Round:</div>
<div id="updateTime"><b>This is accurate as of </b></div>
<br /><br />
<a href="<?php echo MAIN_SERVER.'/viewbrackets/'.$event['Event']['id']; ?>">Click here to View Current Standings and Brackets</a>