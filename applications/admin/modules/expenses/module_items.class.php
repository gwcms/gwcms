<?php

ini_set('memory_limit', '300M');
ini_set('upload_max_filesize', '300M');
ini_set('post_max_size', '300M');
set_time_limit(500);

class Module_Items extends GW_Common_Module
{
	public $list_params = ['page_by' => 100];

	function init()
	{
		parent::init();

		$this->options['type'] = GW_Expense::typeOptions();
		$this->options['status'] = GW_Expense::statusOptions();
	}

	function doUpload()
	{
		$files = GW_File_Helper::reorderFilesArray('files');
		$ids = [];

		foreach ($files as $file) {
			if (!$this->isAllowedFile($file))
				continue;

			$item = $this->createExpenseFromUpload($file);
			$ids[] = $item->id;
		}

		if ($ids) {
			Navigator::backgroundRequest(
				'admin/' . $this->app->ln . '/expenses/items',
				['act' => 'doProcess', 'ids' => implode(',', $ids), 'cron' => 1],
				$this->app->user->id,
				['background' => 1]
			);
		}

		die('OK');
	}

	function createExpenseFromUpload($file)
	{
		$item = $this->model->createNewObject([
			'title' => pathinfo($file['name'], PATHINFO_FILENAME),
			'type' => 'other',
			'status' => GW_Expense::STATUS_PROCESSING,
			'coefficient' => 1,
			'extra' => ['original_filename' => $file['name']],
		]);

		$item->insert();
		$item->set('source_id', $item->id);

		$fileData = [
			'new_file' => $file['tmp_name'],
			'size' => filesize($file['tmp_name']),
			'original_filename' => $file['name'],
		];

		$item->set('file', $fileData);
		if ($this->isImageFile($file['name']))
			$item->set('image', $fileData);

		$item->updateChanged();

		return $item;
	}

	function doProcess()
	{
		$ids = array_filter(array_map('intval', explode(',', $_GET['ids'] ?? $_GET['id'] ?? '')));
		if (!$ids)
			die('NO IDS');

		$list = $this->model->findAll(GW_DB::inCondition('id', $ids));
		foreach ($list as $item)
			$this->processItem($item);

		die('OK');
	}

	function doReprocess()
	{
		$item = $this->getDataObjectById();
		$item->set('status', GW_Expense::STATUS_PROCESSING);
		$item->updateChanged();

		Navigator::backgroundRequest(
			'admin/' . $this->app->ln . '/expenses/items',
			['act' => 'doProcess', 'ids' => $item->id, 'cron' => 1],
			$this->app->user->id,
			['background' => 1]
		);

		$this->setMessage('Apdorojimas paleistas fone');
		$this->jump();
	}

	function processItem($item)
	{
		try {
			$data = $this->analyzeExpenseImage($item);
			$this->applyAnalysisResult($item, $data);
		} catch (Exception $e) {
			$item->setValues([
				'status' => GW_Expense::STATUS_FAILED,
				'note' => $e->getMessage(),
				'processed_time' => date('Y-m-d H:i:s'),
			]);
			$item->updateChanged();
		}
	}

	function applyAnalysisResult($item, array $data)
	{
		$entries = $data['entries'] ?? null;

		if (!$entries) {
			$item->applyAnalysis($data);
			return;
		}

		$item->setValues([
			'status' => GW_Expense::STATUS_PROCESSED,
			'title' => $data['title'] ?: $item->title,
			'amount' => null,
			'child_amount' => null,
			'note' => 'Failas išskaidytas į ' . count($entries) . ' įrašus',
			'api_response' => $data,
			'processed_time' => date('Y-m-d H:i:s'),
		]);
		$item->updateChanged();

		foreach ($entries as $entry) {
			$child = $this->model->createNewObject([
				'parent_id' => $item->id,
				'source_id' => $item->id,
				'status' => GW_Expense::STATUS_PROCESSING,
				'type' => 'other',
				'coefficient' => 1,
				'extra' => ['source_filename' => $item->file ? $item->file->original_filename : null],
			]);
			$child->insert();
			$child->applyAnalysis($entry);
		}
	}

