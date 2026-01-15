{include "inputs/inputs.tpl"}

{$fields= $m->getFieldsConfig()}

<div class="row"> 		

{if $register || !$item->email}
	<div class="col-md-6">
		{input field="email" type=email required=1 note=GW::ln('/M/USERS/REGISTER_EMAIL_NOTE') help=GW::ln('/M/USERS/REGISTER_EMAIL_HELP')}
	</div>
{else}
	{*todo nera  PADARYTA*}
	<div class="col-md-6">
		{input field="email" type=read required=1 note=GW::ln('/M/USERS/REGISTER_EMAIL_NOTE') help=GW::ln('/M/USERS/REGISTER_EMAIL_HELP')}
	</div>	
{/if}

{if $fields.fields.name}	
	<div class="col-md-6">
	{input field="name" required=$fields.required.name}
	</div>
{/if}					
{if $fields.fields.surname}	
	<div class="col-md-6">
		{input field="surname" required=$fields.required.surname}
	</div>
{/if}			

{if $register}
	<div class="col-md-6">
		{input field="pass_new" required=1 type=password}
	</div>
	<div class="col-md-6">
		{input field="pass_new_repeat" required=1 type=password}
	</div>
{/if}







{if $fields.fields.phone}	
	<div class="col-md-6">
		{input field="phone" type=intphone required=$fields.required.phone limit_country=json_decode(mb_strtolower($m->cfg->phone_limit_country))}
	</div>
{/if}						

{if $fields.fields.company_name}	
	<div class="col-md-6">
	{input field="company_name" required=$fields.required.company_name}
	</div>
{/if}									

{if $fields.fields.country}
	<div class="col-md-6">

		{if $item->country}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
		{input field="country" required=1 type=select empty_option=1 options=$countries readonly=$tmpreadonly}
	</div>
{/if}
{if $fields.fields.city &&  !$register}
	<div class="col-md-6">	
		{input field="city" required=1}	
	</div>
{/if}	

{if $fields.fields.birthdate &&  !$register}
	{if $item->birthdate && $item->birthdate!='0000-00-00' && !isset($item->changed_fields.birthdate)}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
	<div class="col-md-6">
		
	{capture assign=tmp}
		{GW::ln('/M/USERS/ENTER_BIRTHDATE_NOTE')}
		
		
		{if $item->birthdate && $item->birthdate!='0000-00-00'}
			<a title="{GW::ln('/g/REQUEST_TO_ADMIN')}" href="{$app->buildUri('direct/support/support',['message'=>"{GW::ln('/g/REQUEST_TO_ADMIN')} '{GW::ln("/M/users/FIELDS/birthdate")}'. {GW::ln("/g/PLEASE_CHANGE_MY_INF")}"])}">
						<i class="fa fa-exclamation-triangle"></i>
			</a>
		{/if}		
	{/capture}
		
	{input field="birthdate" required=1 type=birthdate readonly=$tmpreadonly note_raw=$tmp help=GW::ln('/M/USERS/RENTER_BIRTHDATE_HELP')}	
	

				
	</div>
{/if}


{*input field="company_name" required=0*}	

{if $fields.fields.agreetc}
	{if $register || !$item->agreetc}
	<div class="col-md-6">
	{capture assign=tmp}
		{GW::ln("/m/I_ACCEPT")} &nbsp; <a href="#" onclick="$('#termsandconds').toggle();return false">{GW::ln("/m/TERMS_AND_CONDS")}</a> &nbsp;
	{/capture}

	{input field=agreetc type=checkbox required=1 title=$tmp}
	</div>
	{/if}
{/if}

{*sports*}
{if $fields.fields.club &&  !$register}
	{if GW::s(SPORT)==badminton}
		
			{capture assign=tmp}
				{GW::ln('/M/USERS/ENTER_CLUB_NOTE')}


				{if $item->birthdate && $item->birthdate!='0000-00-00'}
					<a title="{GW::ln('/g/REQUEST_TO_ADMIN_CHANGE_CLUB')}" href="{$app->buildUri('direct/support/support',['message'=>"{GW::ln('/g/REQUEST_TO_ADMIN_CHANGE_CLUB')}.\n{GW::ln("/g/PLEASE_CHANGE_MY_INF")}"])}">
								<i class="fa fa-exclamation-triangle"></i>
					</a>
				{/if}		
			{/capture}			
					
		
		<div class="col-md-6">
		{if $item->club}
			

			{input field="club" required=1 type=birthdate value=$item->clubObj->title readonly=$tmpreadonly note_raw=$tmp help=GW::ln('/M/USERS/RENTER_BIRTHDATE_HELP')}	
		{else}
			{$options.club=$options.club_long}
			{*include "inputs/select2other.tpl" field=club note_raw=$tmp*}	
			{gw_unassign var=$options.club[-1]}
			{input field="club" type="select2" required=0 options=$options.club}
		{/if}
		</div>
	{else}
		<div class="col-md-6">
			{$options.club=$options.club_long}
			{*include "inputs/select2other.tpl" field=club*}
			{gw_unassign var=$options.club[-1]}
			{input field="club" type="select2" required=0 options=$options.club}
		</div>	
	{/if}
{/if}

