<?php
die('code sample');
class Module_Profile extends GW_Public_Module {

	function init() {
		$this->model = new GW_Customer;

		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		GW::$devel_debug = true;

		
		$this->options['nationality_code'] = GW_Country::singleton()->getOptions('en');


		$this->options['languages'] = GW_Data_Language::singleton()->getMostPopularTop(10, 'native_name');
		$this->options['languages_other'] = GW_Data_Language::singleton()->getOptions('native_name');
		
		$this->tpl_vars['minimum_dimensions'] = $this->model->composite_map['profilefoto'][1]['dimensions_min'];
	}

	function viewDefault() {

		$this->tpl_name = 'profile';

		$this->tpl_vars['item'] = $this->app->user;
	}

	function doSave() {
		$vals = $_POST['item'];



		$item = $this->app->user;

		$errors = [];

		//sudeti visas galimas vertes kad butu atskirti kurie laukeliai atejo neteisetai
		//atfiltruoti neteisetus laukelius

		$allowed = [
			"name", "birthdate", "location", "location_data", 
			"nationality_code", 'body_type', 'eye_color', 'height', 
			'aboutyou', 'languages', 'languages_other',
			'send_notifications'
		];

		$pvals = $vals;
		
		if(isset($vals['agree']))
			$item->agree = 1;
		
		
		$vals = array_intersect_key($vals, array_flip($allowed));
		
		$vals['send_notifications'] = isset($vals['send_notifications']) ? 1 : 0;
		
		$item->setValues($vals);

		$item->setValidators('update_site');

		if (!$item->validate()) {
			$errors = $item->errors;
			$this->setItemErrors($item);
			
			
		} else {
			$this->setPlainMessage("/g/UPDATE_SUCCESS");
			$item->profile_complete = 1;
			$item->save();
		}


		if ($errors) {
			
			$this->app->jump();
		}
	}

	
	function viewImagesActions()
	{
		$item = $this->app->user;
		$name = $_REQUEST['field'];
		$img = $item->get($name);
		
		
			
		if(isset($_REQUEST['action'])){
			
			if(!$item->isCompositeField($name))
				die('Error 5616515619');
						
			switch($_REQUEST['action']){
				case 'upload':

				if (isset($_FILES[$name]))
					GW_Image_Helper::__setFile($item, $name);


				if (!$item->validate()) {
					foreach ($item->errors as $errorc => $err)
						$item->errors[$errorc] = GW::l($err);

					$this->tpl_vars['errors'] = implode(', ', $item->errors);
				} else {
					$item->save();

					$img = $item->get($name);


					if (isset($img->key)) {
						$item->saveValues(['profile_img_id' => $img->id]);
					} else {
						$this->tpl_vars['errors'] = 'Error uploading image';
					}
				}
				
				break;

				case 'rotate':
					if (isset($img->key)) 
						$img->rotate(0);
				break;
				case 'remove':
					if (isset($img->key)) 
						$item->removeCompositeItem($name);
					
					$item->saveValues(['profile_img_id' => 0]);
				break;
			}
		}
		
		
		$this->tpl_vars['field'] = $name;
		$this->tpl_vars['image'] = $item->$name;
		$this->tpl_vars['item'] = $item;
		
		$this->tpl_name = 'input_image_preview';

	}


	function viewPassChange() {
		$this->tpl_name = 'passchange_profile';
	}

	function doPassChange() {

		$item = $this->app->user;


		$old = $_POST['login_old'];

		$user = $this->app->user;

		if (!$user->checkPass($old)) {
			$this->setError('/m/BAD_OLD_PASS');
			$this->app->jump($this->app->page->path);
		}


		$user->set('pass_new', $_POST['login_id'][0]);
		$user->set('pass_new_repeat', $_POST['login_id'][1]);
		$user->setValidators('change_pass_repeat');


		if (!$user->validate()) {
			$this->setItemErrors($item);
		} else {
			$user->site_passchange = '';
			$user->update();

			$this->setPlainMessage('/m/PROFILE_PASS_CHANGED');
			$this->app->jump($this->app->page->path);
		}
	}
	
	function doStoreSubscription()
	{
		$subscription = GW_Android_Push_Notif::getRegistrationId($_GET["subscription"]);
		$new = $this->app->user->getExt()->insertIfNotExists('android_subscription', $subscription);

		echo "New: $new";
		echo $subscription;
		echo "\nOK";
		exit;
	}
	//admin
	///admin/en/users/profile&act=doStoreSubscription?subscription=d6gjNFDxOT4%3AAPA91bGrGWNz74w5LxMw_JQOEVOBaP8mt0xnZdBhtjcLSOVWwLv4JqAqm1tpz8_k0dVnrjgez1Q2IS3BuBGDzc-2f5ag4FWfz8U2MWU27jeXjxQ_mNC6znSkvYXLjv6oDjuHL1ipQFWp
	//site
	//admin/en/direct/users/profile&act=doStoreSubscription?subscription=d6gjNFDxOT4%3AAPA91bGrGWNz74w5LxMw_JQOEVOBaP8mt0xnZdBhtjcLSOVWwLv4JqAqm1tpz8_k0dVnrjgez1Q2IS3BuBGDzc-2f5ag4FWfz8U2MWU27jeXjxQ_mNC6znSkvYXLjv6oDjuHL1ipQFWp

	
	function doTestSubscription()
	{
		$data = GW_Android_Push_Notif::push($this->app->user);
		
		echo json_encode($data, JSON_PRETTY_PRINT);
		
		exit;
			
	}	
	
	//ar reikia sito metodo klausimas
	
	function buildUri($path=false,$getparams=[], $params=[])
	{
		$pagepath=$this->app->page->path;	
		
		if(!isset($params['level']))
			$params['level']=2;
		
		
		
		$path=$pagepath.($path?'/'.$path:'');
		$params['carry_params'] = 1;

		return $this->app->buildURI($path, $getparams, $params);
	}
	
	
}
