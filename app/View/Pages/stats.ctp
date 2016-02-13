<?php $this->pageTitle = "WSOBP Stats"; ?>
<script type="text/javascript" src="<?php echo MAIN_SERVER?>/stats/codebase/jquery-latest.js"></script>
<script type="text/javascript" src="<?php echo MAIN_SERVER?>/stats/codebase/jquery.jqURL.js"></script>
<link rel="STYLESHEET" type="text/css" href="<?php echo MAIN_SERVER?>/stats/codebase/dhtmlxgrid.css" />
<script src="<?php echo MAIN_SERVER?>/stats/codebase/dhtmlxcommon.js"></script>
<script src="<?php echo MAIN_SERVER?>/stats/codebase/dhtmlxgrid.js"></script>
<script src="<?php echo MAIN_SERVER?>/stats/codebase/dhtmlxgridcell.js"></script>

<link rel="STYLESHEET" type="text/css" href="<?php echo MAIN_SERVER?>/stats/codebase/dhtmlxgrid_skins.css">
<script src="<?php echo MAIN_SERVER?>/stats/codebase/excells/dhtmlxgrid_excell_sub_row.js"></script>
	<div style="font-family:Arial, Helvetica, sans-serif;font-size:11px;line-height:14px;">
		<div style="float:left;padding-right:20px">
		    <strong>FR</strong> = Final Rank<br />
		    <strong>PR</strong> = Rank after Prelims<br />
		    <strong>PW</strong> = Prelim Wins<br />
		    <strong>PL</strong> = Prelim Losses<br />
		</div>
		<div style="float:left;padding-right:20px">
		    <strong>PCD</strong> = Prelim Cup Differential<br />
		    <strong>FW</strong> = Finals Wins<br />
		    <strong>FL</strong> = Finals Losses<br />
		    <strong>TW</strong> = Total Wins<br />
		</div>
		<div style="float:left">
		    <strong>TL</strong> = Total Losses<br />
		    <strong>WL %</strong> = Total Win/Loss Percentage<br />
		</div>
	</div>
<!-- WSOBP VI -->
<div class='clear'></div>
<div style='width:100%;margin-top:5px;'>
  <div style='float:left;margin-right:20px;'><h2 style="clear:left;padding-top:10px;">WSOBP VI Stats</h2></div>
  <div style='float:left;margin-top:25px;'><?php echo $this->element('facebook_like');?></div>
  <br class='clear'/>
</div>  
  <strong>Final Bracket:</strong> <a href="/stats/wsobpvi/WSOBP VI Final Brackets.pdf">Click here</a> to download the final brackets from Day 3.<BR /><BR />
<strong>Final Standings:</strong> <a href="/stats/wsobpvi/WSOBP VI Final Standings.pdf">Click here</a> to download the Final Standings from Day 3.<BR /><BR />
  <div id="wsobpVIgrid" height="500px" style="background-color:white;width:100%;"></div>
  
<!-- WSOBP V -->
<h2 style="clear:left;padding-top:10px;">WSOBP V Stats</h2>
 <strong>Final Bracket:</strong> <a href="/stats/wsobpv/wsobp_v_bracket.pdf">Click here</a> to download the final bracket from Day 3.<BR /><BR />
 <strong>Singles Tournament:</strong> <a href="/stats/wsobpv/singles_results.pdf">Click here</a> to download the results of the Singles Tournament from Day 1.<BR /><BR />

 <div id="wsobpVgrid" height="500px" style="background-color:white;width:100%;"></div>

<!-- EOF WSOBP IV -->
<h2 style="clear:left;padding-top:10px;">WSOBP IV</h2>
<div id="wsobpIVgrid" width="100%" height="500px" style="background-color:white;"></div>





  <h2 style="clear:left;padding-top:10px;">WSOBP III</h2>
  <div id="wsobpIIIgrid" width="100%" height="500px" style="background-color:white;"></div>


  <h2 style="padding-top:10px;">WSOBP II</h2>
  <div id="wsobpIIgrid" width="100%" height="500px" style="background-color:white;padding-bottom:20px"></div>

  <h2 style="padding-top:10px;">WSOBP I</h2>
  <div id="wsobpIgrid" width="100%" height="500px" style="background-color:white;padding-bottom:20px"></div>


