{include file="default_form_open.tpl" form_width=1000px}

<style>
.input_label_td { width:120px; }
</style>

{include file="elements/input.tpl" name=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}


{include file="elements/input.tpl" name=project_id type=select options=$options.project_id empty_option=1}
{include file="elements/input.tpl" name=type type=select options=$m->lang.TODO_ITEM_TYPE_OPT}
{*include file="elements/input.tpl" name=job_type type=radio options=$m->lang.TODO_ITEM_JOB_TYPE_OPT*}

{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=time_have value=gw_math_helper::uptime($item->time_have)}

{include file="elements/input.tpl" name=description type=textarea height="100px" autoresize=1}

{include file="elements/input.tpl" name=file1 type=file}



{include file="elements/input.tpl" type=select name=state options=$m->lang.STATE_OPT|strip_tags}


{include file="elements/input.tpl" type=select name=priority options=$m->lang.PRIORITY_OPT default=5 data_type=numeric}
{include file="elements/input.tpl" type=date name=deadline}



{include file="elements/input.tpl" type=select name=user_create empty_option=1 options=$options.users default=$app->user->id data_type=numeric enable_search=1}

{include file="elements/input.tpl" type=select name=user_exec empty_option=1 options=$options.users default=$app->user->id data_type=numeric enable_search=1}


</table>
</div> {*-end of panel body-*}
</div> {*-end of panel-*}

	{include file="tools/form_submit_buttons.tpl"}

</form>
{if $update}
	{include file="extra_info.tpl" extra_fields=[insert_time,update_time]}
{/if}


{if $item->id}
<br /><br />

<table class="gwTable mar-top" style="width:100%">
	<th colspan="2" class="th_h3 th_single">{GW::l('/m/VIEWS/comments')}:</th>

	<tr>
		<td style="border: 1px solid white;background-color:#f9f9f9">

			
			<iframe id="comments" style="width:100%;height:250px;" src="{$app->buildURI("todo/items/`$item->id`/form/`$m->parent->id`/comments")}" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
			
			
			<script type="text/javascript">
				require(['gwcms'],function(){
					$(function() {				
						gwcms.initAutoresizeIframe('#comments', { minHeight: 100, heightOffset: 20, debug:true })
					});	


				})					
			</script>			
			
		</td>
	</tr>	
	

</table>
{/if}

</table>


{include file="default_close.tpl"}