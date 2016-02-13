<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <title><?php $this->pageTitle = 'Projector of Tournament "' . $event['Event']['name'] . '" | BPONG.COM'; ?></title>
  <style>
  	* { margin:0; padding:0 }
  	html, body { height:100% }
  </style>
</head>
<body>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="100%" height="100%">
		<param name="quality" value="high" />
		<param name="allowFullScreen" value="true" />
		<PARAM NAME="SCALE" VALUE="default">
		<embed src="/tournamentprojector/Main.swf?eventid=<?php echo $eventID;?>" quality="high" type="application/x-shockwave-flash" width="100%" height="100%" SCALE="default" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object></body>
</html>