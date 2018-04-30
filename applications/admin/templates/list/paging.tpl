{if $m->list_params['page_by']}
	{$paging=$m->getPagingData()}

	{if $paging.length > 1}

		{assign var="paging_tpl_page_count" value=$paging.length scope=parent}	



				

				<td  style="padding-right:15px;">
							
				{if $paging.length <= 10}
						
						<div class="btn-group gwPagination">
						{if $paging.prev}<a class="btn btn-default" href="javascript:gw_adm_sys.change_page({$paging.prev})">‹</a>{/if}
						
						{for $i=1;$i<=$paging.length;$i++}
								<a  class="btn btn-default {if $i==$paging.current}active{/if}" href="#{$i}" onclick="return gw_adm_sys.change_page({$i})">{$i}</a>
						{/for}
						
						{if $paging.next}<a class="btn btn-default" href="javascript:gw_adm_sys.change_page({$paging.next})">›</a>{/if}
							
						
					</div>
				{else}
					
					<div class="btn-group pagebybtns">
						{if $paging.first && $paging.prev!=1}<a class="btn btn-default" href="#{$paging.first}" onclick="return gw_adm_sys.change_page(1)">1</a>{/if}
						{if $paging.prev}<a class="btn btn-default" href="#{$paging.prev}" onclick="return gw_adm_sys.change_page({$paging.prev})">{$paging.prev}</a>{/if}
					
						<input class="form-control gwDinamicPageNr bg-primary" name="list_params[page]" value="{$paging.current}" onchange="gw_adm_sys.change_page(this.value)" />

					{if $paging.next}<a class="btn btn-default" href="#{$paging.next}" onclick="return gw_adm_sys.change_page({$paging.next})">{$paging.next}</a>{/if}
					{if $paging.last && $paging.next != $paging.last}<a class="btn btn-default" href="#{$paging.last}" onclick="return gw_adm_sys.change_page({$paging.last})">{$paging.last}</a>{/if}
				</div>

			{/if}
			</td>

{/if}
{/if}