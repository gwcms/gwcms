
{include "default_open.tpl"}

{if GW::s('SITE_DEFAULT_NO_HOR_MARGINS')}
<br><br>
{/if}

{include file="inputs/inputs.tpl"}



{$debug=$app->sess["lang-results-active"]}

{include "steps.tpl" }

{*
{$steps=[
	1=>[title=>GW::ln('/m/PREVIEW_BLANK')],
	2=>[title=>GW::ln('/m/FILL_DETAILS')],
	3=>[title=>GW::ln('/m/PREVIEW_FILLED_AND_SIGN')],
	4=>[title=>GW::ln('/m/FINISH')]
]}
*}

{$idx=1}
{foreach $m->steps as $step}
	{$steps[$idx]=['title'=>GW::ln("/m/STEPS/{$step}"), 'key'=>$step]}
	
	{if $m->steps_current == $step}
		{$steps[$idx].current=1}
	{/if}
		
	
	{$idx=$idx+1}
{/foreach}

{call "steps_block" step_in_get=s}


{$step=$smarty.get.s|default:1}

<hr class="colorgraph" style="clear:both">
{if $item->site_info_trans && $step==1}
{GW::ln("/{$item->site_info_trans}")}
<br /><br />
{/if}



{if in_array($m->steps_current, [preview,sign_basic])  || ($m->steps_current==sign_marksign && $m->isSigned($answer))}
  

	<div class='containercontainer' >
		
		<div class="scrollcontainer"  style="margin-bottom: 15px;overflow-y:scroll;{if $m->isSigned($answer)}max-height: 40vh;{else}max-height: 70vh;{/if}">
<div class="containerim">
	<div >
		{$body}

		{if $answer->signature}
			{include "{$smarty.current_dir}/digitalsignature.tpl"}
		{/if}
	</div>
	
</div>
		 </div>
		
	</div>


{/if}


{if $m->steps_current==form}
	
	{if $m->feat(anonymous_access) && !$app->user->id}
		Jei norite tęsti sutarties sudarymą kurį anksčiau buvote pradėję ir išsaugoję į paskyrą
		<a class='btn btn-warning btn-sm' href='{$m->buildDirectUri('docs/docs/item',['act'=>doAnonymousToRealUserLogin, 'id'=>$smarty.get.id],[level=>0])}'>{GW::ln('/m/LOGIN')}</a>
		<br><br>
	{/if}	
	
	
	{if GW::s(PROJECT_NAME)==artistdb}
<div class="panel panel-primary animated fadeInDown">
	<div class="panel-heading">{$item->form->title}</div>
		<div class="panel-body">	
	{else}
<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
	<header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{$item->form->title}</h1>
              </header>		
	{/if}

	
	

	


						
	<form method='post' action='{$smarty.server.REQUEST_URI}'>
		<input type='hidden' name='act' value='doSubmitForm'>
		<input type='hidden' name='docid' value='{$item->id}'>
		<input type='hidden' name='formid' value='{$item->form->id}'>


	{foreach $grouplist as $fieldset => $fieldsetlist}
		{if $m->isSigned($answer)}
			{$readonly=1}
		{/if}
		
		
		{if $fieldset && !is_numeric($fieldset)}
			<br>
			{GW::ln("/m/FIELDSET/{$fieldset}")}
			<hr  style="margin:0;margin-bottom:15px"/>
			
		{/if}		
		<div class="row">	
			
			
		{$item=$answer}
		
			
		{foreach $fieldsetlist as $elm}			
				<div class="col-md-{$elm->size}">
					
					{if $elm->hidden_note}
						{$tmphiddennote=GW::ln("/G/customform/FIELD_H_NOTES/{$elm->fieldname}")}
					{else}
						{$tmphiddennote=false}
					{/if}
					
					{$params_expand=json_decode($elm->config, true)}
					{if $params_expand.options_ln}{$params_expand.options=GW::ln($params_expand.options_ln)}{/if}
					{if $elm->options}{$params_expand.options=$elm->options}{/if}
					{$params_expand.field="keyval/{$elm->fieldname}"}
					{$params_expand.type=$elm->type}
					{$params_expand.required=$elm->required}
					{$params_expand.title=$elm->title}
					{$params_expand.help=$elm->hidden_note}
					{$params_expand.note=$elm->note}
					{$params_expand.placeholder=$elm->placeholder}
					{$params_expand.value=$elm->value}
										
					
					{if $debug}
						{d::ldump($params_expand)}
					{/if}
						
					{*allowedFileExtensions=explode(',',$item->composite_map.passportscan.1.allowed_extensions)}*}
					{call name=input params_expand=$params_expand}
				</div>
		{/foreach}
		</div>
	{/foreach}

	
	{if GW::s(PROJECT_NAME)==artistdb}
		</div>
	{/if}	
		
</div>

		{if $step!=1}
		<a class="btn btn-ar btn-default" href="javascript:window.history.back()">&laquo;
			{GW::ln('/g/BACK')}
		</a>
		{/if}
		
		
		{if !$m->isSigned($answer)}
			<input class="btn btn-warning float-right" type="submit" value="{GW::ln('/m/SUBMIT')}"> 
			<br /><br />	
		{else}
			<span class="text-muted">{GW::ln('/m/SIGN_TIME')}: {$answer->sign_time}</span>
		{/if}
	
