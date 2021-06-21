

{capture assign=tmpchange}if(this.value==-1){ $('#other{$field}').fadeIn() }else{ $('#other{$field}').fadeOut() }{/capture}
{call name=input field=$field type=select2 empty_option=1 options=$options[$field] required=$tmpreq 
	onchange=$tmpchange placeholder=GW::ln('/m/START_TYPING') help=GW::ln("/m/FIELD_NOTES/{$field}")}
<div id="other{$field}" style="display:none">
{call name=input field="{$field}_other" type=text 
	addclass="requiredhidden" 
	placeholder=GW::ln("/M/USERS/FIELDS_HINT/{$field}_other") 
	required=$tmpreq}
</div>