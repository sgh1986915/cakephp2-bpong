<?php
	$this->pageTitle = "The World Series of Beer Pong (WSOBP) | Professional Beer Pong Tournaments for Big Money | BPONG.COM";
?>
<script type="text/javascript">

var CP={
  elems		:0,
  width		:0,
  left 		:0,
  fullWidth :0,
  init:function(){
    CP.elems = $(".panels >").length;
    CP.width = $(".panels > :last-child").width();
    CP.fullWidth = CP.elems*CP.width;
    $(".panels").width(CP.fullWidth);

    $("div.choose > div.tab").each(
 			function(i){
				$("a", this).click(function() {
				   CP.moveSlide(i*CP.width); return false;
				})
			}
    );
  },
  next: function() {
    CP.left += CP.width;
    CP.moveSlide(CP.left);
  },
  prev: function() {
    CP.left -= CP.width;
    CP.moveSlide(CP.left);
  },
  moveSlide: function(left) {
    CP.removeActive();
    if(left>(CP.fullWidth-CP.width)) left -=CP.fullWidth;
    if(left<=-CP.width) left +=CP.fullWidth;
    $(".panels").animate({left:"-"+left+"px"},"slow","easeboth");
    CP.left = left;
    CP.setActive();
    return false;
  },

  removeActive: function() {
    $("div.choose > div.tab").each(function() { $("a", this).removeClass("activeone")});
  },

  setActive: function() {
    i = Math.ceil(CP.left/CP.width);
    $("a", $("div.choose > div.tab")[i]).addClass("activeone");
  }
};

var CPS = {
  elems		:Array(0,0,0),
  width     :Array(0,0,0),
  left 		:Array(0,0,0),
  fullWidth :Array(0,0,0),
  slides    :Array(),
  i	:0,
  init:function(){

     $(".panels >").each(function(i) {
        CPS.i = i;
        CPS.slides.push(this);
        CPS.elems[i] = $(".thumb_panels >", this).length;
        CPS.width[i] = 71;
        CPS.fullWidth[i] = CPS.width[i]*CPS.elems[i];
        $(".thumb_panels", this).width(CPS.fullWidth[i]);

        //check to get sure if we need to show pointers
        if(Math.ceil(($(this).width()-100)/CPS.width[i])<CPS.elems[i]) { //yes, we need
           $($(".thumb_past > .pointer > a", this)[0]).click( function() {
               CPS.prev(i);
            });
           $($(".thumb_past > .pointer > a", this)[1]).click( function() {
               CPS.next(i);
            });
        } else {  //no, we needn't. Let's hide it
           $(".thumb_past > .pointer", this).each(function() { $(this).hide(); });
        }
     });
  },
  next: function(i) {
    CPS.left[i] += CPS.width[i];
    CPS.moveSlide(CPS.left[i], i);
  },
  prev: function(i) {
    CPS.left[i] -= CPS.width[i];
    CPS.moveSlide(CPS.left[i], i);
  },
  moveSlide: function(left, i) {
    shift = (CPS.fullWidth[i]-(CPS.width[i]*11));
    if(left>=shift)
      left = 1;
    if(left<=0)
      left +=shift;
    $(".thumb_panels", CPS.slides[i]).animate({left:"-"+left+"px"},"fast","easeboth");
    CPS.left[i] = left;
    return false;
  }
};

