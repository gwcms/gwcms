<?php

die('code sample');

class Module_UsersList extends GW_Public_Module {

	function init() {
		$this->model = new GW_Customer;

		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		//GW::$devel_debug = true;


		$this->paging_enabled = 1;
		$this->list_params['page_by'] = 30;
		$this->list_params['paging_enabled'] = 1;
		$this->list_params['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

		$this->options['countries'] = GW_Country::singleton()->getOptions('en');
		

		$this->links['public_profile'] = GW::s('SITE/PATH_TRANS/users/userslist/_') . '/profile';
		
		$this->filters =& $this->app->sess['userlistfilters'];
		$this->tpl_vars['filters'] =& $this->filters;
		
		
		if(!isset($this->filters['gender']) && $this->app->user)
			$this->filters['gender']  = $this->app->user->gender=="M" ? "f":"m";
		
				
	}
	
	function getFiltersCond()
	{
		$cond=[];
		
		foreach(['gender','location_country','location_subregion','location_city'] as $fltrname)
		{
			if(isset($this->filters[$fltrname]) && $this->filters[$fltrname])
				$cond[$fltrname]=$this->filters[$fltrname];
					
		}
		

		
		return  $cond ? GW_DB::prepare_query(GW_DB::buidConditions($cond)) : '';
	}
	

	function getList($extra_cond = '') {
		
		if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
			$page = $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
			$params['offset'] = $this->list_params['page_by'] * $page;
			$params['limit'] = $this->list_params['page_by'];
		}

		$extra_cond = GW_DB::prepare_query($extra_cond);
		$extra_cond = $extra_cond ? "($extra_cond) AND " : "";
		
		$params['key_field']='id';
		$params['order']='last_request_time DESC';

		$cond = $extra_cond . 'active=1 AND profile_img_id>0';
		$list = $this->model->findAll($cond, $params);


		$this->tpl_vars['list'] = $list;

		$this->setUpPaging($this->model->lastRequestInfo());

		return $list;
	}
	
	function loadApproves(){
		
		$approves = DG_PrivatePhoto_Request::singleton()->findAll(['from_id=? AND approved=1 AND approve_seen=0',$this->app->user->id]);
		
		foreach($approves as $idx => $item){
			$item->user = GW_Customer::singleton()->find(['id=?', $item->from_id]);
			
			if(!$item->user)
				unset($approves[$idx]);
		}

		$this->tpl_vars['ppr_approves'] = $approves;
	}

	function viewDefault() {
		

		$this->userRequired();
		/*
		 * kitas sprendimas dabar jau
		if(!$this->app->user->profile_complete)
		{
			$this->app->setMessage('/M/USERS/PLEASE_COMPLETE_PROFILE');			
			
			$this->app->jump(GW::s('SITE/PATH_PROFILE'));
		}		
		*/
		
		
		if($this->app->user_updates['new_private_photo_approves'])
			$this->loadApproves();

		$cond = $this->getFiltersCond();
		
		
		$list = $this->getList($cond);

		$this->tpl_name = 'list';
	}



	function __viewProfileGetImages($userid) {

		/*
		  if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
		  $page = $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
		  $params['offset'] = $this->list_params['page_by'] * $page;
		  $params['limit'] = $this->list_params['page_by'];
		  }
		 * 
		 */

		//$extra_cond = GW_DB::prepare_query($extra_cond);
		//$extra_cond = $extra_cond ? "($extra_cond) AND " : "";

		$params = [];
		$params['order'] = 'priority ASC';

		$list = GW_SiteUser_Pic::singleton()->findAll(['user_id=?', $userid], $params);
		
	
		$approve = !$this->app->user ? false: DG_PrivatePhoto_Request::singleton()->find(['from_id=? AND to_id=?',$this->app->user->id, $userid]);
		
		if($approve && !$approve->approve_seen){
			$approve->saveValues(['approve_seen'=>1, 'approve_seen_time'=>date('Y-m-d H:i:s')]);
		}
		
		$this->tpl_vars['ppr_approve'] = $approve;
		
		foreach($list as $item)
			$this->tpl_vars['images_list_'.($item->public?'public':'private')][] = $item;
	
	}

