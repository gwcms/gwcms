


</table>
	{include file="tools/form_submit_buttons.tpl"}
</form>


{if GW::$smarty->template_functions.df_after_form}
	{df_after_form}
{/if}

{if $update}
	{include file="extra_info.tpl"}
{/if}

</table>




{include file="default_close.tpl"}