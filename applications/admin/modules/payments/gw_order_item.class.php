<?php
class GW_Order_Item extends GW_Composite_Data_Object
{
	
	
	public $calculate_fields = [
	    'obj'=>1,
	    'total'=>1,
	    'expirable'=>1,
	    'expires_secs'=>1,
	    'is_expired'=>1,
	    'title'=>1,
		'type'=>1,
		'invoice_line'=>1,
	    	'door_code'=>1,
	    'coupon_codes'=>1,
	    'vat_title'=>1,
	    'vat_part'=>1
	];
	
	public $composite_map = [
		'order' => ['gw_composite_linked', ['object'=>'GW_Order_Group','relation_field'=>'group_id']],
	];	
	
	
	public $ownerkey = 'payments/orderitems';
	public $extensions = [
	    'keyval'=>1,
	    'changetrack'=>1
	];				
	public $keyval_use_generic_table = 1;
	public $preserve_changetrack_on_delete = true;
	public $ignored_change_track = ['update_time'=>1];
	
	//used in ordered items
	public $ignore_fields = [
		'modpath' => 1,
		'insert_change_track_context' => 1,
		'delete_change_track_context' => 1,
	];		
	
		
	static function getVatGroupsPerc()
	{
		static $cache;
		
		if(!$cache)
			$cache = GW_VATgroups::singleton()->getOptionsPercent();
		
		return $cache;
	}
	
	static function getVatGroups()
	{
		static $cache;
		
		if(!$cache)
			$cache = GW_VATgroups::singleton()->getOptions();
		
		return $cache;
	}	

	
	function getChangeTrackSnapshot()
	{
		$values = [];
		
		foreach(array_keys($this->getColumns()) as $field){
			if($field == 'update_time')
				continue;
			
			$values[$field] = $this->getStoreVal($field);
		}
		
		return $values;
	}

	function insertManualChangeTrack($old, $new, $context)
	{
		$context = is_array($context) ? $context : ['note' => $context];
		
		$item = GW_Change_Track::singleton()->createNewObject();
		
		if($last = $item->find([
			'owner_type=? AND owner_id=? AND user_id=? AND last=1 AND undone=0',
			$this->ownerkey,
			(int)$this->id,
			GW::$context->app->user->id ?? -1,
		])){
			$last->last = 0;
			$last->updateChanged();
		}
		
		$item->owner_type = $this->ownerkey;
		$item->owner_id = (int)$this->id;
		$item->user_id = GW::$context->app->user->id ?? -1;
		$item->old = $old;
		$item->new = $new;
		$item->last = 1;
		
		if($context['note'] ?? false)
			$item->note = $context['note'];
		
		if($context['transaction_id'] ?? false)
			$item->transaction_id = (int)$context['transaction_id'];
		
		$item->insert();
	}

	function buildOrderItemTrackContext($action_type, $note)
	{
		if(!$this->group_id)
			return ['note' => $note];
		
		$user = GW::$context->app->user ?? false;
		$user_id = $user ? (int)$user->id : -1;
		
		$tx = GW_Change_Transaction::singleton()->createNewObject();
		$tx->action_type = $action_type;
		$tx->context_obj_type = 'gw_order_item';
		$tx->context_obj_id = (int)$this->id;
		$tx->order_id = (int)$this->group_id;
		$tx->user_id = $user_id;
		$tx->status = 'completed';
		$tx->note = $note;
		$tx->meta = [
			'order_id' => (int)$this->group_id,
			'order_item_id' => (int)$this->id,
		];
		$tx->insert();
		
		return [
			'id' => (int)$tx->id,
			'note' => $note,
			'transaction_id' => (int)$tx->id,
		];
	}

	function ensureOrderItemTrackContext(&$context, $action_type, $note)
	{
		if(!is_array($context))
			$context = ['note' => $context];
		
		if(!empty($context['transaction_id']))
			return $context;
		
		$context = $this->buildOrderItemTrackContext($action_type, $note);
		
		return $context;
	}

	function getDeleteTrackContext()
	{
		$context = $this->delete_change_track_context ?? [];
		
		if(!is_array($context))
			$context = ['note' => $context];
		
		$context['note'] = $context['note'] ?? 'Order item deleted';
		$this->ensureOrderItemTrackContext($context, 'order_item_deleted', $context['note']);
		
		return $context;
	}
	
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case 'BEFORE_INSERT':
				if(!$this->invoice_line2)
					$this->invoice_line2 = $this->invoice_line;
			break;
			case 'AFTER_INSERT':
				if(!isset($this->insert_change_track_context))
					$this->insert_change_track_context = $this->buildOrderItemTrackContext('order_item_created', 'Order item created');
				
				$this->insertManualChangeTrack([], $this->getChangeTrackSnapshot(), [
					'note' => $this->insert_change_track_context['note'] ?? 'Order item created',
					'transaction_id' => $this->insert_change_track_context['transaction_id'] ?? (is_array($context_data) ? ($context_data['transaction_id'] ?? null) : null),
				]);
			break;
			case 'BEFORE_CHANGES':
				$this->ensureOrderItemTrackContext($context_data, 'order_item_updated', 'Order item updated');
			break;
			case 'AFTER_SAVE':
			
				if($this->order instanceof GW_Order_Group) {
					$this->order->fireEvent('BEFORE_CHANGES');
					$this->order->updateTotal();
				}
			break;
			case 'BEFORE_DELETE':
				$this->insertManualChangeTrack($this->getChangeTrackSnapshot(), [], $this->getDeleteTrackContext());
			break;
			case 'AFTER_DELETE':
				if($this->group_id) {
					$order = GW_Order_Group::singleton()->find(['id=?', $this->group_id]);
					
					if($order)
						$order->updateTotal();
				}
			break;
			

		}
		
		parent::eventHandler($event, $context_data);
	}	
	
	function calculateField($name) {
		
		switch ($name)
		{
			case "obj":
				$class = $this->obj_type;
				
				if(!$class)
					return false;
				
				if($class)				
					return $class::singleton()->createNewObject($this->obj_id, true);
			break;
			case "total":
				return $this->unit_price * $this->qty;
			break;	
			case 'expires_secs':
				return strtotime($this->expires) - time();
			break;

			case 'title':
				if($this->id)
					return $this->type. ' - '.$this->obj->title;
			break;
			case 'type':
				return GW::ln("/g/CART_ITM_{$this->obj_type}");
			break;
			case  'invoice_line':
				return $this->obj->invoice_line ?: $this->obj->title;
			break;
		
			case 'expirable':
				return $this->expires && strpos($this->expires, "0000-00-00")===false;
			break;
			case 'is_expired':
				return  $this->expirable  && $this->expires_secs < 0;
			break;
			case 'door_code';
				return gw_ttlock_codes::singleton()->createNewObject($this->get('keyval/door_code_id'), true)->code;
			break;	
			case 'coupon_codes':
				return explode(',',$this->keyval->coupon_codes);
			break;
			
			case 'vat_title':
				$opt=$this->getVatGroups();
				return $opt[$this->vat_group] ?? ''; 
			break;
			case 'vat_part':
				if(!$this->vat_group)
					return '-';
				
				$percents=$this->getVatGroupsPerc();
				
				//d::dumpas($percents);
				
				if(isset($percents[$this->vat_group]) && $percents[$this->vat_group]);
					return round($this->total - $this->total/((100+$percents[$this->vat_group])/100), 2);
					
				return '-';
				
			break;
		
		}
		
		parent::calculateField($name);
	}	
		
	
}