	function analyzeExpenseImage($item)
	{
		$cfg = new GW_Config('expenses/');
		$apiKey = trim($cfg->apikey ?: getenv('OPENAI_API_KEY'));

		if (!$apiKey)
			throw new Exception('OpenAI API raktas neįvestas expenses confige');

		$source = $item->file ?: $item->image;
		if (!$source || !file_exists($source->full_filename))
			throw new Exception('Nerastas įkeltas išlaidų failas');

		$payload = $this->buildOpenAiPayload($item, $source, $cfg);
		$resp = $this->openAiRequest($apiKey, $payload);
		$data = $this->extractJsonFromResponse($resp);

		if (!$data)
			throw new Exception('API negrąžino suprantamo JSON');

		return $data;
	}

	function buildOpenAiPayload($item, $file, $cfg)
	{
		$foodCoeff = (float)($cfg->food_coefficient ?: 0.333333);
		$housingCoeff = (float)($cfg->housing_coefficient ?: 1);
		$otherCoeff = (float)($cfg->other_coefficient ?: 1);
		$model = $cfg->model ?: 'gpt-5.4-mini';

		$prompt = trim($cfg->prompt ?: $this->defaultPrompt());
		$prompt .= "\n\nKoeficientai: maistas={$foodCoeff}, bustas={$housingCoeff}, kita={$otherCoeff}, kuras={$otherCoeff}.";

		$content = [
			['type' => 'input_text', 'text' => $prompt],
		];

		$filename = $file->original_filename ?: basename($file->full_filename);
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$bytes = file_get_contents($file->full_filename);

		if ($this->isImageFile($filename)) {
			$mime = $this->mimeType($file->full_filename);
			$content[] = ['type' => 'input_image', 'image_url' => 'data:' . $mime . ';base64,' . base64_encode($bytes)];
		} elseif (in_array($ext, ['csv', 'txt'])) {
			$content[] = ['type' => 'input_text', 'text' => "Failas {$filename}:\n\n" . mb_substr($bytes, 0, 50000)];
		} else {
			$content[] = [
				'type' => 'input_file',
				'filename' => $filename,
				'file_data' => base64_encode($bytes),
			];
		}

		return [
			'model' => $model,
			'input' => [[
				'role' => 'user',
				'content' => $content,
			]],
			'text' => [
				'format' => [
					'type' => 'json_schema',
					'name' => 'expense_extract',
					'strict' => true,
					'schema' => [
						'type' => 'object',
						'additionalProperties' => false,
						'required' => ['expense_date', 'expense_month', 'title', 'amount', 'type', 'coefficient', 'child_amount', 'note', 'entries'],
						'properties' => [
							'expense_date' => ['type' => ['string', 'null'], 'description' => 'YYYY-MM-DD arba null jei datos nėra'],
							'expense_month' => ['type' => ['string', 'null'], 'description' => 'YYYY-MM arba null'],
							'title' => ['type' => 'string'],
							'amount' => ['type' => ['number', 'null']],
							'type' => ['type' => 'string', 'enum' => ['food', 'other', 'housing', 'fuel']],
							'coefficient' => ['type' => 'number'],
							'child_amount' => ['type' => ['number', 'null']],
							'note' => ['type' => ['string', 'null']],
							'entries' => [
								'type' => ['array', 'null'],
								'description' => 'Jei faile yra keli išlaidų įrašai, grąžink juos čia. Vienam čekiui grąžink null.',
								'items' => [
									'type' => 'object',
									'additionalProperties' => false,
									'required' => ['expense_date', 'expense_month', 'title', 'amount', 'type', 'coefficient', 'child_amount', 'note'],
									'properties' => [
										'expense_date' => ['type' => ['string', 'null']],
										'expense_month' => ['type' => ['string', 'null']],
										'title' => ['type' => 'string'],
										'amount' => ['type' => ['number', 'null']],
										'type' => ['type' => 'string', 'enum' => ['food', 'other', 'housing', 'fuel']],
										'coefficient' => ['type' => 'number'],
										'child_amount' => ['type' => ['number', 'null']],
										'note' => ['type' => ['string', 'null']],
									],
								],
							],
						],
					],
				],
			],
		];
	}

