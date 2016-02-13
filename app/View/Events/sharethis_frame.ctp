<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<style type="text/css">
	body {
		margin: 0;
		background-color: transparent;
	}
</style>
<script type="text/javascript">
onscroll = function () {
    scroll(0,0);
}
</script>
<?php
echo $this->element('share_this', array(
	'shareUrl' => $this->Html->url(array('controller' => 'events', 'action' => 'view', $shareUrl), true),
	'shareTitle' => $shareTitle
)); ?>