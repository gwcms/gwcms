

{capture assign=tmpchange}if(this.value==-1){ $('#other{$field}').fadeIn(); $('#other{$field}0').fadeOut() }else{ $('#other{$field}').fadeOut(); $('#other{$field}0').fadeIn() }{/capture}
<div id="other{$field}0" style="display:none">
{call name=input field=$field type=select2 empty_option=1 options=$options[$field] required=$tmpreq 
	onchange=$tmpchange placeholder=GW::ln('/g/START_TYPING') help=GW::ln("/m/FIELD_NOTES/{$field}")}
</div>
<div id="other{$field}" style="display:none">
	
	{capture assign=field_note}
		<button class="btn btn-sm" onclick="$('#other{$field}0').fadeIn();
		$('#other{$field}0 select').val('');
		$('#other{$field}').fadeOut();
		$('#other{$field}0 select').trigger('change');
		setTimeout(() => $('#other{$field}0 select').select2('open'),100)
		return false;"><i class="fa fa-undo" aria-hidden="true"></i></button>

	{/capture}
	
{call name=input field="{$field}_other" type=text 
	addclass="requiredhidden" 
	placeholder=GW::ln("/m/FIELDS_HINT/{$field}_other") 
	note_raw=$field_note
	required=$tmpreq}

	
</div>