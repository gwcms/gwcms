<?php

class GW_Common_Service
{

	public $path_arr;
	public $admin = false;

	/**
	 *
	 * @var GW_Service_Application
	 */
	public $app;
	public $debug = true;
	public $user;

	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}

	function init()
	{
		$this->initLang();
		$this->app->initDB();
	}

	function checkAuth()
	{
		if ($this->user)
			return true;
	}

	/**
	 * each call you should add user pass
	 * good for testing & developing & debuging
	 * bad for security
	 * simple http(s)://host/path?args..&usr=...&pass=...
	 */
	function checkBasicGetAuth()
	{
		if (isset($_GET['usr']) && $_GET['pwd'] && $this->checkBasicUser($_GET['usr'], $_GET['pwd']))
			return true;
	}

	/**
	 * each call you should add user pass
	 * easy testing
	 * bad for security if using not https
	 * simple inurl user pass - http(s)://user:pass@host/path
	 */
	function checkBasicHTTPAuth()
	{
		if (!isset($_SESSION))
			session_start();

		if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_PW'] && $this->checkBasicUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
			$_SESSION['service_user'] = $_SERVER['PHP_AUTH_USER'];
			return true;
		}

		if (isset($_GET['debug'])) {
			header('WWW-Authenticate: Basic realm="Basic GW service auth"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Authentification failed, you canceled';
			exit;
		}
	}

	function checkBasicUser($user, $pass)
	{
		return $user === $this->username && $pass === $this->pass;
	}

	function checkBasicSystemUser($user, $pass)
	{
		$usr = GW_user::singleton()->find(['username=? AND active=1', $user]);


		if (!$usr->checkAllowedIp($_SERVER['REMOTE_ADDR'])) {
			$this->output(['error_code' => '/G/USER/IP_ADDRESS_NOT_ALLOWED_OR_UNCONFIGURED']);
		}

		return $user === $usr->username && $pass === $usr->api_key;
	}

	function actPublic($args)
	{
		$act = array_shift($args);

		if (is_callable([$this, 'pact' . $act])) {
			$response = $this->{'pact' . $act}($args);
		} else {
			$response['error'] = "Requested public action not found";
			$response['error_code'] = '405';
		}

		return $response;
	}

	function pactEcho($path)
	{
		return ['post' => $_POST, 'get' => $_GET, 'path' => $path];
	}

	function processAct(&$args, &$response)
	{
		$act = array_shift($args);

		if (is_callable([$this, 'act' . $act])) {
			$response = $this->{'act' . $act}($args);
		} else {
			$response['error'] = "Requested action not found";
			$response['error_code'] = '404';
		}
	}

	function process()
	{
		ob_start();

		$t = new GW_Timer;
		$response = [];

		$args = $this->path_arr;

		if (!count($args) || count($args) == 1 && !$args[0]) {
			$response['error'] = "Bad request";
			$response['error_code'] = '400';
		} else {

			//no authorization required for /public/*
			if ($args[0] == 'public') {
				$this->processAct($args, $response);
			} else {
				//authorized requests

				if (!$this->checkAuth($args[0] == 'getToken')) {
					$response['error'] = "Unauthorized";
					$response['error_code'] = '401';
				} else {
					$this->processAct($args, $response);
				}
			}
		}

		$response['process_time'] = $t->stop(5);

		$unexpected = ob_get_contents();
		ob_end_clean();

		if ($unexpected) {
			//if($this->debug)
			$response['unexpected_output'] = $unexpected;

			mail('errors@gw.lt', "Error under service " . $this->name, "Unexpected output: \r\n" . $unexpected);
		}


		$this->output($response);
	}

	function output($response)
	{
		header('Content-type: text/plain');

		if (isset($response['error_code']) && strpos($response['error_code'], '/') === 0)
			$response['error_human'] = GW::l($response['error_code']);

		echo json_encode($response, JSON_PRETTY_PRINT);
		exit;
	}

	function initLang($appname = "admin")
	{
		GW_Lang::$ln = 'en';
		GW_Lang::$langf_dir = GW::s("DIR/APPLICATIONS") . $appname . '/lang/';
	}
}
