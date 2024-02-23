
	</tbody>
</table>
</div>
</div>


	{include file="tools/form_submit_buttons.tpl"}


	{function name="df_after_form"}
	{/function}

	{call name="df_after_form"}
	
	
	{if $comments}
		{if  $item->id}
		<br /><br />

		<table class="gwTable mar-top" style="width:100%">
			<th colspan="2" class="th_h3 th_single">{GW::l('/g/VIEWS/comments')}:</th>

			<tr>
				<td style="border: 1px solid white;background-color:#f9f9f9">


					<iframe id="comments" style="width:100%;height:250px;" src="{$app->buildURI("datasources/comments/list?obj_type={$item->model->table}&obj_id={$item->id}&clean=2")}" frameborder="0" scrolling="no" allowtransparency="true"></iframe>

					<script type="text/javascript">
						require(['gwcms'],function(){
							/*add debug to look for bugs, debug:true*/
							gwcms.initAutoresizeIframe('#comments', { minHeight: 100, heightOffset: 0, fixedWidth:true, interval:1000})
						})					
					</script>
				</td>
			</tr>	


		</table>
		{/if}
		
	{/if}


{if $update}
	{include file="extra_info.tpl"}
{/if}

</table>

</form>



{if $app->page->info->itemactions && $smarty.get.clean}
	<div style="display:none;">
		<div id="itemactions_hidden" style="display:inline-block;margin-left:10px;" >

		{include "tools/ajaxdropdown.tpl" item=[actions=>$m->buildUri("{$item->id}/itemactions")]}

		</div>
	</div>
{/if}
