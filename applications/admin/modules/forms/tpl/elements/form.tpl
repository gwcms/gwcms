{include file="default_form_open.tpl" form_width="1000px"}
{$width_title=100px}


{*todo: dropdownas is vertimu perrasymu*}
{$editinsite="Vertimai susikurs saite CTLR+Q - redaguoti"}

{*
{call e field="owner_id"}
*}

{call e field="fieldset" hidden_note=$editinsite}
{call e field="fieldname" note="Unik."}
{call e field="title" i18n=4}

{call e field="required" type=bool}
{call e field="type" type=select options=$item->getTypes() empty_option=1 options_fix=1}
{call e field="options_src" type="select_ajax" modpath="datasources/classificator_types" options=[] preload=1 after_input_f="editadd"}


{call e field=size type=number default=2}
{call e field=config type=code_json height=200px nopading=1}  

{call e field="hidden_note" i18n=4}
{call e field="note"  i18n=4}
{call e field="placeholder"  i18n=4}

{call e field="active" type=bool}

{call e field="i18n" type=select options=GW::l('/m/OPTIONS/i18n')}

{call e field="linkedfields" type=multiselect options=GW::l('/m/OPTIONS/linkedfields') rowclass="field_linkedfields"}
{call e field="selectcfg" type=multiselect options=[hidden_note,note,placeholder,config,size,linkedfields] options_fix=1}



<script>
	require(['gwcms'], function(){
		$('#item__selectcfg______').change(function(){			
			
		
			$(this).find('option').each(function(){
				
				var value = $(this).val()
				
				$('#gw_input_item__'+value+'__, .field_'+value).toggle($(this).is(":selected"))
						
			})
			
			
		}).change()	
		
		$('#item__type__').change(function(){			
			
			var enable_options = ['radio','select','checkbox'].indexOf($(this).val())!=-1
			
			$('#gw_input_item__options_src__').toggle(enable_options);
			
			
			
		}).change()
	})
</script>

{include file="default_form_close.tpl"}