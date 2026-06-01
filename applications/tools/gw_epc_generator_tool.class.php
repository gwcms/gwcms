<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;

class GW_Epc_Generator_Tool
{
	public $path_arr;
	public $admin = false;
	public $app;

	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}

	function init()
	{
	}

	protected function fail($message, $code = 400)
	{
		http_response_code($code);
		header('Content-Type: text/plain; charset=utf-8');
		echo $message;
		exit;
	}

	protected function param($name, $fallback = '')
	{
		return trim((string)($_GET[$name] ?? $_POST[$name] ?? $fallback));
	}

	protected function cleanText($value, $maxLen)
	{
		$value = preg_replace('/[\r\n]+/', ' ', trim((string)$value));
		$value = preg_replace('/\s{2,}/', ' ', $value);

		if(function_exists('mb_substr'))
			return mb_substr($value, 0, $maxLen, 'UTF-8');

		return substr($value, 0, $maxLen);
	}

	protected function cleanIban($iban)
	{
		return strtoupper(preg_replace('/\s+/', '', (string)$iban));
	}

	protected function formatAmount($amount)
	{
		$amount = str_replace(',', '.', trim((string)$amount));

		if($amount === '' || !is_numeric($amount))
			return '';

		$amount = round((float)$amount, 2);

		if($amount <= 0)
			return '';

		return number_format($amount, 2, '.', '');
	}

	protected function buildEpcPayload()
	{
		$iban = $this->cleanIban($this->param('recipient_iban', $this->param('iban')));
		$name = $this->cleanText($this->param('recipient_name', $this->param('name')), 70);
		$amount = $this->formatAmount($this->param('amount'));
		$currency = strtoupper($this->param('currency', 'EUR'));
		$bic = strtoupper($this->cleanText($this->param('bic'), 11));
		$purpose = strtoupper($this->cleanText($this->param('purpose'), 4));
		$reference = $this->cleanText($this->param('reference'), 35);
		$remittance = $this->cleanText($this->param('remittance', $this->param('details', $this->param('message'))), 140);

		if(!$iban)
			$this->fail('Missing recipient_iban');

		if(!preg_match('/^[A-Z]{2}[0-9A-Z]{13,32}$/', $iban))
			$this->fail('Invalid recipient_iban');

		if(!$name)
			$this->fail('Missing recipient_name');

		if(!$amount)
			$this->fail('Missing or invalid amount');

		if($currency !== 'EUR')
			$this->fail('EPC QR supports EUR payments only');

		return implode("\n", [
			'BCD',
			'002',
			'1',
			'SCT',
			$bic,
			$name,
			$iban,
			'EUR'.$amount,
			$purpose,
			$reference,
			$remittance,
			'',
		]);
	}

	function process()
	{
		require_once GW::s('DIR/ROOT').'vendor/autoload.php';

		$payload = $this->buildEpcPayload();
		$scale = max(3, min(20, (int)$this->param('scale', 6)));

		$options = new QROptions([
			'outputType' => QROutputInterface::GDIMAGE_PNG,
			'outputBase64' => false,
			'scale' => $scale,
			'quietzoneSize' => 2,
			'quality' => 9,
		]);

		$png = (new QRCode($options))->render($payload);

		header('Content-Type: image/png');
		header('Content-Length: '.strlen($png));
		header('Cache-Control: no-store, max-age=0');
		echo $png;
		exit;
	}
}
