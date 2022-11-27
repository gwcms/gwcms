{include file="default_form_open.tpl" changes_track=1 auto_save=1 form_width="100%"}

<style>
.input_label_td { width:120px; }
</style>

{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{*
{call e field=project_id type=select options=$options.project_id empty_option=1}
*}


{include 
	file="elements/input_select_edit.tpl" 
	name=project_id type=select 
	options=$options.project_id 
	empty_option=1
	datasource=$app->buildUri('todo/projects')
}


{call e field=type type=select options=GW::l('/m/TODO_ITEM_TYPE_OPT')}
{*include file="elements/input.tpl" name=job_type type=radio options=GW::l('/m/TODO_ITEM_JOB_TYPE_OPT')*}

{call e field=title}
{call e field=time_have value=gw_math_helper::uptime($item->time_have)}




{$tmpheight="{if $item->body_editor_height}{$item->body_editor_height*100}px{else}300px{/if}"}


{if $item->body_editor == 0}
	{$ck_set='minimum'}
	{$ck_options.height=$tmpheight}
	{$bodyInpType=htmlarea}
{elseif $item->body_editor == 1}
	{$bodyInpType=textarea}
	{$autoresize=1}
{elseif $item->body_editor == 2}
	{$bodyInpType=code_smarty}
	
{/if}

{call e field=description type=$bodyInpType rowclass="bodyinputs" hidden_note=$tmpnote layout=wide notr=1 height=$tmpheight}	


{*
{call e field=file1 type=file}
*}
{call e field=attachments 
	type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>5]
	preview=[thumb=>'50x50']
}


{call e field=state  type=select options=GW::l('/m/STATE_OPT')|strip_tags}


{call e field=priority type=select options=GW::l('/m/PRIORITY_OPT') default=5 data_type=numeric}
{call e field=deadline type=date}



{call e field=user_create type=select  empty_option=1 options=$options.users default=$app->user->id data_type=numeric enable_search=1}

{call e field=user_exec type=select empty_option=1 options=$options.users default=$app->user->id data_type=numeric enable_search=1}



{capture assign=tmp}
	<small class="text-muted">type</small> {call e0 title=false field=body_editor type=select options=GW::l('/m/OPTIONS/body_editor') readonly=isset($custom_cfg.body_editor_ro)}
	<small class="text-muted">height</small> {call e0 title=false field=body_editor_height type=select options=GW::l('/m/OPTIONS/body_editor_height') readonly=isset($custom_cfg.body_editor_height_ro)}	
{/capture}
{call e field="descript_area_config" type=read value=$tmp hidden_note=GW::l('/g/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}

</table>
</div> {*-end of panel body-*}
</div> {*-end of panel-*}

	{include file="tools/form_submit_buttons.tpl"}

</form>
{if $update}
	{include file="extra_info.tpl" extra_fields=[insert_time,update_time]}
{/if}


{if  $item->id}
<br /><br />

<table class="gwTable mar-top" style="width:100%">
	<th colspan="2" class="th_h3 th_single">{GW::l('/m/VIEWS/comments')}:</th>

	<tr>
		<td style="border: 1px solid white;background-color:#f9f9f9">

			
			<iframe id="comments" style="width:100%;height:250px;" src="{$app->buildURI("todo/items/`$item->id`/form/`$m->parent->id`/comments?clean=2")}" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
			
			
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

</table>








{include file="default_close.tpl"}