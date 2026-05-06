<?php

class Module_ChangeTransactions extends GW_Common_Module
{
	function init()
	{
		$this->model = GW_Change_Transaction::singleton();
		
		parent::init();
		
		if(isset($_GET['order_id']))
			$this->filters['order_id'] = (int)$_GET['order_id'];
		
		if(isset($_GET['transaction_id']))
			$this->filters['id'] = (int)$_GET['transaction_id'];
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['order_id'] = 1;
		$this->app->carry_params['transaction_id'] = 1;
		
		if(isset($this->filters['order_id']) || isset($this->filters['id']))
			$this->list_params['paging_enabled'] = false;
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields'] = [
			'id' => 'Lof',
			'changetrack_count' => 'L',
			'action_type' => 'Lof',
			'status' => 'Lof',
			'note' => 'L',
			'user_id' => 'Lof',
			'username' => 'Lf',
			'context_obj_type' => 'Lof',
			'context_obj_id' => 'Lof',
			'order_id' => 'Lof',
			'insert_time' => 'Lof',
			'update_time' => 'Lof',
		];
		
		if(isset($this->filters['order_id']))
			unset($cfg['fields']['order_id']);
		
		$cfg['filters']['transaction_id'] = ['type' => 'text'];
		
		return $cfg;
	}
	
	function __eventBeforeListParams(&$params)
	{
		$params['key_field'] = 'id';
		$params['select'] = "a.*, usr.username, TRIM(CONCAT(COALESCE(usr.name,''), ' ', COALESCE(usr.surname,''))) as usertitle";
		$params['joins'] = [
			['left', 'gw_users AS usr', 'a.user_id = usr.id'],
		];
		$params['order'] = 'a.id DESC';
	}
	
	function __eventAfterList(&$list)
	{
		if(isset($this->filters['id'])){
			$this->tpl_vars['transaction'] = $list[$this->filters['id']] ?? $this->model->find(['id=?', $this->filters['id']]);
		}
	}

	protected function buildUndoTrackContext($transaction, $reason)
	{
		$user = $this->app->user;
		$username = trim((string)($user->username ?? ''));
		
		if(!$username)
			$username = trim((string)($user->title ?? ''));
		
		if(!$username)
			$username = 'user#'.(int)$user->id;
		
		$note = "Grouped undo for transaction #{$transaction->id} by {$username}: {$reason}";
		
		$tx = GW_Change_Transaction::singleton()->createNewObject();
		$tx->action_type = 'group_undo';
		$tx->context_obj_type = 'gw_change_transactions';
		$tx->context_obj_id = (int)$transaction->id;
		$tx->order_id = (int)$transaction->order_id;
		$tx->user_id = (int)$user->id;
		$tx->status = 'started';
		$tx->note = $note;
		$tx->meta = [
			'original_transaction_id' => (int)$transaction->id,
			'reason' => $reason,
		];
		$tx->insert();
		
		return [
			'id' => (int)$tx->id,
			'note' => $note,
			'transaction_id' => (int)$tx->id,
		];
	}

	protected function completeTrackContext($context, $status='completed', $meta=[])
	{
		$txid = (int)($context['id'] ?? $context['transaction_id'] ?? 0);
		
		if(!$txid)
			return false;
		
		$tx = GW_Change_Transaction::singleton()->find(['id=?', $txid]);
		
		if(!$tx)
			return false;
		
		$tx->status = $status;
		
		if($meta){
			$current_meta = (array)$tx->meta;
			$tx->meta = array_merge($current_meta, $meta);
		}
		
		$tx->updateChanged();
		return $tx;
	}
	
	function getTransactionChanges($transaction_id)
	{
		return GW_Change_Track::singleton()->findAll(
			['transaction_id=? AND undone=0', (int)$transaction_id],
			['order' => 'id DESC']
		);
	}
	
	protected function getOwnerGroupKey($change)
	{
		return $change->owner_type.'|'.$change->owner_id.'|'.$change->user_id;
	}
	
	function getGroupUndoCheck($transaction)
	{
		$transaction_id = is_object($transaction) ? (int)$transaction->id : (int)$transaction;
		$changes = $this->getTransactionChanges($transaction_id);
		
		if(!$changes){
			return ['ok'=>false, 'message'=>'Group undo is not possible because no active changetrack records were found for this transaction.'];
		}
		
		$grouped = [];
		foreach($changes as $change){
			$grouped[$this->getOwnerGroupKey($change)][] = $change;
		}
		
		foreach($grouped as $group_changes){
			$first = $group_changes[0];
			$all = GW_Change_Track::singleton()->findAll(
				[
					'owner_type=? AND owner_id=? AND user_id=? AND undone=0',
					$first->owner_type,
					$first->owner_id,
					$first->user_id
				],
				['order' => 'id DESC']
			);
			
			$expected_ids = array_map(function($change) {
				return (int)$change->id;
			}, $group_changes);
			
			$top_ids = array_map(function($change) {
				return (int)$change->id;
			}, array_slice($all, 0, count($expected_ids)));
			
			if($expected_ids !== $top_ids){
				return [
					'ok' => false,
					'message' => 'Group undo is not possible because changetrack for object is not last, so it is possible only manual way.',
				];
			}
		}
		
		return ['ok'=>true, 'changes'=>$changes];
	}
	
	protected function loadObjectByChange($change)
	{
		if($change->owner_type == 'competitions/particservices'){
			$fields = array_keys((array)$change->old + (array)$change->new);
			$ordered_service_markers = ['order_id', 'order_item_id', 'payd_time', 'canceled'];
			
			if(array_intersect($fields, $ordered_service_markers)){
				return Adb_Ordered_Participant_Services::singleton()->find(['id=?', $change->owner_id]);
			}
		}
		
		$page = GW_ADM_Page::singleton()->getByPath($change->owner_type.'/'.$change->owner_id);
		
		if(!$page)
			return false;
		
		return $page->getDataObject();
	}
	
	protected function undoChangeRecord($change, $track_context=[])
	{
		$obj = $this->loadObjectByChange($change);
		
		if(!$obj)
			throw new Exception("Could not load object {$change->owner_type}/{$change->owner_id} for undo");
		
		$prev = (array)$change->old;
		$obj->fireEvent('BEFORE_CHANGES', $track_context);
		$obj->setValues($prev);
		$obj->update(array_keys($prev), ['onlychanged'=>1]);
		
		$change->undone = 1;
		$change->last = 0;
		$change->updateChanged();
	}
	
	function doGroupUndo()
	{
		$transaction = $this->getDataObjectById();
		
		if(!$transaction){
			$this->setError('/g/DATA_NOT_FOUND');
			return $this->jump();
		}
		
		$check = $this->getGroupUndoCheck($transaction);
		
		if(!$check['ok']){
			$this->setError($check['message']);
			return $this->jumpAfterSave();
		}
		
		$form = [
			'fields' => [
				'reason' => ['type' => 'text', 'required' => 1],
			],
			'cols' => 1,
		];
		
		if(!($answers = $this->prompt($form, "Undo grouped changetrack transaction #{$transaction->id}", ['method' => 'post'])))
			return false;
		
		$reason = trim((string)($answers['reason'] ?? ''));
		$track_context = $this->buildUndoTrackContext($transaction, $reason);
		
		foreach($check['changes'] as $change){
			$this->undoChangeRecord($change, $track_context);
		}
		
		$transaction->status = 'undone';
		$transaction->updateChanged();
		
		$this->completeTrackContext($track_context, 'completed', [
			'original_transaction_id' => (int)$transaction->id,
			'changes_count' => count($check['changes']),
		]);
		
		$this->setMessage("Group undo completed for transaction #{$transaction->id}");
		return $this->jumpAfterSave();
	}
}