{if $fields.fields.fivb_number &&  !$register}	
	<div class="col-md-6">	
	{input field="fivb_number" type=number help=GW::ln('/M/USERS/FIELD_HELP/fivb_number')}
	</div>
{/if}
{*sports*}
{if $fields.fields.coach &&  !$register}
	<div class="col-md-6">
		{include "inputs/select2other.tpl" field=coach}
	</div>
{/if}

{if $fields.fields.newsletter}

	<div class="col-md-6">
		{input field=newsletter type=checkbox title=GW::ln("/m/SUBSCRIBE_TO_NEWSLETTER")}
	</div>

{/if}

{if $fields.fields.image &&  !$register}
	<div class="col-md-6">	
		{input field="image" type="image" required=0 endpoint="users/users"}
	</div>
{/if}	

{*sports*}
{if $fields.fields.gender &&  !$register}
	<div class="col-md-6">	
		{call name=input field=gender type=radios options=GW::ln('/M/USERS/OPTIONS/gender') required=1}
	</div>
{/if}
{*sports*}
{if $fields.fields.ts1_number &&  !$register}	
	<div class="col-md-6">	
	{input field="ts1_number" type=number help=GW::ln('/M/USERS/FIELD_HELP/ts1_number')}
	</div>
{/if}
{*sports*}
{if $fields.fields.regothers &&  !$register}
	<div class="col-md-6">
		{if $item->regothers < 1}

			{$opts = GW::ln('/M/USERS/OPTIONS/regothers')}
			{$opts=[1=>$opts.1, 2=>$opts.2]}
			{call name=input field=regothers type=radios options=$opts required=1 note=GW::ln('/m/FIELD_NOTES/regothers')}
		{else}
			{GW::ln("/M/users/FIELDS/regothers")}: <b>{GW::ln("/M/USERS/OPTIONS/regothers/{$item->regothers}")}</b>


			<a title="{GW::ln('/g/REQUEST_TO_ADMIN')}" href="{$app->buildUri('direct/support/support',['message'=>"{GW::ln('/g/REQUEST_TO_ADMIN')} '{GW::ln("/M/users/FIELDS/regothers")}'. {GW::ln("/g/PLEASE_CHANGE_MY_INF")}"])}">
				<i class="fa fa-exclamation-triangle"></i>
			</a>
		{/if}
	</div>
{/if}
{if $fields.fields.passportscan &&  !$register}
	<div class="col-md-6">
		{input field="passportscan" type="image" 
			required=1  endpoint="users/users" allowpdf=1 note=GW::ln('/m/FIELD_NOTES/passportscan')
			help=GW::ln('/m/FIELD_NOTES/upload_image_or_pdf')
		}
	</div>
{/if}
{if $fields.fields.medicalpermit &&  !$register}			
	<div class="col-md-6">
		{input field="medicalpermit" type="image" required=1  
			endpoint="users/users" allowpdf=1 note=GW::ln('/m/FIELD_NOTES/passportscan')
			help=GW::ln('/m/FIELD_NOTES/upload_image_or_pdf')
		}	
	</div>
{/if}

{if GW::s('PROJECT_NAME') ==  'events_ltf_lt' && !$register}

	<div class="col-md-6">
		{input field="antidoping" type="image" required=1  
			endpoint="users/users" allowpdf=1 note=GW::ln('/m/FIELD_NOTES/antidoping')
			help=GW::ln('/m/FIELD_NOTES/upload_image_or_pdf')
		}	
	</div>	
	
{/if}



{if $register && $smarty.session.3rdAuthUser}
	<div class="col-md-6">
	{capture assign="titletmp"}
		{$tmptype=strtoupper($smarty.session.3rdAuthUser->type)}
		  {GW::ln('/m/LINK_WITH_X',[v=>[type=>$tmptype]])} <b class="ml-1"> {$smarty.session.3rdAuthUser->title}</b>
	{/capture}
	{call "input" field=3rdAuthUserlink required=0 type=checkbox title=$titletmp value=1}
	</div>
{/if}

</div>
{if $register}
	{if $recapPublicKey}
		<div class="row g-mb-10">
		      <div class="col-md-6">
		   <div class="g-recaptcha" data-sitekey="{$recapPublicKey}" style="margin-bottom:5px;"></div>
			   </div>	  

		   <script src='https://www.google.com/recaptcha/api.js' async defer></script>
		   <script>
		       $('#regForm').submit(function(event) {

			   var response = grecaptcha.getResponse();

			   if(response.length == 0){
				   event.preventDefault();
				   alert('{GW::ln('/G/validation/RECAPTCHA_FAILED')|escape:'javascript'}');
			   }
		     });
		   </script>
	      </div>				
      {/if}
{/if}			      
<iframe id="termsandconds" src="/{$ln}/f/terms_and_conditions?clean=1" style="width:100%;height:400px;display:none"></iframe>	