	function defaultPrompt()
	{
		return 'Įvertink įkeltą čekį, PDF, CSV ar banko išrašą vaiko išlaidų skaičiavimui. Vienam čekiui užpildyk pagrindinius laukus ir entries grąžink null. Jei faile yra keli atskiri įrašai, ypač banko išraše ar CSV, pagrindinius laukus užpildyk pirmu įrašu ir visus įrašus grąžink entries masyve, kad sistema galėtų sukurti atskiras išlaidas kiekvienai datai/mėnesiui. Maxima, Lidl ir Iki priskirk tipui food ir pavadinime naudok pvz. "Maxima maistas". Būsto išlaidos yra housing, kuras yra fuel, visa kita other. Jei datos patikimai nerandi, expense_date ir expense_month grąžink null ir note paaiškink problemą. child_amount = amount * coefficient.';
	}

	function openAiRequest($apiKey, array $payload)
	{
		$ch = curl_init('https://api.openai.com/v1/responses');
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $apiKey,
			],
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_TIMEOUT => 180,
		]);

		$raw = curl_exec($ch);
		$err = curl_error($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($raw === false || $err)
			throw new Exception('OpenAI API klaida: ' . $err);

		$resp = json_decode($raw, true);
		if ($code >= 400)
			throw new Exception('OpenAI API HTTP ' . $code . ': ' . ($resp['error']['message'] ?? $raw));

		return $resp;
	}

	function extractJsonFromResponse(array $resp)
	{
		if (isset($resp['output_text']))
			return json_decode($resp['output_text'], true);

		foreach (($resp['output'] ?? []) as $out)
			foreach (($out['content'] ?? []) as $content)
				if (isset($content['text']) && ($tmp = json_decode($content['text'], true)))
					return $tmp;

		return null;
	}

	function isAllowedFile($file)
	{
		if (!is_uploaded_file($file['tmp_name']) && !file_exists($file['tmp_name']))
			return false;

		return in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'csv', 'txt']);
	}

	function isImageFile($filename)
	{
		return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
	}

	function mimeType($file)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		return [
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'webp' => 'image/webp',
		][$ext] ?? 'image/jpeg';
	}

	function getListConfig()
	{
		$cfg = parent::getListConfig();

		$cfg['fields']['image'] = 'L';
		$cfg['fields']['file'] = 'L';
		$cfg['fields']['parent_id'] = 'lof';
		$cfg['fields']['source_id'] = 'lof';
		$cfg['fields']['expense_date'] = 'lof';
		$cfg['fields']['expense_month'] = 'lof';
		$cfg['fields']['title'] = 'lof';
		$cfg['fields']['amount'] = 'lof';
		$cfg['fields']['type'] = 'lof';
		$cfg['fields']['coefficient'] = 'lof';
		$cfg['fields']['child_amount'] = 'lof';
		$cfg['fields']['status'] = 'lof';
		$cfg['fields']['note'] = 'lof';
		$cfg['fields']['extra'] = 'F';

		$cfg['inputs']['expense_date'] = ['type' => 'date'];
		$cfg['inputs']['parent_id'] = ['type' => 'number'];
		$cfg['inputs']['source_id'] = ['type' => 'number'];
		$cfg['inputs']['expense_month'] = ['type' => 'text'];
		$cfg['inputs']['title'] = ['type' => 'text'];
		$cfg['inputs']['amount'] = ['type' => 'number', 'step' => '0.01'];
		$cfg['inputs']['type'] = ['type' => 'select', 'options' => $this->options['type']];
		$cfg['inputs']['coefficient'] = ['type' => 'number', 'step' => '0.000001'];
		$cfg['inputs']['child_amount'] = ['type' => 'number', 'step' => '0.01'];
		$cfg['inputs']['status'] = ['type' => 'select', 'options' => $this->options['status']];
		$cfg['inputs']['note'] = ['type' => 'textarea'];
		$cfg['inputs']['extra'] = ['type' => 'code_json', 'value_format' => 'json1', 'height' => '200px'];
		$cfg['inputs']['api_response'] = ['type' => 'code_json', 'value_format' => 'json1', 'height' => '260px'];
		$cfg['inputs']['image'] = ['type' => 'image'];
		$cfg['inputs']['file'] = ['type' => 'file'];

		$cfg['filters']['expense_date'] = ['type' => 'date'];
		$cfg['filters']['expense_month'] = ['type' => 'text'];
		$cfg['filters']['type'] = ['type' => 'multiselect', 'options' => $this->options['type']];
		$cfg['filters']['status'] = ['type' => 'multiselect', 'options' => $this->options['status']];

		return $cfg;
	}
}
