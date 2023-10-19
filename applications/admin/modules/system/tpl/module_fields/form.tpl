{include file="default_form_open.tpl" form_width="1000px"}
{$width_title=100px}


{*todo: dropdownas is vertimu perrasymu*}
{$editinsite="Vertimai susikurs saite CTLR+Q - redaguoti"}

{if false && $app->user->isRoot()}
	{call e field="owner_id"}
	{call e field="parent"}
{/if}


{call e field="fieldset" hidden_note=$editinsite}
{call e field="fieldname" note="Unik."}
{call e field="title" i18n=4}

{call e field="required" type=bool}
{call e field="type" type=select options=$item->getTypes(type) options_fix=1}
{call e field="inp_type" type=select options=$m->getInputTypes() empty_option=1 options_fix=1 rowclass=inptype}

{call e field="modpath" type=select_ajax modpath="system/modules" source_args=[byPath=>1]  rowclass=selajax hidden_note="kitokio veikimo be preload, nerodo vertes, reiktu paziuret ilgiau kad atstatyti"}


{call e field=size type=number default=2}
{call e field=config type=code_json height=200px nopading=1}  

{call e field="hidden_note" i18n=4}
{call e field="note"  i18n=4}
{call e field="placeholder"  i18n=4}
{call e field="i18n" type=bool}

{call e field="active" type=bool}



<script>
	require(['gwcms'], function(){
		$('#item__inp_type__').change(function(){
			
			var type=$(this).val();
						
			if(type=='select_ajax' || type=='multiselect_ajax'){
				$('.selajax').fadeIn();
			}else{
				$('.selajax').fadeOut();
			}
			
			
		}).change();
	})
</script>


{include file="default_form_close.tpl"}