	function viewProfile() {

		$this->options['nationality_code'] = GW_Country::singleton()->getOptions('en');
		$this->options['languages'] = GW_Data_Language::singleton()->getOptions('name');


		$id = (int) $_GET['id'];

		$item = GW_Customer::singleton()->find(['active=1 AND id=?', $id]);

		if (!$item) {
			$this->setError('/m/USER_NOT_FOUND');
			$this->app->jump($this->app->page->path);
			exit;
		}


		$this->tpl_vars['item'] = $item;

		$this->links['rotate'] = $this->buildDirectUri('profile', ['act' => 'doRotateProfileImage'], ['level' => 1]);
		
		//processintu chat modulis bet gryztu atgal i si moduli
		$this->links['message_form_action'] = $this->app->buildUri(GW::s('SITE/PATH_TRANS/users/chat/_'), ['return_to'=>$this->app->page->path]);

		$this->registerProfileView($id);

		$this->__viewProfileGetImages($id);
		
		//d::dumpas($this->app->acceptMessages());
				

		$this->tpl_name = 'public_profile';
	}
	
	function doAskPrivatePhotoPermission()
	{
		$to_id = $_GET['id'];
		$from_id = $this->app->user->id;
		
		if(!$to_id){
			$this->setError('Error 2s1f3g1s32');
			goto sFinish;
		}
			
	
		$last_request=DG_PrivatePhoto_Request::singleton()->find(['from_id=? AND to_id=?',$from_id,$to_id]);
		
		if($last_request)
		{
			$this->setError(GW::l('/m/ALREADY_ASKED_AT_').$last_request->insert_time);
		}else{
			$vals=['from_id'=>$from_id, 'to_id'=>$to_id];
			$item = DG_PrivatePhoto_Request::singleton()->createNewObject($vals);
			$item->insert();
			
			$this->setPlainMessage('/m/REQUEST_WAS_SENT');
		}
		
		sFinish:
			
		$this->app->carry_params['id']=1;
		$this->app->jump();
	}

	function registerProfileView($profid) {

		if ($this->app->user && $this->app->user->id != $profid && !$this->app->user->isRoot()) {
			$cond = GW_DB::prepare_query(GW_DB::buidConditions(['profile_uid' => $profid, 'viewer_uid' => $this->app->user->id]));

			$cnt = GW::db()->fetch_result("SELECT cnt FROM dg_profile_hits WHERE " . $cond);

			GW::db()->insert("dg_profile_hits", ['profile_uid' => $profid, 'viewer_uid' => $this->app->user->id, 'time' => date('Y-m-d H:i:s'), 'cnt' => $cnt + 1], true, true);
		}
	}

	function viewProfileVisitors() {
		
		$this->userRequired();
		
		$this->tpl_name = 'profile_visitors_list';

		$visits = GW::db()->fetch_rows_key(["SELECT * FROM dg_profile_hits WHERE profile_uid=? AND `time` > NOW() - INTERVAL 3 MONTH ORDER BY `time` DESC", $this->app->user->id], 'viewer_uid');

		$ids = [];
		foreach ($visits as $row)
			$ids[$row['viewer_uid']] = $row['viewer_uid'];

		
		if ($ids) {
			$cond = GW_DB::inCondition('id', $ids);
			$list0 = $this->getList($cond, ['key_field' => 'id']);
			$list = [];

			foreach ($visits as $visit) {
				if(!isset($list0[$visit['viewer_uid']]))
					continue;
				
				$item = $list0[$visit['viewer_uid']];
				
				$item->visit_time = $visit['time'];
				$item->visit_count = $visit['cnt'];
				$list[] = $item;
			}
			
			$this->tpl_vars['list'] = $list;
		}
		
		
		$this->links['markasseen_profilevisits'] = $this->buildDirectUri(false, ['act' => 'doMarkAsSeenProfVisits']);
	}

	function doMarkAsSeenProfVisits() {

		$this->app->user->saveValues(['last_pr_visitors_check' => date('Y-m-d H:i:s')]);
		exit;
	}
	
	function doSetFilters(){
		

		$filters = isset($_REQUEST['merge']) ? array_merge($this->filters, $_REQUEST['item']) : $_REQUEST['item'];

				
		if(isset($filters['location_data']) && ($location = json_decode($filters['location_data']))){
			
			if($location->country_short)
				$filters['location_country'] = $location->country_short;
			
			if($location->subregion)
				$filters['location_subregion'] = $location->subregion;
			
			if($location->city)
				$filters['location_city'] = $location->city;			
			
		}
		

		$this->filters = $filters;
		$this->app->jump();
	}

}
