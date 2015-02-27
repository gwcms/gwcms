
	<script type="text/javascript" src="{$app_root}js/autoresize.jquery.min.js"></script>
	<script type="text/javascript" src="{$app_root}js/elements_inputs_sms.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){ 
		
		
$('.ta_autoresize').autoResize(); 
		

gw_sms.max_parts=5
gw_sms_form.register_sms_input('sms_inpt');
//gw_sms_form.register_targets_input('targets_inp');


gw_sms_form.onchange.push(
	function()
	{
		
		{*
		document.getElementById('sms_cnt').innerHTML = gw_sms_form.last_info.parts_count;
		document.getElementById('chars_left').innerHTML=gw_sms_form.last_info.chars_left;
		document.getElementById('chars_max').innerHTML=gw_sms_form.last_info.max_length;
		document.getElementById('target_cnt').innerHTML=gw_sms_form.last_target_count;
		document.getElementById('total_sms_cnt').innerHTML=gw_sms_form.total_sms();
		*}
		
		var x = $("#gw_input_{$name} .input_label_td")[0];
		
		x.title = gw_sms_form.info_english().split('\n').join(' // ');
		x.innerHTML= 
			"msg ("+gw_sms_form.last_info.length+" / "+gw_sms_form.last_info.max_length+")";
	}
)

gw_sms_form.init();		
		
		});




	</script>



<textarea id="sms_inpt" {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
    			
