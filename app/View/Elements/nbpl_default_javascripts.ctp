<script type="text/javascript">
var shown = 0;

	$(document).ready(function(){
		$('.tooltip').hover(function(){
			var th = $(this);
			var off = th.position();

			var info = th.next('.tooltip_info');
			if (info.html()) {
				info.css({top:off.top, left:off.left - 5});
				info.show();
				info.mouseleave(function(){
						info.hide();
						delete th;
						delete info;
						delete off;
				});
			}
		});
		$.ajaxSetup({ cache: false });
		<?php /*?>
		//Change all links
		$("#content a[href^='http:']").each(
		    function(){
			if(this.host!="www.bpong.com") {
			    $(this).attr("target","_blank");
			}
		});
		//EOF changing links
		<?php */ ?>


	        $('#GoogleSearch').focus(function(){if ($('#GoogleSearch').val()=="Search beerpong.com")$('#GoogleSearch').val('');});
			$('#GoogleSearch').blur(function(){if ($('#GoogleSearch').val()=="")$('#GoogleSearch').val('Search beerpong.com');});

			$('#MERGE0').focus(function(){if ($('#MERGE0').val()=="Enter Email Address")$('#MERGE0').val('');});
			$('#MERGE0').blur(function(){if ($('#MERGE0').val()=="")$('#MERGE0').val('Enter Email Address');});

	  	   <?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
	  	   		 	menuShow();
	  	   		 	menuHide();
	  	   <?php endif; ?>

			if($("#flashMessage").length) {
				$("#flashMessage").fadeTo(8000);
				$("#flashMessage").height($("#flashMessage").height() || 0).animate({height: "0",paddingTop:"0",paddingBottom:"0",margin:"0"}
											, 500
											, function() {$("#flashMessage").hide();}
				);
			}

	});
function menuShow(){
	 $('#MyBpong').bind("mouseenter",function(){
	    if ( shown == 0){
	         shown = 2;//in progress
	   		 $("#MyBpong").addClass('overmenu');
      		 $("#UserSubmenu").slideDown('medium',function(){ shown = 1;});

      	}
      });
	 $('#MyBpong').bind("click",function(){
		    if ( shown == 0){
		         shown = 2;//in progress
		   		 $("#MyBpong").addClass('overmenu');
	      		 $("#UserSubmenu").slideDown('medium',function(){ shown = 1;});

	      	} else {
		         shown = 2;//in progress
		   		 $("#MyBpong").removeClass('overmenu');
	      		 $("#UserSubmenu").slideUp('medium',function(){ shown = 0;});
			}
	      	return false;
	      });
}
function menuHide(){
	  $('#wrapper').bind("mouseout",function(over){
	    if (!$(over.target).hasClass("MyBpong")){
	        if ( shown == 1){
	        	 shown = 2;//in progress
	      		 $("#UserSubmenu").slideUp('medium',function(){$("#MyBpong").removeClass('overmenu'); shown = 0;});
	      	}
        }
      });
}
</script>
<!--[if lt IE 7]>
	<script type="text/javascript" src="/js/DD_belatedPNG.js"></script>
	<script type="text/javascript">
		DD_belatedPNG.fix('.wsowp_span_title, .nivo-prevNav, .nivo-nextNav, #closeBut,.nyroModalPrev,.nyroModalNext');
		//for ie6 png transparency
	</script>


	<link rel="stylesheet" type="text/css" href="/css/ie.css" />
	<script type="text/javascript">
	$(window).load(function () {
      if($("#content").height()<300) {
      		$("#content").height(300);
      }
    });

	</script>

<![endif]-->
<!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="/css/ie.css" />
<![endif]-->
<!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" href="/css/ie8.css" />
<![endif]-->

<script type="text/javascript">
if(window.opera) {
         document.write('<link rel="stylesheet" type="text/css" href="/css/opera.css" />');
}
</script>