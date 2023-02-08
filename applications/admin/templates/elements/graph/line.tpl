
{GW::$globals._highchart_line=GW::$globals._highchart_line+1}
{$index=GW::$globals._highchart_line}

{GW::$globals._highchart=GW::$globals._highchart+1}

{if GW::$globals._highchart == 1}
<script src="https://code.highcharts.com/4.2.2/highcharts.js"></script>
{/if}



<div id="highcharsline{$index}" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<script>
	

	function mysqld2jsdate(t)
	{
		t = t.split(/[- :]/);

		// Apply each element to the Date function
		var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3]));
		
		return d.getTime()-3600*1000*3;
	}
		
	function getData(source)
	{
		var ordered = {};
		Object.keys(source).sort().forEach(function(key) {
		  ordered[key] = source[key];
		});

		source = ordered;
			
			
			
		var data = [];
			
		for (var idx in source) {
		    var val=source[idx];

		    var time=mysqld2jsdate(idx);
		    data.push({
			x: time,
			y: val-0
		    });
		}
		
		return data;		
	}
	
	function initHighCharLine{$index}(){

	var process_data = {json_encode($data)}
	
				var graphseries=[];
		{foreach $data as $key => $values}
		graphseries.push({
                name: '{$key}',
                data: (function () { return getData(process_data["{$key}"]) }())
            });
	    {/foreach}					
				

	Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });       
		
		Highcharts.chart('highcharsline{$index}',{
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
				zoomType: 'x',
                events: {
                    load: function () {

                        // set up the updating of the chart each second
                        var series = this.series[0];
						
						console.log(series);
						
						                    }
                }
            },
            title: {
                text: '{$title}'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Value'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        Highcharts.dateFormat('%H:%M', this.x) + '<br/>' +
                        Highcharts.numberFormat(this.y, 2);
                }
            },
            legend: {
                enabled: true
            },
            exporting: {
                enabled: false
            },
            series: graphseries

			
        });
	}
	
	require(['gwcms'], function(){
		initHighCharLine{$index}();
	})
</script>