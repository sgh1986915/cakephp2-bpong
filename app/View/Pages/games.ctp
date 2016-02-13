<link rel="STYLESHEET" type="text/css" href="<?php echo STATIC_BPONG?>/css/dhtmlxgrid.css">
<link rel="STYLESHEET" type="text/css" href="<?php echo STATIC_BPONG?>/css/dhtmlxgrid_skins.css">

<script  src="<?php echo STATIC_BPONG?>/js/jquery.js"></script>
<script  src="<?php echo STATIC_BPONG?>/js/dhtml/dhtmlxcommon.js"></script>
<script  src="<?php echo STATIC_BPONG?>/js/dhtml/dhtmlxgrid.js"></script>
<script  src="<?php echo STATIC_BPONG?>/js/dhtml/dhtmlxgridcell.js"></script>
<script src="<?php echo STATIC_BPONG?>/js/dhtml/excells/dhtmlxgrid_excell_sub_row.js"></script>
<script  src="<?php echo STATIC_BPONG?>/js/dhtml/jquery.jqURL.js"></script>

<body>
	<h2 style="clear:left;padding-top:10px;">Team Games</h2>
  <div id="teamGamesGrid"   width="626px"  height="420px"  style="background-color:white;"></div>
<script language="javascript">
	var teamID = $.jqURL.get("teamID");
</script>
<script>

	teamGamesGrid = new D('teamGamesGrid');
	teamGamesGrid.setImagePath("img/");
	teamGamesGrid.setHeader("#,Team Name,P/F,Opponent,Opp. Rank,W/L,OT?,CD");
	teamGamesGrid.setInitWidths("40,180,80,180,40,40,40,40");
	teamGamesGrid.setColAlign("center,center,center,center,center,center,center,center,center");
	teamGamesGrid.setColTypes("edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt,edtxt");
	teamGamesGrid.setColSorting("str,str,str,str,str,str,str,str,str");
	teamGamesGrid.rg(false,false,false);
	teamGamesGrid.init();
	teamGamesGrid.setSkin("light");
		
	teamGamesGrid.bD("<?php echo STATIC_BPONG?>/files/gamesOutput/"+teamID+".xml");
	
</script>