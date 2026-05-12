<?php

class GW_Expense extends GW_Composite_Data_Object
{
	const STATUS_PROCESSING = 'processing';
	const STATUS_DATE_NOT_FOUND = 'date_not_found';
	const STATUS_PROCESSED = 'processed';
	const STATUS_FAILED = 'failed';

	public $table = 'gw_expenses';
	public $default_order = 'expense_date DESC, id DESC';
	public $encode_fields = [
		'extra' => 'JSON',
		'api_response' => 'JSON',
	];

	public $composite_map = [
		'image' => ['gw_image', ['dimensions_resize' => '2400x2400', 'dimensions_min' => '10x10', 'size_max' => 20971520]],
		'file' => ['gw_file', ['size_max' => 50971520, 'allowed_extensions' => 'jpg,jpeg,png,gif,webp,pdf,csv,txt']],
	];

	static function typeOptions()
	{
		return [
			'food' => 'Maistas',
			'other' => 'Kita',
			'housing' => 'Būstas',
			'fuel' => 'Kuras',
		];
	}

	static function statusOptions()
	{
		return [
			self::STATUS_PROCESSING => 'Vykdomas apdorojimas',
			self::STATUS_DATE_NOT_FOUND => 'Negaliu rasti datos',
			self::STATUS_PROCESSED => 'Apdorota',
			self::STATUS_FAILED => 'Klaida',
		];
	}

	function config()
	{
		static $cache;

		if (!$cache)
			$cache = new GW_Config('expenses/');

		return $cache;
	}

	function applyAnalysis(array $data)
	{
		$type = $data['type'] ?? 'other';
		if (!isset(self::typeOptions()[$type]))
			$type = 'other';

		$date = $data['expense_date'] ?? null;
		$status = $date ? self::STATUS_PROCESSED : self::STATUS_DATE_NOT_FOUND;

		$this->setValues([
			'expense_date' => $date,
			'expense_month' => $data['expense_month'] ?? ($date ? substr($date, 0, 7) : null),
			'title' => $data['title'] ?? $this->title,
			'amount' => $data['amount'] ?? $this->amount,
			'type' => $type,
			'coefficient' => $data['coefficient'] ?? $this->coefficient,
			'child_amount' => $data['child_amount'] ?? null,
			'note' => $data['note'] ?? null,
			'status' => $status,
			'api_response' => $data,
			'processed_time' => date('Y-m-d H:i:s'),
		]);

		$this->updateChanged();
	}
}
