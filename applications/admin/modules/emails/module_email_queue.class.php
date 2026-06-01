<?php


class Module_Email_Queue extends GW_Common_Module
{
	function init()
	{
		parent::init();

		$this->list_params['paging_enabled']=1;
		$this->config = new GW_Config($this->module_path[0].'/');



		if(isset($_GET['to']))
			$this->filters['to']=$_GET['to'];


		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['to'] = 1;

		$this->initLogger();

	}

	function __eventAfterListParams(&$params)
	{
		if(isset($_GET['searchbycontent'])){
			$search=$_GET['searchbycontent'];
			$params['conditions'] = GW_DB::mergeConditions ($params['conditions'], "body LIKE '%$search%'");
		}
	}

	function getListConfig()
	{
		$cfg = parent::getListConfig();
		$cfg['fields']['attachments'] = 'L';

		return $cfg;
	}

	function __eventAfterList(&$list)
	{
		foreach($list as $item)
			break;

		if(isset($item) && ($item->extensions['attachments'] ?? false))
			$item->extensions['attachments']->prepareList($list);
	}

	function doSend($item=false, $functiononly=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();

		$stored_attachments = $this->getStoredAttachments($item);
		$generated_attachments = $this->hasStoredGeneratedAttachment($item, $stored_attachments) ? [] : $this->getGeneratedAttachments($item);
		$attachments = array_merge($stored_attachments ?: [], $generated_attachments ?: []);

		if($attachments){
			$itemcopy = $item->toArray();
			$itemcopy['attachments'] = $attachments;
			$itemcopy['noStoreDB'] = 1;
			unset($itemcopy['scheduled']);
		}else{
			$itemcopy = $item;
		}

		$status = GW_Mail_Helper::sendMail($itemcopy);

		if($status){
			if(!$functiononly)
				$this->setMessage("Mail id:{$item->id} SENT");

			$this->setMessage("Mail id:{$item->id} SENT");
			$item->status = "SENT";

			if($generated_attachments && $this->shouldSaveGeneratedAttachments($item))
				$this->storeGeneratedAttachments($item, $generated_attachments);
		}else{
			if(!$functiononly)
				$this->setError("Mail id:{$item->id} FAILED ({$item->error})");

			$item->status = "ERR";
		}

		$item->updateChanged();

		if($functiononly)
			return true;

		if($this->sys_call && !$this->isPacketRequest())
			$this->jump();


		$this->notifyRowUpdated($item->id, false);
	}

	function getGeneratedAttachments($item)
	{
		$args = json_decode((string)$item->args, true);

		if(!is_array($args))
			return false;

		if(!($order_id = (int)($args['invoice_attachment_order_id'] ?? 0)))
			return false;

		$order = GW_Order_Group::singleton()->find(['id=?', $order_id]);

		if(!$order)
			return false;

		if(!class_exists('Module_OrderGroups'))
			require_once GW::s('DIR/APPLICATIONS').'admin/modules/payments/module_ordergroups.class.php';

		$invoice_module = new Module_OrderGroups();
		$invoice_module->app = $this->app;
		$invoice_module->module_path = ['payments', 'ordergroups'];
		$invoice_module->module_name = 'ordergroups';
		$invoice_module->tpl_vars = [];
		$invoice_module->model = GW_Order_Group::singleton();
		$invoice_module->modconfig = $invoice_module->initModCfgEx($invoice_module->module_path);
		$invoice_module->config = new GW_Config('payments/');

		list($tpl_code, $vars) = $invoice_module->initInvoiceVars($order, ['ln' => $this->app->ln]);

		if(!empty($args['invoice_attachment_preinvoice']))
			$vars['preinvoice'] = 1;

		$html = GW_Mail_Helper::prepareSmartyCode($tpl_code, $vars);
		$pdf = GW_html2pdf_Helper::convert($html, false);
		$filename = (!empty($args['invoice_attachment_preinvoice']) ? 'isankstine-saskaita-' : 'saskaita-').$order_id.'.pdf';

		return [$filename => $pdf];
	}

	function getGeneratedAttachmentFilename($item)
	{
		$args = json_decode((string)$item->args, true);

		if(!is_array($args))
			return false;

		if(!($order_id = (int)($args['invoice_attachment_order_id'] ?? 0)))
			return false;

		return (!empty($args['invoice_attachment_preinvoice']) ? 'isankstine-saskaita-' : 'saskaita-').$order_id.'.pdf';
	}

	function hasStoredGeneratedAttachment($item, $stored_attachments=false)
	{
		if(!($filename = $this->getGeneratedAttachmentFilename($item)))
			return false;

		if($stored_attachments !== false)
			return isset($stored_attachments[$filename]);

		return (bool)GW_Attachment::singleton()->count([
			'owner_type=? AND owner_id=? AND field=? AND title_lt=?',
			$item->ownerkey,
			$item->id,
			'attachments',
			$filename,
		]);
	}

