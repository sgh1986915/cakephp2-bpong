  <h1>The Official NBPL Rankings</h1>
    <br />
  Welcome to the Official NBPL Rankings. This is your one-stop shop for all the statistical data you could ever want about the sport of beer pong. The initial rankings, released in late 2011, were created from the massive amount of statistical data that we’ve acquired from running The World Series of Beer Pong. However, the rankings are a living, breathing organism, and starting in early 2012, all officially sanctioned BPONG events will be used in the calculation of these rankings.<br>
  <br>
<h2>  <a href="<?php echo MAIN_SERVER; ?>/rankings/allusers">View the Rankings</a></h2>
   <br>
      <h2>Basics of How the Ranking System Works</h2>       
      Our official rankings use a methodology that is similar to<a href="http://en.wikipedia.org/wiki/Elo_rating_system"> ELO</a>, 
      the ranking system that is used to rank players in Chess and many other sports. While the math behind ELO is somewhat 
      complicated, the concept is pretty simple: Every player has a ‘Rating’. This Rating goes up or down based on the results 
      of each game the player plays. The amount by which the Rating changes is dependent on what was ‘expected’ to happen in 
      the game, based on the Ratings each player had before the game began.<br>
      <br>
      What does this mean? Let’s say a really great chess player (who has a really high Rating) plays against a first time 
      player (who starts with a low Rating). Naturally, we would expect the better player to win most of the time. So, if 
      the better player does in fact win, it isn’t really a surprise, and therefore the Ratings of the players won’t change 
      very much. However, if the better player loses, that indicates that maybe he wasn’t as good as his Rating implied, 
      or maybe his opponent wasn't as bad as his Rating implied. As a result, the Ratings of both players are adjusted 
      by a much more significant amount.<br>
      <br>
      This system works well in practice. Over time, the Rating of a player will constantly be fluctuating up and down 
      by small amounts, but in theory, it should generally hover around a level that reasonably approximates 
      the actual ability of that player.<br>
      <br>
      
  <h2>How this differs from ELO</h2>
  ELO is an extremely popular system, used in many different types of applications. It’s versatility is derived from the fact that it only needs to know one thing about what happened in an event: which side was victorious. However, the sport of beer pong actually has a very important statistic available to it called ‘cup differential’. Not only do we know the winner of each game, but we also know ‘how much they won by’. Beating a team by five cups is a lot different than beating a team by two. The NBPL Rating System was designed specifically to incorporate this meaningful statistic into the calculation of player Ratings and Rankings. <br>
  <br>
<h2>How we came up with this system</h2>
    In order to design a ranking system specifically for beer pong, we ran millions of computer-simulated beer pong games 
    using the Official Rules of The World Series of Beer Pong. We used hypothetical players with varying degrees of skill
    (i.e. different shooting percentages). Based on this, we determined a set of ‘probability distributions’ of expected cup 
    differentials for beer pong games. With these distributions, we created formulas to approximate the ‘meaning’ of the 
    results of games. In other words, winning by 2 cups is obviously better than winning by 1 - we went and quantified 
    ‘how much better’ that result is. You can see some examples of this in the calculator below. <br>
    <br>
    <h2>How each game affects your Rating</h2>
    When a player players his first game in the Ranking system, he is assigned a Rating of 5000. Every time he plays a game, we take a look at two things:<br> <br>
  1. His Rating before the game began<br />
  2. The Average Ratings of his opponents before the game began.<br />
  <br>
  We take these numbers, and the results of the game (i.e. which team won and by how many cups), and put it through our formulas to determine the player’s 'rating change'. We then apply a ‘weight’ to the rating change, based upon the type of event the game was associated with.<br>
  <br>
  Below, you will find a calculator that will tell you how much your Rating will change after each game. You’ll notice 
  that sometimes, the results of a game will have zero effect on the Ratings of each player. This will happen when a very 
  high-rated team plays a very low-rated team, and only wins by 1 or 2 cups. If the world champions play a first-time team, 
  we would expect them to win by more than 2 cups. Therefore, it wouldn’t make sense for us to raise their rating as a result 
  of a game where they only won by one cup. On the other hand, we wouldn’t want them to lose points from a game they just won.
  Thus, the Rating Change in this case would be zero.<br>
  <br>
  <h2>Rating Calculator</h2>
      <?php echo $this->element('rankings/sampleratingcalc');?> 
  <br>
  <h2>Game Weighting</h2>
  Games are given the following weights in the Ranking System<br>
  <br>
  <table width="425">
<tr>
    	<td width="286">Type of Event</td>
      <td width="102" align="center">Weighting</td>
    </tr>
    <tr>
    	<td>The World Series of Beer Pong</td>
        <td align="center">100%</td>
    </tr>
	<tr>
        <td>BPONG Events with $5K-$25K in Prizes</td>
        <td align="center">60%</td>
   <tr>
   <tr>
         <td>WSOBP Satellite Tournaments</td>
        <td align="center">25%</td>
  </tr>  
   <tr>
         <td>Weekly BPONG Sanctioned Events</td>
        <td align="center">15%</td>
  </tr>           
  
  </table>
<br>
    <h2>Adjustment for Non-Participation</h2>
  For the Ranking system to provide accurate information about a players skill level, that player must be at least somewhat active in the beer pong community. Otherwise, a player could rise to the top of the rankings, stop playing, and stay at the top. <br/><br />
  Every year, to avoid a negative adjustment in their rating, a player must attend at least one of BPONG's larger events. As of December 2011, the only events that have qualified as such are <a href="<?php echo MAIN_SERVER; ?>/wsobp">The WSOBP</a> and <a href="<?php echo MAIN_SERVER; ?>/ReturnToMesquite">The Return to Mesquite</a>.<br />However, starting in 2012, this will include all tournaments sanctioned by BPONG offering prizes of at least $5,000. <br /><br />
  Each year, we adjust the Ratings of players who did not participate in any large events. This adjustment is calculated as follows:<br /><br />
  NewRating = OldRating - Max( (OldRating - 5000) * .2,0)<br/><br/>
  Example 1: Player had a Rating of 6000.<br /> 
  NewRating = 6000 - Max ( 1000 * .2, 0) = 5800<br /><br/>
  Example 2: Player had a Rating of 4800.<br />
  NewRating = 4800 - Max (-200 * .2, 0) = 4800<br />  
  
  
    <h2>How do I get ranked? </h2>

  Players are ranked based on every game they play in officially-sanctioned BPONG events. <a href="<?php echo MAIN_SERVER; ?>/wsobp">Sign up now for The World Series of Beer Pong VII</a> for your first chance to show the world how great you really are. Starting in 2012, there will be chances for you to move up the rankings in cities all across the world.  Own a Venue, and would like to bring the BPONG Ranking system to your events? Contact us <a href="<?php echo MAIN_SERVER;?>/contact">here</a>. 

