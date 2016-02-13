<?php $this->set('title_for_layout', 'The NBPL Handicapping System');?>
<h1>The NBPL Handicapping System</h1><br />
<h2>Summary</h2>
One of the fundamental pillars of the National Beer Pong League is that it should be fun for players of 
a wide range of skill and experience level. If a new player shows up to an event, and gets demolished, 
the experience can be demoralizing, and can turn that player away from organized beer pong. However, if 
all players are allowed to be competitive in every game the play, it will make the experience more enjoyable
for everyone. With this in mind, we created a handicapping system that helps level the playing field for
certain types of NBPL events.<br/><br/>
<h2>When is the handicapping system used?</h2>
The NBPL handicapping system is a great way to make beer pong tournaments more fun. However, it's not always appropriate 
for every event. For this reason, we have made the NBPL system flexible. <strong>Many NBPL events will not use the 
system. However, all weekly NBPL events will use the handicapping system.</strong> In addition, all Satellite Tournaments
that are generated from weekly events will use the handicapping system. These are the Satellite Tournaments that
have *no* buy-in fee - the only way to enter them is by winning a weekly event. However, there will still be 
WSOBP Satellite Tournaments that do not use the handicapping system. The use of handicapping for these events 
should be clearly indicated by the tournament organizer.<br/><br/>
<h2>The NBPL Ranking System</h2>
The handicapping system is built upon the NBPL Ranking system. In the ranking system, every player has an NBPL 'Rating',
which is a number that reasonably reflect that players skill level. 
<a href="<?php echo MAIN_SERVER; ?>/rankings">Click here for more information on the NBPL Ranking system</a>.<br/><br/>
<h2>Handicapping by removing cups</h2>
When a strong team plays a weak team, the handicapping system works by removing cups from the side of the strong
team. The cups are removed starting at the front of the rack, going left to right (from the perspective of the 
team at that side of the table). The number of cups removed is dependent upon the difference in average player
ratings of each team (see below for more detail).<br/><br/>
<h2>How to calculate the handicap</h2>
The handicap for each game is determined as follows:<br/><br/>
1) First, for each team, determine the 'average' rating of the team's players. For instance, if Player 1 has a 
rating of 5000, and Player 2 has a rating of 6000, then the Team's average rating is 5500.<br/><br/>
2) Determine the difference of the two team's average ratings. If Team 1 has an average rating of 4500, and
Team 2 has an average rating of 5500, the difference is 1000.<br/><br/>
3) Use the following chart to determine the handicap:<br/>
<table>
<tr>
    <td>Difference</td>
    <td>Handicap (cups)</td>
</tr>
<tr>
    <td>0-150</td>
    <td>0 cups</td>
</tr>
<tr>
    <td>150-300</td>
    <td>1 cup</td>
</tr>    
<tr>
    <td>300-600</td>
    <td>2 cups</td>
</tr>
<tr>
    <td>600-1000</td>
    <td>3 cups</td>
</tr>
<tr>
    <td>1000+</td>
    <td>4 cups</td>
</tr>        
</table><br/>
<h2>The NBPL Tournament Management Software</h2><br/>
The NBPL Tournament Management Software is specifically designed to coordinate the NBPL 
Handicapping System. The software downloads and updates all player ratings, and then calculates
the handicap for each game. The NBPL Tournament Management Software is available to all 
bars that take part in the <a href="<?php echo MAIN_SERVER; ?>/about_nbpl_bar_program">National Beer Pong League Bar Program</a>, as well as all operators of
<a href="<?php echo MAIN_SERVER;?>/wsobp/world-series-of-beer-pong-satellite-tournaments">WSOBP Satellite Tournaments</a>.