<script>



	var wsobpVIgrid = new dhtmlXGridObject('wsobpVIgrid');
	wsobpVIgrid.setImagePath("http://www.bpong.com/stats/img/");
	wsobpVIgrid.setHeader("+,Rank,Team Name,P1,P1S,P2,P2S,W,L,CD");
	wsobpVIgrid.setInitWidths("35,40,300,50,50,50,40,30,30,40");
	wsobpVIgrid.setColAlign("center,left,center,center,center,center,center,center,center,center");
	wsobpVIgrid.setColTypes("sub_row,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	
	wsobpVIgrid.setColSorting("na,int,str,str,str,str,int,int,int");
	wsobpVIgrid.setEditable(false);
	wsobpVIgrid.init();
	wsobpVIgrid.setSkin("light");
	wsobpVIgrid.loadXML("/stats/wsobpvi/wsobpVIallrecords.xml");


	var wsobpVgrid = new dhtmlXGridObject('wsobpVgrid');
	wsobpVgrid.setImagePath("http://www.bpong.com/stats/img/");
	wsobpVgrid.setHeader("+,Rank,Team Name,P1,P1S,P2,P2S,W,L,CD");
	wsobpVgrid.setInitWidths("35,40,300,50,50,50,40,30,30,40");
	wsobpVgrid.setColAlign("center,left,center,center,center,center,center,center,center,center");
	wsobpVgrid.setColTypes("sub_row,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	
	wsobpVgrid.setColSorting("na,int,str,str,str,str,int,int,int");
	wsobpVgrid.setEditable(false);
	wsobpVgrid.init();
	wsobpVgrid.setSkin("light");
	wsobpVgrid.loadXML("/stats/wsobpv/wsobpVallrecords.xml");



	var wsobpIgrid = new dhtmlXGridObject('wsobpIgrid');
	wsobpIgrid.setImagePath("<?php echo MAIN_SERVER?>/stats/codebase/img/");
	wsobpIgrid.setHeader("Final Rank,Team Name,PR,Player 1,S1,Player 2,S2,PW,PL,PCD,FW,FL,TW,TL,WL %");
	wsobpIgrid.setInitWidths("70,150,30,120,30,120,30,30,30,30,30,30,30,30,55");
	wsobpIgrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
	wsobpIgrid.setColTypes("edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	wsobpIgrid.setColSorting("int,str,int,str,int,str,int,int,int,int,int,int,int,int,int");
	wsobpIgrid.setEditable(false);
	//wsobpIgrid.attachHeader(" ,#text_search,#cspan,#text_search,#cspan,#text_search,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
	//wsobpIgrid.enableSmartRendering(true);
	wsobpIgrid.enableMultiline(true);
	wsobpIgrid.init();
	wsobpIgrid.setSkin("light")
	wsobpIgrid.loadXML("<?php echo MAIN_SERVER?>/stats/wsobp_i_stats.xml");

	var wsobpIIgrid = new dhtmlXGridObject('wsobpIIgrid');
	wsobpIIgrid.setImagePath("<?php echo MAIN_SERVER?>/stats/img/");
	wsobpIIgrid.setHeader("Final Rank,Team Name,PR,Player 1,S1,Player 2,S2,PW,PL,PCD,FW,FL,TW,TL,WL %");
	wsobpIIgrid.setInitWidths("70,150,30,120,30,120,30,30,30,30,30,30,30,30,55");
	wsobpIIgrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
	wsobpIIgrid.setColTypes("edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	wsobpIIgrid.setColSorting("int,str,int,str,int,str,int,int,int,int,int,int,int,int,int");
	wsobpIIgrid.setEditable(false);
	//wsobpIIgrid.attachHeader(" ,#text_search,#cspan,#text_search,#cspan,#text_search,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
	//wsobpIIgrid.enableSmartRendering(true);
	//wsobpIIgrid.enableMultiline(true);

	wsobpIIgrid.init();
	wsobpIIgrid.setSkin("light")
	wsobpIIgrid.loadXML("<?php echo MAIN_SERVER?>/stats/wsobp_ii_stats.xml");


	var wsobpIIIgrid = new dhtmlXGridObject('wsobpIIIgrid');
	wsobpIIIgrid.setImagePath("<?php echo MAIN_SERVER?>/stats/img/");
	wsobpIIIgrid.setHeader("Final Rank,Team Name,PR,Player 1,S1,Player 2,S2,PW,PL,PCD,FW,FL,TW,TL,WL %");
	wsobpIIIgrid.setInitWidths("70,150,30,120,30,120,30,30,30,30,30,30,30,30,55");
	wsobpIIIgrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
	wsobpIIIgrid.setColTypes("edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	wsobpIIIgrid.setColSorting("int,str,int,str,int,str,int,int,int,int,int,int,int,int,int");
	wsobpIIIgrid.setEditable(false);
	wsobpIIIgrid.init();
	wsobpIIIgrid.setSkin("light")
	wsobpIIIgrid.loadXML("<?php echo MAIN_SERVER?>/stats/wsobp_iii_stats.xml");
	
	var wsobpIVgrid = new dhtmlXGridObject('wsobpIVgrid');
	wsobpIVgrid.setImagePath("<?php echo MAIN_SERVER?>/stats/img/");
	wsobpIVgrid.setHeader("+,Final Rank,Team Name,W,L,Prelim CD,Prelim Rank");
	wsobpIVgrid.setInitWidths("35,100,250,30,30,90,90");
	wsobpIVgrid.setColAlign("center,left,center,center,center,center,center");
	wsobpIVgrid.setColTypes("sub_row,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	wsobpIVgrid.setColSorting("na,int,str,int,int,int,int");
	wsobpIVgrid.setEditable(false);
	wsobpIVgrid.init();
	wsobpIVgrid.setSkin("light");
	wsobpIVgrid.loadXML("<?php echo MAIN_SERVER?>/stats/wsobpIVallrecords.xml");
</script>