var TL={
  images    :Array(),
  imgObjects: Array(),
  bigThumb  :0,
  bigThumbA  :0,
  loadImage : function() {

			$('img', TL.bigThumbA).attr('src', this.src);
			TL.bigThumb.removeClass('loader');
      		TL.bigThumbA.fadeIn();
      		TL.images.push(this.src);
      		TL.imgObjects.push(this);
  },
  clickProcess: function() {
  			     //getting image src
			      src = $(this).attr('src');
			      bigSrc = src.replace(/thumb_/,"big_");
			      src = src.replace(/thumb_/,"");

			      currentSlide = $(this).parent().parent().parent().parent(".past_gray");
			      TL.bigThumb = $('.big-thumb', currentSlide);
			      TL.bigThumbA = $('a', TL.bigThumb);


			      if($('img', TL.bigThumbA).attr('src')==src) return false; //avoiding image reloading

			      if($.inArray(src, TL.images)<0) {
			         //setting loader spinner
			          TL.bigThumbA.hide();
			          TL.bigThumb.addClass('loader');
			          TL.bigThumbA.attr("href", bigSrc);
			           //loading images
			          var img = new Image();
			          $(img)
			       	      .load(TL.loadImage)
 			           	  .error(function () { /*do something to handle error*/   })
 			              .attr('src', src);
		       	  } else {

					  $('img', TL.bigThumbA).attr('src', src);
					  TL.bigThumbA.fadeIn("fast");

		       	  };

  },
  init: function() {
      	  $(".thumb_past > div > .thumb_panels > img").each(
	      		function() {
	      		    $(this).hover(
	      		   	     function() { $(this).addClass("hoverd");},
	         			 function() { $(this).removeClass("hoverd");}
	      		   );

	      		   $(this).click(TL.clickProcess);
        });
  }
};

$(document).ready(function() {
	  CP.init();
	  TL.init();
	  CPS.init();
});

</script>
<div class="choose">
<div style="padding:30px 10px 0px 240px; width:38px; float:left">
  <a href="javascript:CP.prev();" style="text-decoration:none; border:none">
    <img src="<?php echo STATIC_BPONG?>/img/wsobp/pointer_l.gif" alt="more" />
  </a>
</div>

<div style="width:80px; float:left; line-height:12px" class="tab">
  <a href="#wsobp-i" class="activeone"><img src="<?php echo STATIC_BPONG?>/img/wsobp/past_2006.gif" alt="wsobp06" /></a><br />
  <a href="#wsobp-i">
  <span style="font-size:11px; font-weight:bold; color:#1e1e1e">WSOBP I</span><br />
  <span style="font-size:14px; font-weight:bold; color:#d61c20">2006</span>
  </a>
</div>
<div style="width:80px; float:left; line-height:12px" class="tab">
	<a href="#wsobp-ii"><img src="<?php echo STATIC_BPONG?>/img/wsobp/past_2007.gif" alt="wsobp07" /></a><br />
	<a href="#wsobp-ii">
	<span style="font-size:11px; font-weight:bold; color:#1e1e1e">WSOBP II</span><br />
	<span style="font-size:14px; font-weight:bold; color:#d61c20">2007</span>
	</a>
</div>


<div style="width:80px; float:left; line-height:12px" class="tab">
    <a href="#wsobp-iii"><img src="<?php echo STATIC_BPONG?>/img/wsobp/past_2008.gif" alt="wsobp08" /></a><br />
	<a href="#wsobp-iii"><span style="font-size:11px; font-weight:bold; color:#1e1e1e">WSOBP III</span><br />
	<span style="font-size:14px; font-weight:bold; color:#d61c20">2008</span>
	</a>
</div>

<div style="padding:30px 0 0 10px; width:38px; float:left">
 <a href="javascript:CP.next();" style="text-decoration:none">
    <img src="<?php echo STATIC_BPONG?>/img/wsobp/pointer_r.gif" alt="more" />
 </a>
</div>

