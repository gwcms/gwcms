{include file="default_form_open.tpl"}


{capture assign=modopts}
	
      <span class="input-group-btn">	

		
	<div class="btn-group">
	    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
	     <span class="caret"></span></button>
	    <ul class="dropdown-menu" role="menu">
		    {foreach $options.module as $tmp}
			<li><a href="javascript:setModule('{$tmp}')">{$tmp}</a></li>
		{/foreach}
	    </ul>
	  </div>		
		
      </span>
	<script>function setModule(val){ $('#itemform input[name="item[module]"]').val(val); }</script>
	    
<style>
	
	.input-group { height: 32px; }
</style>		
{/capture}


{call e field=module after_input=$modopts}
{call e field=key}


{foreach GW::$settings.LANGS as $lncode}
	{call e field="value_$lncode" type=textarea height="50px"}
{/foreach}	






{include file="default_form_close.tpl"}