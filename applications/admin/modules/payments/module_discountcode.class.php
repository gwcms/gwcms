<?php



class Module_DiscountCode extends GW_Common_Module
{		
	
	function init()
	{
	
		parent::init();	
	}	
	
	
		
	
	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->title;  },
		    'search_fields'=>['code']
		];	
		
		return $opts;	
	}
	
	
	
	function __eventAfterList(&$list)
	{
		/*
		foreach($list as $item){
			$item->element_count = GW_Form_Elements::singleton()->count('owner_id='.(int)$item->id);
			$item->answer_count = GW_Form_Answers::singleton()->count('owner_id='.(int)$item->id);
		}
		*/
	}
	
	

	function __eventBeforeClone($ctx)
	{		
		$ctx['dst']->code = Shop_DiscountCode::singleton()->getUniqueCode();
		$ctx['dst']->used = 0;
		$ctx['dst']->user_id = 0;
	}
		

	function __eventBeforeDelete($item)
	{
		$this->recoveryEmail($item);
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['changetrack'] = 'L';
	
		$cfg['filters']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
		$cfg['inputs']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
	
		return $cfg;
	}

	function __eventAfterInsert($item){
		//kad pažymėti kas sukurė
		$this->addItemComment($item, "Sukūriau");
	}
	
	function __eventAfterForm()
	{
		$this->tpl_vars['comments']=1;
	}

	function getUniqueCodeWithPrefix($prefix, $length)
	{
		$prefix = strtoupper(trim($prefix));
		$retry = 10000;

		while($retry-- > 0){
			$code = $prefix.GW_String_Helper::getRandString($length, GW_String_Helper::$simple);

			if(!Shop_DiscountCode::singleton()->count(['code=?', $code]))
				return $code;
		}

		return false;
	}

	function doCreateMultipleCodes()
	{
		$form = [
			'fields'=>[
				'qty'=>[
					'type'=>'number',
					'required'=>1,
					'default'=>10,
					'note'=>'Kiek kodų sugeneruoti',
				],
				'codepre'=>[
					'type'=>'text',
					'required'=>0,
					'note'=>'Kodo prefiksas, pvz. WFIMC',
				],
				'amount'=>[
					'type'=>'number',
					'step'=>0.01,
					'required'=>1,
					'note'=>'Vieno kupono suma',
				],
				'chars'=>[
					'type'=>'number',
					'required'=>1,
					'default'=>6,
					'note'=>'Atsitiktinės kodo dalies simbolių skaičius',
				],
			],
			'cols'=>1,
		];

		if(!($answers = $this->prompt($form, 'Generuoti kuponų kodus', ['method'=>'post'])))
			return false;

		$qty = max(1, min(1000, (int)$answers['qty']));
		$amount = round((float)$answers['amount'], 2);
		$chars = max(3, min(32, (int)$answers['chars']));
		$prefix = $answers['codepre'] ?? '';

		if($amount <= 0){
			$this->setError('Amount must be greater than zero');
			return $this->jumpAfterSave();
		}

		$codes = [];

		for($i = 0; $i < $qty; $i++){
			$code = $this->getUniqueCodeWithPrefix($prefix, $chars);

			if(!$code){
				$this->setError('Failed to generate unique code');
				break;
			}

			$item = Shop_DiscountCode::singleton()->createNewObject();
			$item->code = $code;
			$item->limit_amount = $amount;
			$item->used_amount = 0;
			$item->percent = 100;
			$item->products = '';
			$item->active = 1;
			$item->user_id = 0;
			$item->note = 'Generated '.date('Y-m-d H:i:s');
			$item->insert();

			$codes[] = $code;
		}

		if($codes)
			$this->setMessage('Codes generated: '.implode(', ', $codes));

		$this->app->jump($this->app->page->path);

	}
}
