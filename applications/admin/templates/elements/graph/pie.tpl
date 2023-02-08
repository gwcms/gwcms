{$GLOBALS._chart_pie=$GLOBALS._chart_pie+1}
{$index=$GLOBALS._chart_pie}
{$GLOBALS._highchart=$GLOBALS._highchart+1}

{if $GLOBALS._highchart == 1}
<script src="https://code.highcharts.com/4.2.2/highcharts.js"></script>
{/if}

<div id="hichcontainer{$index}" style="min-width: {$size|default:300}px; height: {$size|default:300}px; max-width: {$maxwidth|default:600}px;" ></div>

<script>

	
function initGraph{$index}(){		
Highcharts.chart('hichcontainer{$index}', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: '{$title}'
  },
  tooltip: {
    {literal}pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'{/literal}
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        {literal}format: '<b>{point.name}</b>: {point.percentage:.1f} %'{/literal}
      }
    }
  },
  series: [{
    name: 'Disk use',
    colorByPoint: true,
    data: {json_encode($data)}
    }]
});	
}


require(['gwcms'],function(){
		initGraph{$index}();
})



</script>