<?php

class GW_Chat_Store_Tool
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
		$this->app->initDB();
	}

	function jsonResponse($data, $status = 200)
	{
		http_response_code($status);
		header('Content-type: application/json');
		echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		exit;
	}

	function process()
	{
		if (ob_get_level())
			ob_clean();

		try {
			$svc = GW_Chat_Service::singleton();
			$expectedToken = (string)$svc->getRemoteStoreConfig('remote_store_token', '');
			$token = (string)($_REQUEST['token'] ?? '');

			if (!$expectedToken || !hash_equals($expectedToken, $token))
				$this->jsonResponse(['ok' => 0, 'error' => 'Bad token'], 403);

			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$userId = (int)($_REQUEST['user_id'] ?? 0);
			$filename = (string)($_REQUEST['filename'] ?? 'file');
			$mime = (string)($_REQUEST['mime'] ?? '');
			$data = base64_decode((string)($_REQUEST['filedata'] ?? ''), true);

			if ($roomId <= 0 || $data === false || $data === '')
				$this->jsonResponse(['ok' => 0, 'error' => 'Bad request'], 400);

			$tmp = tempnam(GW::s('DIR/TEMP'), 'chat_remote_');
			file_put_contents($tmp, $data);

			try {
				$meta = $svc->storeChatAttachmentLocal($roomId, $userId, $tmp, $filename, $mime);
			} finally {
				@unlink($tmp);
			}

			$this->jsonResponse([
				'ok' => 1,
				'file' => $meta,
			]);
		} catch (Exception $e) {
			$this->jsonResponse(['ok' => 0, 'error' => $e->getMessage()], 400);
		}
	}
}
