<?php 
	if ($winnings > 0 || $losses > 0):
?>	
<script type="text/javascript">
var chart;
$(document).ready(function() {
	var chart<?php echo $chartIndex;?>;
	$(document).ready(function() {
		chart<?php echo $chartIndex;?> = new Highcharts.Chart({
			chart: {
				backgroundColor:'transparent',
				borderRadius:0,
				width:50,
				height:50,
				renderTo: 'pie_chart<?php echo $chartIndex;?>',
				plotBackgroundColor: null,
				borderWidth:false,
				plotBorderWidth: null,
				plotShadow: false,
				events: {
					<?php if (!empty($chartLink)):?>
					click: function(e) {redirect('<?php echo $chartLink;?>');}
					<?php endif;?>
				}
			},
			credits: {
				enabled: false
			},
			title: {
				text: false
			},
			tooltip: {
				enabled:false,
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ this.y;
				}
			},	
			exporting: {
				enabled:false
			},	
			plotOptions: {
				pie: {
					enableMouseTracking: false,
					size:45,
					innerSize:0,
		            borderWidth: <?php if ($losses > 0 && $winnings > 0) { echo '2';} else { echo '0';}?>,
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					}
				}
			},
		    series: [{
				animation:false,
				type: 'pie',
				name: 'Browser share',
				data: [
					{
						name: 'Losses',    
						y: <?php echo intval($losses);?>,
						color: 'rgb(214,28,32)'
					},	
					{
						name: 'Winnings',    
						y: <?php echo intval($winnings);?>,
						color: 'rgb(27,114,0)',
					}				
				]
			}]
		});
	});

});
</script>
<div id="pie_chart<?php echo $chartIndex;?>" <?php if (!empty($chartLink)):?>style='cursor:pointer;'<?php endif;?>></div>
<?php endif;?>