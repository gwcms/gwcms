


</table>
	{include file="tools/form_submit_buttons.tpl"}



{if $app->smarty->template_functions.df_after_form}
	{call name="df_after_form"}
{/if}

{if $update}
	{include file="extra_info.tpl"}
{/if}

</table>

</form>


{include file="default_close.tpl"}