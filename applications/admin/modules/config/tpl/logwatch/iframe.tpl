{include file="head.tpl"}
<style>
	
.rt_logwatch_ta{
	position:absolute;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	overflow: scroll;
	white-space: pre-line;
	font-family: courier new;

}		

.rt_logwatch_status{ padding:5px;border:1px solid silver;background-color:#ddd;position:fixed;top:10px;right:20px; }
.rt_logwatch_status span{ margin-left:2px;margin-right:2px;paddin:1px }
body{  } 

span:last-child { background-color: orange; } 		
</style>
			


<script src="modules/{$app->path_arr.0.path}/tpl/logwatch/realtime.js"></script>			
			
<body>

<div id="ta_container"></div>

<script type="text/javascript">


var x = new rt_logwatch({ 
	file: '{$smarty.request.id}', 
	time: 3000,
	container: $('#ta_container'),	
	});
</script>
			

</body>
</html>