

{function search_block}
		<div class="unhide_container" style="float:left">
			<input placeholder="ieškoti" style="width:{$width}px" onkeyup="updateDropdown(this.value)" onclick="$('#dropdown').toggle();">
					
			<div id="dropdown">


				<div class="status nowrap">
					<table style="width:100%" cellpadding="0" cellspacing="0">
					<tr>
					<td>
					Rasta: <span class="result_count">0</span> &nbsp; 
					<a class="foundresults" href="#" onclick="addAll(); return false">Pridėti visus</a>
					</td>
					<td width="100%"></td>
					<td>
					
					<button data-dismiss="alert" class="close" type="button" onclick="$('#dropdown').hide(); ">×</button>
					</td>
					</tr>
					</table>
					
					
					
				<div class="results foundresults">
					
				</div>
					
					
				</div>
			</div>
		
		
		</div>
{/function}

<style>
	.cleantable td{ border:0;padding:0 }
</style>

<form method="POST" action="{$smarty.server.REQUEST_URI}">
	<input type="hidden" name="act" value="do:send_sms">

	<link rel="stylesheet" type="text/css" href="css/new_sms.css" />
	
	<script type="text/javascript" src="js/autoresize.jquery.min.js"></script>
	<script type="text/javascript" src="js/elements_inputs_sms.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){ 
		
		
$('.ta_autoresize').autoResize(); 
		

gw_sms.max_parts=5
gw_sms_form.register_sms_input('sms_inpt');
gw_sms_form.register_targets_input('targets_inp');


gw_sms_form.onchange.push(
	function()
	{
		var total = gw_sms_form.total_sms();
		var targets = gw_sms_form.last_target_count;
		var parts = gw_sms_form.last_info.parts_count;
		var encoding = gw_sms_form.last_info.encoding;
		var length = gw_sms_form.last_info.length;
		var maxl = gw_sms_form.last_info.max_length;
		
		var x = $("#sms_info")[0];
		
		x.title = gw_sms_form.info_english().split('\n').join(' // ');
		var str = 		"<b>Simb. sk.</b>: "+length+', '+
						(encoding==7 ? '<b>Ilgoji (160 simb.)</b>, ' : '<b>Trumpoji (70 simb.)</b>, ') +			
						(parts > 1 ? "Daugiadalė. <b>Dalių sk</b>: "+parts+', ' : '') +
						//(targets > 1 ? '<b>Gavėjai</b>: '+ targets +', ' : '') +
						(total > 1 ? '<b>Viso žinučių</b>: '+ total +', ' : '') +				
						(length > maxl ? '<b>Viršytas max ilgis ('+maxl+')</b>' : '') +			
						'';
					
		$('#sms_targets_display').text(targets);	
		str = str.substring(0, str.length-2);
		x.innerHTML = str;

		var ltchars=gw_sms.hasLTChars(gw_sms_form.inputs.text.value)
		
		if(ltchars)
			$('#susveplinti').show();
		else
			$('#susveplinti').hide();
			
	}
)

gw_sms_form.init();		
		
		
		$('#defer').change(function(){
			this.checked ? $('.sms_delay_t').show() : $('.sms_delay_t').hide()
		}).change();
		
		
		
		
		});


	</script>






<table class="gwTable" style="width:auto">
<tr>





<td> {* VIDURYS *}


	{* ŽINUTĖ *}
	<table class="gwTable">
	
	<tr><th {if $item_errors.message}class="error_fld_label"{/if}>SMS</th>
	<td id="sms_info" style="max-width:300px"></td>
	
	</tr>
	<tr>
	<td colspan="2">
		
		<textarea name="sms[message]" id="sms_inpt" class="ta_autoresize" 
		style="width:300px; height:100px"
		onchange="this.value=$.trim(this.value);" placeholder="Žinutės tekstas">{$item->message|escape}</textarea>
		
		<div id="susveplinti" style="display:none;text-align:right">
			<button onclick="gw_sms_form.removeLtChars(); return false">Sušveplinti</button>
		</div>
	    		
	</td>
	</tr>
	</table>
	{* / ŽINUTĖ *}

	{* ADMIN ONLY SELECT GATE *}
	
	<table class="gwTable" style="margin-top:2px;">

	
	
	<tr id="sender_id_row">
	<th width="1%" nowrap {if $item_errors.sender}class="error_fld_label"{/if}>Sender id</th>
	<td style="max-width:300px">
		<input name="sms[sender]" maxlength="11" value="{$item->sender|escape}" />
	</td>
	</tr>	
	
	</table>
	
	{* / ADMIN ONLY SELECT GATE *}
	
	{* ATIDETA *}
	<table class="gwTable" style="width:100%;margin-top:2px">
	<tr>
	<th style="width:1%">Atidėta</th>
	<td id="" style=""> <input 
			name="sms[defer]" 
			type="checkbox" 
			id="defer"
			{if $item->send_time}checked{/if}
			> </td>
	
	<th class="sms_delay_t" style="width:1%;display:none" nowrap>Išsiuntimo laikas</th>

	
	<td class="sms_delay_t" style="display:none"> <input 
			name="sms[send_time]" 
			value="{if $item->send_time}{$item->send_time|escape}{else}{'Y-m-d H:i'|date}{/if}" 
			style="width:100px"> </td>
	</tr>
	</table>
	{* / ATIDETA *}

</td>{* / CENTRAS *}
<td valign="top"> {* DESINE *}

<table class="gwTable">

<tr >
	<th colspan="2" {if $item_errors.recipients}class="error_fld_label"{/if}>Gavėjai (<span id="sms_targets_display">0</span>)</th>
	
	
</tr>

<tr>
<td colspan="2">



	{*gavejai*}
	
	
	
	{*
		Jeigu vienas gavejas ir nurodytas tik numeris, 
		tada bus papildyta gavejo vardu 
		jei irasytas i kontaktus gavejas
	*}
	{*
	{$target_name=Sms_Contact::nameByNrStatic($targets)}
	*}
	
	{if !$targets}
		{$targets=$smarty.session.new_sms_targets|default:$smarty.get.sms.targets}
	{/if}
	{gw_unassign var=$smarty.session.new_sms_targets}
	 
	
	<textarea 
	name="sms[recipients]" id="targets_inp" style="width:200px; height:80px"
	onchange="this.value=$.trim(this.value);"
	 wrap="off" placeholder="Gavėjų tel. nr. sąrašas"
	>{$item->recipients|escape}{$target_name}</textarea>
</td></tr>



</tr>


</table>

<input type="hidden" id="send" value="0" name="send" />

<button onclick="$('#send').val(1)" style="margin:5px;padding-left:22px;padding-right:22px;" title="Išsaugoti ir siųsti">Siųsti</button>
<button style="margin:5px;padding-left:22px;padding-right:22px;" title="Išsaugoti be siuntimo">Išsaugoti</button>

</td>


</table>

</form>