	function getStoredAttachments($item)
	{
		if(!$item->id || !($item->extensions['attachments'] ?? false))
			return [];

		$list = $item->extensions['attachments']->findAll(["field=?", "attachments"]);
		$attachments = [];

		foreach($list as $attachment){
			if(($file = $attachment->attachment) && is_file($file->full_filename))
				$attachments[$file->original_filename] = file_get_contents($file->full_filename);
		}

		return $attachments;
	}

	function shouldSaveGeneratedAttachments($item)
	{
		$args = json_decode((string)$item->args, true);

		return is_array($args) && !empty($args['save_attachments']);
	}

	function storeGeneratedAttachments($item, $attachments)
	{
		if(!$item->id || !($item->extensions['attachments'] ?? false))
			return;

		foreach($attachments as $filename => $contents){
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$tempfn = GW::s('DIR/TEMP').'attachment'.time().rand(0, 100000).($extension ? '.'.$extension : '');
			file_put_contents($tempfn, $contents);

			$title_exists = GW_Attachment::singleton()->count([
				'owner_type=? AND owner_id=? AND field=? AND title_lt=?',
				$item->ownerkey,
				$item->id,
				'attachments',
				$filename,
			]);

			$values = [
				'owner_type' => $item->ownerkey,
				'owner_id' => $item->id,
				'field' => 'attachments',
				'checksum' => md5_file($tempfn),
			];

			if(!$title_exists && !GW_Attachment::singleton()->count(GW_DB::buidConditions($values))){
				$item->extensions['attachments']->storeAttachment('attachments', $tempfn, [
					'title_lt' => $filename,
					'title_en' => $filename,
					'title_ru' => $filename,
				], $filename);
			}

			unlink($tempfn);
		}
	}


	function getReady()
	{
		$limit = $this->config->mail_queue_portion_size ?: 5;

		return $this->model->findAll('status="ready"', ['limit'=>$limit]);
	}

	function doSendQueue()
	{
		$limit = $this->config->mail_queue_portion_size ?: 5;
		$affected = GW_Mail_Queue::singleton()->updateMultiple(['scheduled < ? AND `status`="scheduled" ', date('Y-m-d H:i')], ['status'=>'ready'], $limit);

		if($affected)
			$this->setMessage("Scheduled switched to ready: $affected");

		$list = $this->getReady();

		foreach($list as $item){
			$ids[] = $item->id;
			$this->doSend($item, true);
			sleep(1);
		}

		$next = count($this->getReady());
		//ids processed: ".implode(',',$ids).".
		$this->setMessage("Next portion size: $next");

		/*
		if($this->getReady()){
			sleep(1);
			Navigator::backgroundRequest('admin/lt/emails/email_queue?act=doSendQueue');
		}
		 *
		 */
	}


	function doViewBody($item=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();

		if($item->plain && !$this->getAttachmentList($item))
			header('Content-type: text/plain');

		echo $item->body;

		if($attachments_html = $this->getAttachmentsHtml($item))
			echo $attachments_html;

		exit;
	}

	function getAttachmentList($item)
	{
		if(!$item->id || !($item->extensions['attachments'] ?? false))
			return [];

		return $item->extensions['attachments']->findAll(["field=?", "attachments"]);
	}

	function getAttachmentsHtml($item)
	{
		$list = $this->getAttachmentList($item);

		if(!$list)
			return '';

		$html = '<hr><div style="font-family:Arial,sans-serif;font-size:13px;margin:14px 0;">';
		$html .= '<b>Attachments</b><ul>';

		foreach($list as $attachment){
			if(!($file = $attachment->attachment))
				continue;

			$filename = htmlspecialchars($file->original_filename, ENT_QUOTES, 'UTF-8');
			$size = htmlspecialchars($file->size_human, ENT_QUOTES, 'UTF-8');
			$url = htmlspecialchars($this->app->sys_base.'tools/download/'.$file->key, ENT_QUOTES, 'UTF-8');

			$html .= '<li>'.$filename.' '.$size.' <a href="'.$url.'">download</a></li>';
		}

		$html .= '</ul></div>';

		return $html;
	}

	function dummy()
	{
		GW_Mail_Queue::singleton();
	}

	function __eventAfterForm()
	{
		$this->tpl_vars['form_width']="1000px";
		$this->tpl_vars['width_title']="120px";

	}

	function __eventAfterSave($item)
	{
		if($_POST['submit_type']==7)
		{
			$this->doSend($item);
		}
	}


}