<div class="clear"></div>
</div>
<div class="tab-container">
 <div class="panels" style="overflow: visible; display: block; left: 0px;">
	<div id="wsobp-i" class="past_gray">
	<div style="width:530px; background:#FFF; float:left;">

	<div class="chump">
	<span style="color:#d61c20;font-weight:bold; font-size:13px">WSOBP I Champions:</span><br />
	<span style="color:#0e0e0e; font-weight:bold; font-size:13px">Team "France"</span><br />
	<span>Jason Coben &and; Nick Vellisaris, Ann Arbor, MI</span>
	</div>

	<div style="width:270px; text-align:center; float:left; padding-top:30px"><p style=" font-size:24px; font-weight:bold">WSOBP I<br />
	</p>
	<p style="">January 1-5, 2006</p>
	</div>
	<br />
	<div style="clear:both; padding:15px 23px"><p style="font-size:14px; font-weight:bold">The Oasis Casino, Mesquite, NV &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$10,000 Grand Prize</p>
	<p style="font-size:12px">Featuring nearly 600 players from 43 U.S. States and Canada, hundreds of spectators, and more than double the prize money of the previous year, WSOBP III was a tremendous expansion over previous years.  It was also the first WSOBP to be held within Las Vegas proper - just a few miles south of the famous Las Vegas Strip.
	<br />
	<br />
	The event was full of surprises, but the predominant highlight of WSOBP III came at the final match, featuring  Team Chauffeuring the Fat Kid of San Diego, CA vs Three-time WSOBP-alumni The Iron Wizard Coalition of Albany, NY.  Chauffeuring managed to hit 4 cups in a row to force overtime in a redemption situation in an elimination round, forcing an extra game, which they won, taking home the $10,000 prize.  <a href="#">more >></a></p>
	<p class="actions" style="padding-top:30px"><a href="#">Final Rankings</a> &nbsp; |&nbsp; <a href="#">Stats Breakdown</a> &nbsp; | &nbsp;<a href="#">Photo &and; Videos</a></p>
	</div>
	</div>
	<div class="big-thumb"><a href="<?php echo STATIC_BPONG?>/img/wsobp/06/big_01.jpg" class="thickbox"><img src="<?php echo STATIC_BPONG?>/img/wsobp/06/01.jpg" alt="WSOBP1" /></a></div>
	<div class="clear"></div>
	<div class="thumb_past">
    <div class="pointer"><a style="text-decoration:none"><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2.gif" alt="more" /></a></div>
    <div style="overflow:hidden; float:left; width:710px; position:relative">
      <div class="thumb_panels" style="left:0px; overflow: visible; width: 1250px;">
    	<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_01.jpg" alt="01" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_02.jpg" alt="02" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_03.jpg" alt="03" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_04.jpg" alt="04" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_05.jpg" alt="05" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_06.jpg" alt="06" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_07.jpg" alt="07" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_08.jpg" alt="08" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_09.jpg" alt="09" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_10.jpg" alt="10" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_11.jpg" alt="11" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_06.jpg" alt="06" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_07.jpg" alt="07" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_08.jpg" alt="08" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_09.jpg" alt="09" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_10.jpg" alt="10" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/06/thumb_11.jpg" alt="11" />
	  </div>
    </div>
    <div  class="pointer"><a style="text-decoration:none"><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2_r.gif" alt="more" /></a></div>
	</div>
	</div>
	<div id="wsobp-ii"  class="past_gray">
	<div style="width:530px; background:#FFF; float:left;">

	<div class="chump">
	<span style="color:#d61c20;font-weight:bold; font-size:13px">WSOBP II Champions:</span><br />
	<span style="color:#0e0e0e; font-weight:bold; font-size:13px">Team "We Own Your Face"</span><br />
	<span>Antonio Vassilatos &and; Niel Gurriero, Clifton. NJ</span>
	</div>

	<div style="width:270px; text-align:center; float:left; padding-top:30px">
	<p style=" font-size:24px; font-weight:bold">WSOBP II<br />
	</p>
	<p style="">January 1-5, 2007</p>
	</div>
	<br />
	<div style="clear:both; padding:15px 23px"><p style="font-size:14px; font-weight:bold">The Oasis Casino, Mesquite, NV &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$20,000 Grand Prize</p>
	<p style="font-size:12px"><a href="#">more >></a></p>
	<p class="actions" style="padding-top:30px"><a href="#">Final Rankings</a> &nbsp; |&nbsp; <a href="#">Stats Breakdown</a> &nbsp; | &nbsp;<a href="#">Photo &and; Videos</a></p>
	</div>
	</div>
	<div class="big-thumb" ><a class="thickbox" href="<?php echo STATIC_BPONG?>/img/wsobp/07/big_01.jpg"><img src="<?php echo STATIC_BPONG?>/img/wsobp/07/01.jpg" alt="WSOBP2" /></a></div>
	<div class="clear"></div>
	<div class="thumb_past">
    <div class="pointer"><a><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2.gif" alt="more" /></a></div>
    <div style="overflow:hidden; float:left; width:710px; position:relative">
      <div class="thumb_panels" style="left:0px; overflow: visible; width: 1250px;">
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_01.jpg" alt="01" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_02.jpg" alt="02" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_03.jpg" alt="03" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_04.jpg" alt="04" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_05.jpg" alt="05" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_06.jpg" alt="06" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_07.jpg" alt="07" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_08.jpg" alt="08" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_09.jpg" alt="09" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_10.jpg" alt="10" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_11.jpg" alt="11" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_01.jpg" alt="01" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_02.jpg" alt="02" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_03.jpg" alt="03" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/07/thumb_04.jpg" alt="04" />
	</div>
