<?php
	echo $this->Html->script('frontanimation');
	$this->set('title_for_layout', "Beer Pong | Official Beer Pong Tables, Rules, Games, and the World Series of Beer Pong | BPONG.COM");
	$this->set("meta_description", "Beer Pong Players Unite. Home of the World Series of Beer Pong - the largest beer pong tournament in the world. Find official beer pong tables, supplies, and information about beer pong, tournaments, news, and more at BPONG.COM.");
?>
<div class="homenew">

   <div class="show1">
    	<?php echo $this->element("home/carusel1");?>
   </div>

  <div class="touchblock">
<!--    <div class="color1" style="border-bottom:1px #ddcfc9 solid">

    <form method="post" action="http://bpong.list-manage.com/subscribe/post">
    <input type="hidden" value="c2c381054c301e1d6d03a86d9" name="u"/>
    <input type="hidden" value="37c8b44af4" name="id"/>

      <input type="text" value="Enter Email Address" id="MERGE0" name="MERGE0" />
      <button>GO</button>
      </form>
    </div>   -->
<!--<div class="color1" style="float:left">
     <table border="0" cellspacing="0" cellpadding="0" style="background:none; margin-bottom:0">
  <tr>
    <td style="font-size:10px; width:30px">Follow us on:</td>
    <td><a href="http://www.facebook.com/BPONG"><img src="<?php echo STATIC_BPONG?>/img/home/icon_facebook1.jpg" /></a> <a href="http://twitter.com/BPONG"><img src="<?php echo STATIC_BPONG?>/img/home/icon_tweet2.jpg" /></a> <a href="http://www.myspace.com/wsobp"><img src="<?php echo STATIC_BPONG?>/img/home/icon_3.jpg" /></a> </td>
    <td><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=d2ee3acd-77d6-4497-a5f9-9819c34c3288&amp;type=website&amp;style=rotate&amp;linkfg=%23e81409"></script></td>
  </tr>
</table>
     <img src="<?php echo STATIC_BPONG?>/img/home/icon_4.jpg" />
    </div>   -->
    <a style="display:block; background:#fff;" href="http://wsobp.eventbrite.com"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="http://www.facebook.com/bpong" width="216" height="303" show_faces="true" border_color="white" stream="false" header="false"></fb:like-box></a>
     </div>

  <div class="redline clear" style="margin:0px -15px 10px -15px; height:4px"></div>
  <div class="news">
    <h2>New&nbsp;Stuff</h2>
    <div style="width: auto;color:#8F8F8F !important;" class="minselect">All beer pong, all the time.</div>
    <div class="clear"></div>
    <div class="redline" style='position:relative;'></div>
    <?php  echo $this->element("home/new_stuff"); ?>
    <?php  /*echo $this->element("home/blogposts");*/ ?>
	</div>

  <div class="tourn">
     	<?php echo $this->element("home/tournaments");?>
  </div>
  <div class="clear" style="height:10px"></div>
  <div class="wsobpshow">
    <h2 style="width:50%">The WSOBP</h2>
    <div class="minselect" style="width:auto">Sign up deadline: &nbsp;<span class="red">Dec. 10</span></div>
    <div class="clear"></div>
    <div class="redline"></div>
     	<?php echo $this->element("home/carusel2");?>
    </div>

  <div class="forum">
    <h2>The Forum</h2>
    <div class="minselect" style="width: auto"><a href="/nation/beer-pong-forum/addtopic/beer-pong-in-general" target="_blank">Post</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/nation/beer-pong-forum" target="_blank">View All</a></div>
    <div class="clear"></div>
    <div class="redline"></div>
    <?php echo $this->element("home/forumposts");?>
<?php /*
<div class="adv"><script type="text/javascript"><!--
google_ad_client = "pub-4626544747480022";
google_ad_slot = "8033059000";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
*/?>
  </div>
  <div class="clear" style="height:20px"></div>
  <div class="aboutbpong">
    <h2 style="width:100%">About BPONG.COM</h2>
    <div class="clear"></div>
    <div class="redline"></div>
    <p>This site was created by beer pong players with a love of the sport of beer pong for the purpose of serving the needs of players and the beer pong community at large. Here you will find The World Series of Beer Pong, the beer pong store, WSOBP Satellite Tournaments, the beer pong forum, and more, much of which is yet to come.</p>
  </div>
  <div class="howtoplay">
    <h2 style="width:100%">How To Play</h2>
    <div class="clear"></div>
    <div class="redline"></div>
    <p><img src="<?php echo STATIC_BPONG?>/img/rules.jpg" /></p>
    <p style="width:100px">The Official Rules of &quot;The World Series&quot; of Beer Pong<br />
      <br />
      <a href="http://static.bpong.com/files/doc/wsobp-quick_rules.pdf">Abridged</a> &nbsp; | &nbsp; <a href="/wsobp/official-rules-of-the-world-series-of-beer-pong">Full</a></p>
    <p><img src="<?php echo STATIC_BPONG?>/img/howtoplay.jpg" /></p>
    <p style="padding-top:20px"><a href="/vault/general-beer-pong-rules">General Rules<br />
      for Beer Pong</a></p>
  </div>
</div>
<div class="footer_new">
</div>