{/if}


{if $m->steps_next==sign_marksign || $m->steps_current==preview_blank}

	<a class="btn btn-ar btn-default float-right" href="{$app->buildUri(false,[s=>$smarty.get.s+1]+$smarty.get)}">
		{GW::ln('/g/NEXT')} &raquo;
	</a>	
	<br/><br/>
	
	{if $m->feat(sign_again)}

		<div class="float-right" style="margin-bottom:10px">
			{GW::ln('/m/CREATE_CONTRACT_AGAIN_EXPLAIN')}
		<br>

		{$seq=$smarty.get.multiple|default:1}
		<a class="btn btn-ar btn-primary" href="{$app->buildUri(false, [multiple=>$seq+1,s=>$m->getStepIdx(form)]+$smarty.get)}">
			 {GW::ln('/m/CREATE_CONTRACT_AGAIN')} ({$seq+1})
		</a>
		</div>


		<br><br>			
	{/if}		
{/if}


{if $m->steps_current==sign_basic}

	
	{if !$m->isSigned($answer)}

		<a class="btn btn-ar btn-default" href="javascript:window.history.back()">&laquo;
			{GW::ln('/g/BACK')}
		</a>			

		<a class="btn btn-ar btn-warning float-right" 
		   onclick="if(!confirm('{GW::ln('/G/DOCS/CONFIRM_SIGN_NO_EDIT_AFTER_SIGN')}'))return false" 
		   href="{$app->buildUri(false, [act=>doSign]+$smarty.get)}">
			{GW::ln('/m/VIEWS/doSign')}
		</a>	
		
		<a class="btn btn-ar btn-default" href="{$app->buildUri(false, [act=>doExportAsPdf]+$smarty.get)}" target='_blank'>
			<i class='fa fa-file-pdf-o'></i> {GW::ln('/g/DOWNLOAD')}
		</a>
		<a class="btn btn-ar btn-default" href="javascript:doprint()">
			<i class='fa fa-print'></i> {GW::ln('/g/PRINT')}
		</a>
	{else}
		<a class="btn btn-ar btn-default" href="{$app->buildUri(false, [act=>doExportAsPdf]+$smarty.get)}" target='_blank'>
			<i class='fa fa-file-pdf-o'></i> {GW::ln('/g/DOWNLOAD')}
		</a>		

		{if $m->feat(sign_again)}
			{$seq=$smarty.get.multiple|default:1}
			<a class="btn btn-ar btn-primary" href="{$app->buildUri(false, [multiple=>$seq+1,s=>$m->getStepIdx(form)]+$smarty.get)}">
				 {GW::ln('/g/CREATE_CONTRACT_AGAIN')} ({$seq+1})
			</a>			
		{/if}	

		<a class="btn btn-ar btn-default float-right" href="javascript:doprint()">
			<i class='fa fa-print'></i> {GW::ln('/g/PRINT')}
		</a>			
		
	{/if}
	
	<br/><br/>
{/if}

{if $m->steps_current==finish}
	
	
	
	<a class="btn btn-ar btn-default" href="{$app->buildUri(false, [act=>doExportToMarkSign]+$smarty.get)}" target='_blank'>
		<span class="material-symbols-outlined">signature</span>
		{GW::ln('/g/DO_SIGN_MARKSIGN')}
	</a>	
	
{/if}

{if $m->steps_current=='finish'}
	{GW::ln('/m/CONTRACT_SIGNED_MESSAGE')}
{/if}


{if $m->steps_current==sign_marksign}
	{include "`$smarty.current_dir`/step_marksign.tpl"}

	
{/if}



<script>
	function doprint()
	{
		var doc = $('.containerim').html();
		$('body').html(doc).css({ padding: "50px" });
		window.print()
	}
</script>


<link rel="stylesheet" href="/applications/site/assets/css/cssreset-min.css" type="text/css">
<style>
.containercontainer{
	all: unset;
}	
.containerim{    
	border:4px inset;
	position: relative;
	z-index:1;
	overflow:hidden; /*if you want to crop the image*/
	text-shadow: 1px 1px rgba(255,255,255,0.8);
	padding:80px;
}

@media only screen and (max-width: 800px) {
  .containerim {

    padding:30px;
  }
}

.containerim p{ color:black; }

.containerim:before{
    z-index:-1;
    position:absolute;
    left:0;
    top:0;
    display:block;
    width: 100%;
    height:100%;
    content: '';
    background-image: url('/applications/site/assets/img/seamless_paper_texture.jpg');
    opacity:0.4;
}

.containersm{ max-height: 400px; overflow-y: scroll; border:4px inset; }
.containersm .containerim{ border:0; }




@media print {
	body{
		font-size: 10pt;
		padding:0px !important;
	}
}


</style>


{if GW::s('SITE_DEFAULT_NO_HOR_MARGINS')}
<br><br>
{/if}

{include "default_close.tpl"}