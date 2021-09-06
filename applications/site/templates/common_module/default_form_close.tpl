                <div class="row justify-content-md-center">		
                  <div class="col-md-6">
                <button class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 mb-4" type="button" onclick="$('#itemForm').submit()"> <i class='fa fa-floppy-o'></i> {GW::ln('/g/SAVE')}</button>
		</div>	
		
		</form>

		{if !$smarty.get.clean}
	</div>
		</div>
	</div>
	
</div>
		{/if}
{include "default_close.tpl"}