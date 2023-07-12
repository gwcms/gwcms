{function input}
	{if $params_expand}
		{foreach $params_expand as $k => $v}
			{assign var=$k value=$v}
		{/foreach}
		{$params_expand=[]}
	{/if}	
	
	{if $type=='checkboxes' || $type=="bsmultiselect"}
		{$multiple=1}
	{/if}
	
	{if !$input_name_pattern}
		{$input_name_pattern="item[%s]"}
		{if $multiple}{$input_name_pattern="`$input_name_pattern`[]"}{/if}
	{/if}
	
	{$input_name=sprintf($input_name_pattern,$field)}

	
	{if !$title && $title!==false}
		{if !isset($fields_source)}
			{$fields_source="/m/FIELDS/"}
		{/if}
		{$title=gw::ln("{$fields_source}{$field}")}
	{/if}
	{if !isset($value) && is_object($item)}
		{$value=$item->$field}
	{/if}
	
	{if $value!=='0' && !$value && $default}
		{$value=$default}
	{/if}
	
	{if !$type}
		{$type='text'}
	{/if}
	

	
	{if !$id}
		{$id=str_replace(["[","]",'/'],'_',$input_name)}
	{/if}
	{if $type=='url' && !$placeholder}
		{$placeholder='http://'}
	{/if}
	
	{if !isset($required) && $required_fields[$field]}
		{$required=1}
	{/if}
		      
	
	
	
					
				
	{if $type==checkbox}
		
	<div class="mb-3 {if $m->error_fields.$field}u-has-error-v1 has-feedback{/if}">
                  <label class="form-check-inline u-check g-color-gray-dark-v5 g-font-size-13 g-pl-25 mb-2">
                    <input 
			class="g-hidden-xs-up {if $addclass} {$addclass}{/if}"
			    name="{$input_name}" 
			    type="checkbox"  
			    	id="{$id}" 
			    value="1" {if $required}required="1"{/if} {if $value}checked="checked"{/if}
			    {if $disabled}disabled="disabled"{/if}
			    />
		    
                    <span class="d-block u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
                      <i class="fa" data-check-icon=""></i>
                    </span>
                    {$title} {if $note}<i>({$note})</i>{/if} {if $note_raw}{$note_raw}{/if} {if $help}<i class="fa fa-question-circle" onclick="alert('{$help|escape:js}')"></i>{/if} 
		    
		    {if $longtext}
			 <div style="padding:10px;border:1px solid #eee;float:right;margin-right:10px;"> {$longtext} </div>
		     {/if}
		     
		    {if $required}<sup title="{GW::ln('/G/validation/REQUIRED')}">*</sup>{/if}
		    

                  </label>
                </div>			
			
	{else}
	<div class="form-group {if $m->error_fields.$field}u-has-error-v1 has-feedback{/if}" {if $type==hidden}style='display:none'{/if}>
		{if $type!=hidden && $title!==false}<label class="control-label" for="{$id}" 
		       {if $help} data-original-title="{$help|escape:'html'}" rel="tooltip" class="btn btn-default" data-toggle="tooltip" data-placement="top" title=""{/if}
		       >{$title} {if $required}<sup title="{GW::ln('/G/validation/REQUIRED')}">*</sup>{/if} {if $help}<i class="fa fa-question-circle primary-color"></i>{/if} {if $note}<small style="font-weight: normal;font-style: italic">{$note|escape}</small>{/if} {if $note_raw}{$note_raw}{/if} </label>{/if}
		       
		
		{if in_array($type, ['text','email','password','url','hidden','number'])}
			{include file="inputs/input_text.tpl"}
		{elseif $type=='radios' || $type=='radio'}
			<br />
			
			{foreach $options as $key => $opttitle}
				<div class="form-check form-check-inline mb-0">
				<label class="form-check-label mr-2">
					<input type="radio" name="{$input_name}" value="{$key|escape}" 
					       {if $value==$key}checked="checked"{/if} 
					       {if $required}required="1"{/if} 
					       class="form-check-input mr-1"
					       >{$opttitle}
				</label>
				</div>
				{if $newline}<br>{/if}
			{/foreach}

			{if $onchangeFunc}
				{capture append=footer_hidden}
				<script type="text/javascript">
					$('input[type=radio][name="{$input_name}"]').change(function() {
						{$onchangeFunc}(this.value, this);
					});
				</script>
				{/capture}

			{/if}			
			
		{elseif $type=='checkboxes'}

			{$selected=$value}
			{if is_array($selected)}
				{$selected=array_flip($selected)}
			{/if}
			
			<div class="row">
			{foreach $options as $key => $opttitle}
				<div class="{if $newline}col-md-12{else}col-md-4{/if}">
				 <label class="checkbox-inline"><input style="opacity:1" type="checkbox" name="{$input_name}" value="{$key|escape}" {if isset($selected[$key])}checked="checked"{/if}> {$opttitle}</label>
				</div>
				
			{/foreach}
			</div>			
					
		{elseif $type=='textarea'}
			<textarea name="{$input_name}" type="{$type}" class="form-control" id="{$id}" 
			       {if $required}required="1"{/if} 
			       {if $placeholder}placeholder="{$placeholder|escape}"{/if}
			       style="{if $width}width:'{$width}'{/if}"
			       {if $rows}rows="{$rows}"{/if}
			       {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
			       >{$value|escape}</textarea>
		{else}
			{include file="inputs/input_`$type`.tpl"}
		{/if}
		
		
		
		{if $hint}<p class="help-block">{$hint}</p>{/if}
		{if $jserror}<p class="error_label" id="{$id}_jserror"></p>{/if}
	</div>		
	{/if}
	
	
	
	{if $help && !$input_help_loaded}

		<script type="text/javascript">
		    $(function(){
		       $('[rel="tooltip"]').tooltip();
		       $('[rel="popover"]').popover();
		    });
		</script>

		{assign scope=global var=$input_help_loaded value=1}

			
	{/if}
{/function}