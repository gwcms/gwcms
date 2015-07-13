{include "default_open.tpl"}


<table>
	
				<tr>
					<td id="progress_percent_drop" style="font-size:25px;text-align:center;padding: 10px">
						?
					</td>
				</tr>
				
				<tr>	
					<td style="text-align:center;padding: 0px 5px 0px 5px">		

					<img style="display:none" id="icon_working" src="{$app_root}/img/working.gif" />
					<img style="display:none" id="icon_done" src="{$app_root}/img/complete_64.png" />
					<img style="display:none" id="icon_fail" src="{$app_root}/img/error_64.png" />

					</td>
					
					<td id="progress_status_drop" style="font-size:14px">
						Siunčiama... <i title="Išjungus įvyks pauzė, grįžus siuntimas bus tęsiamas.">Neišjunkite lango.</i>
					</td>					
					
				</tr>	
	
				<tr>
					<td id="progress_count_drop" style="text-align:center">
						?
					</td>
				</tr>		

</table>


<script>
	
	function sendPortion(){
		
		$('#icon_working').show();
		
		$.get('{$smarty.server.REQUEST_URI}', { 'act':'doSend', 'id':{$id} }, function(data){

			var obj = jQuery.parseJSON(data);

			if(!obj.portion_size || obj.finished)
			{
				if((Math.round(obj.total_sent / obj.total_size) > 0.98) || obj.finished)
				{
					$('#progress_status_drop').text('Išsiūsta.');
					$('#icon_working').hide();
					$('#icon_done').show();
				}else{
					$('#progress_percent_drop').text('!');
					$('#icon_working').hide();
					$('#icon_fail').show();				
				}
			}else{

				sendPortion();
			}
			
				$('#progress_percent_drop').text( Math.round(obj.total_sent / obj.total_size*10000)/100 + '%'  );
				$('#progress_count_drop').text(obj.total_sent+' / '+obj.total_size);			


		});
	
	}
	
	$(function(){
		sendPortion();
	})
	
{*	/alert('test');*}
</script>

{include "default_close.tpl"}