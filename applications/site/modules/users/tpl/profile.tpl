{include "default_open.tpl"}




{include "inputs/inputs.tpl"}

<h3>{GW::ln('/m/PLEASE_FILL_REQUIRED_FIELDS')}</h3>

<form id="usrForm" class="g-py-15" action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="act" value="do:saveProfile" />	
	
	
		<div class="row">      
			<div class="col-md-6">
				{if $item->name}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
				{input field="name" required=1 readonly=$tmpreadonly}
				{if $item->surname}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
				{input field="surname" required=1 readonly=$tmpreadonly}
				
				{*
				{if $item->birthdate && $item->birthdate!='0000-00-00' && !isset($item->changed_fields.birthdate)}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
				*}
						
				{*
				{if $item->country}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
				{input field="country" required=1 type=select empty_option=1 options=$countries readonly=$tmpreadonly}
				*}
				{*input field="city" required=1 type=location empty_option=1*}
				
				{input field="phone" required=1}
				
				
				{*
				{include "inputs/select2other.tpl" field=club}
				{include "inputs/select2other.tpl" field=coach}
				*}		
				
			</div>
			<div class="col-md-6">
				
				{*
				{call name=input field=gender type=radios options=GW::ln('/M/USERS/OPTIONS/gender') required=1}
				*}
				
				{input field="image" type="image" required=0 endpoint="users/users" extra_params=[id=>$item->id]}	

				
				{*input field="company_name" required=0*}	

				{if !$item->agreetc ||  isset($item->changed_fields.agreetc)}
					{capture assign=tmp}
						{GW::ln("/m/I_ACCEPT")} &nbsp; <a href="#" onclick="$('#termsandconds').toggle();return false">{GW::ln("/m/TERMS_AND_CONDS")}</a> &nbsp;
					{/capture}

					{input field=agreetc  type=checkbox required=1 title=$tmp}	

					<iframe id="termsandconds" src="/{$ln}/sys/termsandconds?clean=1" style="width:100%;height:400px;display:none"></iframe>
				{/if}
				
				{input field=newsletter type=checkbox title=GW::ln("/m/SUBSCRIBE_TO_NEWSLETTER")}				
			</div>
		</div>
	

	<br />
	
	<div class="row">
		<div class="col-md-4">
	
	<button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4" type="button" onclick="$('#usrForm').submit()">{GW::ln('/g/SUBMIT')}</button>
		</div>
	</div>
</form>



{include "default_close.tpl"}