</div>
    <div  class="pointer"><a style="text-decoration:none"><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2_r.gif" alt="more" /></a></div>
	</div>
	</div>

	<div id="wsobp-iii" class="past_gray">
	<div style="width:530px; background:#FFF; float:left;">

	<div class="chump">
	<span style="color:#d61c20;font-weight:bold; font-size:13px">WSOBP III Champions:</span><br />
	<span style="color:#0e0e0e; font-weight:bold; font-size:13px">Team Chaufeuring The Fat Kid</span><br />
	<span>Jeremy Hughes &and; Michael Orr, San Diego, CA</span>
	</div>

	<div style="width:270px; text-align:center; float:left; padding-top:30px"><p style=" font-size:24px; font-weight:bold">WSOBP III<br /></p>
	<p style="">January 1-5, 2008</p>
	</div>
	<br />
	<div style="clear:both; padding:15px 23px"><p style="font-size:14px; font-weight:bold">South Point Casino, Las Vegas, NV &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$50,000 Grand Prize</p>
	<p style="font-size:12px">Featuring nearly 600 players from 43 U.S. States and Canada, hundreds of spectators, and more than double the prize money of the previous year, WSOBP III was a tremendous expansion over previous years.  It was also the first WSOBP to be held within Las Vegas proper - just a few miles south of the famous Las Vegas Strip.
	<br />
	<br />
	The event was full of surprises, but the predominant highlight of WSOBP III came at the final match, featuring  Team Chauffeuring the Fat Kid of San Diego, CA vs Three-time WSOBP-alumni The Iron Wizard Coalition of Albany, NY.  Chauffeuring managed to hit 4 cups in a row to force overtime in a redemption situation in an elimination round, forcing an extra game, which they won, taking home the $50,000 prize.  <a href="#">more >></a></p>
	<p class="actions" style="padding-top:30px"><a href="#">Final Rankings</a> &nbsp; |&nbsp; <a href="#">Stats Breakdown</a> &nbsp; | &nbsp;<a href="#">Photo &and; Videos</a></p>
	</div>
	</div>
	<div class="big-thumb"><a href="<?php echo STATIC_BPONG?>/img/wsobp/08/big_01.jpg" class="thickbox"><img src="<?php echo STATIC_BPONG?>/img/wsobp/08/01.jpg" alt="WSOBP2" /></a></div>
	<div class="clear"></div>
	<div class="thumb_past">
    <div class="pointer"><a><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2.gif" alt="more" /></a></div>
    <div style="overflow:hidden; float:left; width:710px; position:relative">
      <div class="thumb_panels" style="left:0px; overflow: visible; width: 1250px;">
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_01.jpg" alt="01" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_02.jpg" alt="02" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_03.jpg" alt="03" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_04.jpg" alt="04" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_05.jpg" alt="05" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_06.jpg" alt="06" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_07.jpg" alt="07" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_08.jpg" alt="08" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_09.jpg" alt="09" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_10.jpg" alt="10" />
		<img src="<?php echo STATIC_BPONG?>/img/wsobp/08/thumb_11.jpg" alt="11" />
	  </div>
	</div>
    <div  class="pointer"><a style="text-decoration:none"><img src="<?php echo STATIC_BPONG?>/img/wsobp/point2_r.gif" alt="more" /></a></div>
    </div>
	</div>
  </div>
</div>
