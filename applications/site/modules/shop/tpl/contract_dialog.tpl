{if !$gw_shop_contract_dialog_loaded}
	{$gw_shop_contract_dialog_loaded=1 scope=global}
	<script>
	(function(){
		if(window.gwShopContractDialogInit)
			return;
		
		window.gwShopContractDialogInit = true;
		
		function setHidden(form, name, value){
			var input = form.querySelector('input[name="' + name + '"]');
			
			if(!input){
				input = document.createElement('input');
				input.type = 'hidden';
				input.name = name;
				form.appendChild(input);
			}
			
			input.value = value;
		}
		
		window.returnAnswer = function(answerId, secret){
			var form = window.gwShopContractPendingForm;
			
			if(!form)
				return;
			
			setHidden(form, 'contract_answer_id', answerId);
			setHidden(form, 'contract_answer_secret', secret);
			window.gwShopContractPendingForm = null;
			form.submit();
		};
		
		document.addEventListener('submit', function(e){
			var form = e.target;
			
			if(!form || !form.getAttribute)
				return;
			
			var url = form.getAttribute('data-contract-dialog-url');
			
			if(!url || form.querySelector('input[name="contract_answer_id"]'))
				return;
			
			var opener = false;
			
			if(window.GW && GW.open_dialog2){
				opener = function(){ GW.open_dialog2({ url: url, width: '95vw', height: '90vh' }); };
			}else if(window.gwcms && gwcms.open_dialog2){
				opener = function(){ gwcms.open_dialog2({ url: url, iframe: 1, width: '95vw', height: '90vh' }); };
			}
			
			if(!opener)
				return;
			
			e.preventDefault();
			window.gwShopContractPendingForm = form;
			opener();
		}, true);
	})();
	</script>
{/if}
