{if $tabs}
	<script type="text/javascript" src="{$app_root}js/textarea_tabfunc.js"></script>	
{/if}



<style>
	.infotable {  margin-top: 10px; border-spacing:0px;  border-collapse: collapse;  }
	.infotable td{ font-size: 11px  }
	.infotable th{ padding: 0 5px 0 0; text-align:right; font-style:italic; font-size: 11px; }
	.unicodehelp{ cursor: help; background-color:#4358d9;padding: 0 5px 0 5px;color:white;border-radius: 3px; }
	.partscnt { cursor: help; border-radius: 3px; padding: 0 5px 0 5px; }
	.parts1{ cursor: auto; padding:0 }	
	.parts2{ background-color:#b06710;;color:white; }	
	.parts3{ background-color:#f3ff00;color:black; }	
	.parts4{ background-color:#930060;color:white; }	
	.parts5{ background-color:#67facc;color:black; }	
</style>

<script type="text/javascript" src="{$app_root}static/js/elements_inputs_sms.js"></script>


	<script type="text/javascript">
		require(['gwcms'], function(){

			gw_sms.max_parts=5

			gw_sms_form.register_sms_input('item__{$field}__');

			{if $targets_field}
				gw_sms_form.register_targets_input('item__{$targets_field}__');
			{/if}

			lang={json_encode(GW::ln('/G/SMS_INPUT/JS_VARS'))};

			gw_sms_form.onchange.push(
				function()
				{

					{*
					document.getElementById('sms_cnt').innerHTML = gw_sms_form.last_info.parts_count;
					document.getElementById('chars_left').innerHTML=gw_sms_form.last_info.chars_left;
					document.getElementById('chars_max').innerHTML=gw_sms_form.last_info.max_length;

					document.getElementById('total_sms_cnt').innerHTML=gw_sms_form.total_sms();
					*}

					$('#recipients_cnt').text(gw_sms_form.last_target_count ? '('+gw_sms_form.last_target_count+')' : '');



					var info = gw_sms_form.last_info;

					var unics = info.unicode_simbols ? lang.UNICODE_INFO+'. '+lang.UNICODE_SYMBOLS+': '+info.unicode_simbols : '';


					var tmp = '<table class="infotable">'+
						'<tr><th>'+lang.SYMBOLS+'</td><td>'+info.length+" / "+info.max_length+'</td></tr>'+
						'<tr><th>'+lang.PARTS+'</td><td><span class="parts'+info.parts_count+' partscnt" '+(info.parts_count>1?'title="'+lang.MULTIPART_INFO+'"':'')+'>'+info.parts_count+'</span></td></tr>'+
						'<tr><th>'+lang.ENCODING+'</td><td title="'+unics+'">'+lang.ENCODING_OPT[info.encoding]+'</td></tr>'+
						'</table>';


					//console.log(gw_sms_form.last_info);
					$('#item__{$field}___inputLabel .input_note').html(info.length ? tmp :'')
					/*
					var x = $("#gw_input_{$name} .input_label_td")[0];

					x.title = gw_sms_form.info_english().split('\n').join(' // ');
					x.innerHTML= 
						"msg ("+gw_sms_form.last_info.length+" / "+gw_sms_form.last_info.max_length+")";
						*/
				}
			)

			gw_sms_form.init();			
		});
</script>



<textarea  id="{$id}" {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
    			
