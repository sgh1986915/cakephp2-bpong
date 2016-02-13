<?php if (!empty($userChart['values']) && !empty($userChart['dates'])):?>	
		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
		<script type="text/javascript">
			var chartData = [<?php echo implode(', ' ,$userChart['values']);?>];

			var opponents = [<?php echo '"' . implode('", "' ,$userChart['opponents']) . '"';?>];
			var user_teams = [<?php echo '"' . implode('", "' ,$userChart['user_teams']) . '"';?>];			
			var ots = [<?php echo implode(', ' ,$userChart['ots']);?>];

			for (var i = 0; i < chartData.length; i++) {
			    var value = chartData[i],
			        color = 'rgb(4,157,0)';
			    if (value < 0) {
			        color = 'rgb(147,0,0)';
			    }
			    
			    chartData[i] = {
			        y: value,
			        color: color
			    }			            
			}			
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {	
							plotBackgroundImage: '/js/highcharts/chart_bg.gif',
							borderWidth:0,
							renderTo: 'user_chart',
							defaultSeriesType: 'column'
					},	
					credits: {
						enabled: false
					},
			      plotOptions: {
			         column: {
						pointWidth: 13,
				        series: {	
				            stacking: 'normal'
				        },
				        //pointWidth:11,								         
			            pointPadding: 0,
			            shadow:false,
			            borderWidth: 0,
			            pointPadding: null
			         }					         
			      },
							
					title: {
						text: false
					},			
					legend: {
						enabled: false
					},	
					exporting: {
						enabled:false
					},			
					xAxis: {
						lineWidth: 2,
						lineColor: '#000000',
						gridLineWidth: 0,
						categories: [<?php echo '"' . implode('", "' ,$userChart['dates']) . '"';?>],
		            	tickmarkPlacement: 'on',
		            	
		            	tickWidth:1,
		            	tickColor:'#000000',
		            	tickWidth:2,
						labels: {
							align: 'right',
			            	rotation: -45,
			            	step:1,			     
			                style: {
			                	font: 'normal 9px Verdana, sans-serif',
			                	color: '#000000'
			            	}
						            	
			            }
		            
					},
					yAxis: {
						offset: 0,
						max:10,
						min:-10,
						gridLineWidth: 1,
						gridLineColor: 'rgb(214,214,214)',
						title: false,
						lineWidth: 2,
						lineColor: '#000000',

		            	tickWidth:1,
		            	tickColor:'#000000',
		            	tickWidth:2,	
		            	tickInterval:5,										
				        labels: {		      	            	
				            formatter: function() {
				            	if (this.value > 0) {
				            		return '+' + this.value;
						        } else {
						        	return this.value;
							    }
				               
				            }
			         	},
			            plotLines: [{
			                color: '#000000',
			                width: 2,
			                value: 0,
			                zIndex:100
			            }]
										
					},				
					tooltip: {
						formatter: function() {
							if (this.y > 0) {
								return '"' + user_teams[this.point.x] + '" defeated "' + opponents[this.point.x] + '", CD: '+ Math.abs(this.y) +', #OTs:' + ots[this.point.x];
							} else {
								return '"' + opponents[this.point.x] + '" defeated "' + user_teams[this.point.x] + '", CD: '+ Math.abs(this.y) +', #OTs:' + ots[this.point.x];
							} 
						}
					},
					credits: {
						enabled: false
					},
					series: [{
						animation:false,
						name: 'Cup differential',
						color: 'green',
						data: chartData
					}]
				});
			});
				
		</script>
<div id="user_chart" style="width: 600px; height: 250px;"></div>
<?php endif;?>