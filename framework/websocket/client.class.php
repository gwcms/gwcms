<?php
/**
 * Copyright (C) 2014, 2015 Textalk
 * Copyright (C) 2015 Ignas Bernotas - added context options and handling
 * Copyright (C) 2015 Vidmantas Norkus - fixes for secure protocol
 *
 * 
 * events: connected,disconnected,onjoinchan,incoming,incoming_authoriseReply
 * 
 * This file is part of Websocket PHP and is free software under the ISC License.
 * License text: https://raw.githubusercontent.com/Textalk/websocket-php/master/COPYING
 */

namespace WebSocket;

class Client extends Base
{

	public $lastping = 0;
	protected $socket_uri;
	
	const CANT_CONNECT = 60;
	const NOT_AUTHORISED = 61;
	const CHAN_JOIN_FAIL = 62;
	const CHAN_MSG_FAIL = 63;
	const PRIV_MSG_FAIL = 70;

	
	//client errors array of errors, single error example [12345/*error code*/, 'error message']
	public $errors=[];
	public $messages_enabled = true;

	/**
	 * @param string  $uri      A ws/wss-URI
	 * @param array   $options
	 *   Associative array containing:
	 *   - context:      Set the stream context. Default: empty context
	 *   - timeout:      Set the socket timeout in seconds.  Default: 5
	 *   - headers:      Associative array of headers to set/override.
	 */
	public function __construct($uri, $options = array())
	{
		$this->options = $options;

		if (!array_key_exists('timeout', $this->options))
			$this->options['timeout'] = 10;

		// the fragment size
		if (!array_key_exists('fragment_size', $this->options))
			$this->options['fragment_size'] = 4096;

		$this->socket_uri = $uri;
	}

	public function __destruct()
	{
		if ($this->socket) {
			if (get_resource_type($this->socket) === 'stream')
				fclose($this->socket);
			$this->socket = null;
		}
	}

	/**
	 * Perform WebSocket handshake
	 */
	public function connect()
	{
		
		$url_parts = parse_url($this->socket_uri);
		$scheme = $url_parts['scheme'];
		$host = $url_parts['host'];
		$user = isset($url_parts['user']) ? $url_parts['user'] : '';
		$pass = isset($url_parts['pass']) ? $url_parts['pass'] : '';
		$port = isset($url_parts['port']) ? $url_parts['port'] : ($scheme === 'wss' ? 443 : 80);
		$path = isset($url_parts['path']) ? $url_parts['path'] : '/';
		$query = isset($url_parts['query']) ? $url_parts['query'] : '';
		$fragment = isset($url_parts['fragment']) ? $url_parts['fragment'] : '';

		$path_with_query = $path;
		if (!empty($query))
			$path_with_query .= '?' . $query;
		if (!empty($fragment))
			$path_with_query .= '#' . $fragment;

		if (!in_array($scheme, array('ws', 'wss'))) {
			throw new BadUriException(
			"Url should have scheme ws or wss, not '$scheme' from URI '$this->socket_uri' ."
			);
		}

		$host_uri = ($scheme === 'wss' ? 'ssl' : 'tcp') . '://' . $host;

		// Set the stream context options if they're already set in the config
		if (isset($this->options['context'])) {
			// Suppress the error since we'll catch it below
			if (@get_resource_type($this->options['context']) === 'stream-context') {
				$context = $this->options['context'];
			} else {
				throw new \InvalidArgumentException(
				"Stream context in \$options['context'] isn't a valid context"
				);
			}
		} else {
			$context = stream_context_create();
		}

		if ($scheme == 'wss') {
			//stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
			//stream_context_set_option($context, 'ssl', 'verify_peer', false);
			//stream_context_set_option($context, 'ssl', 'local_cert', __DIR__.'/server.pem');
			//stream_context_set_option($context, 'ssl', 'passphrase', "gw_wss");

			stream_context_set_option($context, 'ssl', 'verify_peer', false);
			stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
			//stream_context_set_option($context, 'ssl', 'peer_fingerprint', false);
			//stream_context_set_option($context, 'ssl', 'verify_host', true);
		}

		// Open the socket.  @ is there to supress warning that we will catch in check below instead.
		$this->socket = @stream_socket_client(
				$host_uri . ':' . $port, $errno, $errstr, $this->options['timeout'], STREAM_CLIENT_CONNECT, $context
		);



		if ($this->socket === false) {
			
			$this->msg("Cant connect to \"$host:$port\": $errstr ($errno)");

			$this->errors[] = [self::CANT_CONNECT, "Cant connect to \"$host:$port\": $errstr ($errno)"];

			return false;
		}
		
		

		// Set timeout on the stream as well.
		stream_set_timeout($this->socket, $this->options['timeout']);

		// Generate the WebSocket key.
		$key = self::generateKey();

		// Default headers (using lowercase for simpler array_merge below).
		$headers = array(
			'host' => $host . ":" . $port,
			'user-agent' => 'websocket-client-php',
			'connection' => 'Upgrade',
			'upgrade' => 'websocket',
			'Sec-WebSocket-Key' => $key,
			'Sec-WebSocket-Version' => '13',
		);

		// Handle basic authentication.
		if ($user || $pass) {
			$headers['authorization'] = 'Basic ' . base64_encode($user . ':' . $pass) . "\r\n";
		}

		// Deprecated way of adding origin (use headers instead).
		if (isset($this->options['origin']))
			$headers['origin'] = $this->options['origin'];

		// Add and override with headers from options.
		if (isset($this->options['headers'])) {
			$headers = array_merge($headers, array_change_key_case($this->options['headers']));
		}

		$header = "GET " . $path_with_query . " HTTP/1.1\r\n"
			. implode(
				"\r\n", array_map(
					function($key, $value) {
					return "$key: $value";
				}, array_keys($headers), $headers
				)
			)
			. "\r\n\r\n";

		// Send headers.
		$this->write($header);

		// Get server response header (terminated with double CR+LF).
		$response = stream_get_line($this->socket, 1024, "\r\n\r\n");
		
		/// @todo Handle version switching
		// Validate response.
		if (!preg_match('#Sec-WebSocket-Accept:\s(.*)$#mUi', $response, $matches)) {
			$address = $scheme . '://' . $host . $path_with_query;

			$this->errors[] = [self::CANT_CONNECT, "Connection to '{$address}' failed: Server sent invalid upgrade response: $response"];

			return false;
		}

		$keyAccept = trim($matches[1]);
		$expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

		if ($keyAccept !== $expectedResonse) {
			throw new ConnectionException('Server sent bad upgrade response.');
		}

		$this->is_connected = true;
		
		$this->fireEvent('connected', $this);

		return true;
	}

	
	public static function randStr($size)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';
		$key = '';
		$chars_length = strlen($chars);
		for ($i = 0; $i < $size; $i++)
			$key .= $chars[mt_rand(0, $chars_length - 1)];

		return $key;
	}

	/**
	 * Generate a random string for WebSocket key.
	 * @return string Random string
	 */
	protected static function generateKey()
	{
		return base64_encode(self::randStr(16));
	}
	
	function fireEvent($event, &$context=false)
	{
		if(!isset($this->callbacks[$event]))
			return false;
		
		foreach($this->callbacks[$event] as $callback)
			$callback($context);
	}
	
	private $callbacks = [];
	
	function registerEvent($event, $callback)
	{
		$this->callbacks[$event][] = $callback;
	}
	
	
	//wait time in miliseconds
	function waitForResponse($msgid, $waittime = 3000)
	{
		$start = microtime(true);
		$end = $start + $waittime / 1000;
			
		while($end > microtime(true)) {		
			$this->readToBuffer();
			
			if(isset($this->buffer[$msgid]))
			{
				$data = $this->buffer[$msgid];
				unset($this->buffer[$msgid]);
				
				return $data + ['waittime' => round(microtime(true) - $start, 5)];
			}
			
			usleep(1000);
		}
		
		return null;
	}
	
	
	function authorise($user, $pass, $wait = 3000)
	{
		$msgid = $this->writeData("authorise", ['username'=>$user, 'pass'=>$pass]);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);
	}
	
	
	function setTempPass($pass, $expire_time, $user=false, $wait=3000)
	{
		$msgid = $this->writeData("settemppass", ['username'=>$user, 'temp_pass'=>$pass, 'temp_pass_expires'=>$expire_time]);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	
	function createUser($user, $pass, $extra=[], $wait = 30000)
	{
		$extra['expires'] = !isset($extra['expires']) ? '1 year' : $extra['expires'];
		
		//{"action":"createuser","data":{"username":"test2","pass":"test123","expires":"1 year"},"msgid":696}		
		
		$msgid = $this->writeData("createuser", ['username'=>$user, 'pass'=>$pass]+$extra);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);
	}
	
	
	function authoriseOrRegister($user, $pass)
	{
		$response = $this->authorise($user, $pass);
		
		if(isset($response['data']) && $response['data']=='FAIL') {
			
			$response = $this->createUser($user, $pass);
			
			if(isset($response['data']) && $response['data']=='FAIL') {
				throw new Exception("Authentification Or Register failed ($user,$pass) ".$this->getErrorsText()."\n");
			}else{
				$response = $this->authorise($user, $pass);
				
				if(isset($response['data']) && $response['data']=='FAIL') {
					throw new Exception("Authentification failed: ".$this->getErrorsText()."\n");
				}
			}
		}
	}
	
	
	function joinChannel($channel, $password = null, $wait = 3000)
	{
		$data = ['channel' => $channel];
		
		if($password !== null){
		    $data['pass'] = $password;
		}

		
		$msgid = $this->writeData("joinchan", $data);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	function infoChannel($channel, $wait = 3000)
	{
		$data = ['channel' => $channel];
		
		
		$msgid = $this->writeData("infochan", $data);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	
	public $buffer = [];
	

	function readToBuffer()
	{
		$rawdata = $this->checkIncomming();
		
		if ($data = $this->_decodeData($rawdata)) {
			
			if(isset($data['msgid']))
			{
				$this->buffer[$data['msgid']] =& $data;
			}
			
			if(isset($data['errors']))
			{
				$this->setErrors($data['errors'], isset($data['msgid']) ? $data['msgid'] : null);
			}
			
			$this->fireEvent("incoming", $data);
			$this->fireEvent("incoming_".$data['action'], $data);
		}
	}
	
	//errors received from server
	public $merrors;
	public $err_id=-1;
	

	function setErrors($errors, $msgid = null)
	{
		if($msgid == null)
			$msgid = $this->err_id--;
			
		$this->errors[$msgid] = $errors;
	}	
	
	function getErrorsText()
	{
		$errors = "";
			
		foreach($this->errors as $errarr)
			$errors .= implode("\n", $errarr)."\n";
		
		return trim($errors);
	}
	
	function heartBeat()
	{
		if (!$this->is_connected) {
			$this->msg("INT connecting...");

			if ($this->connect()) {
				$this->msg("INT connected");
				
				$this->lastping = time();
			}else{
				$this->msg("INT conn failed");
			}
			
		} else {

			$this->readToBuffer();

			//every 5 minutes
			if (time() - $this->lastping > 300)
				$this->ping();
			
		}
	}
	
	function ping()
	{
		$this->msg('INT ping');

		//$this->_interface->send('','ping');
		$this->writeData("ping");

		$this->lastping = time();		
	}
	
	
	function msg($data)
	{
		if(!$this->messages_enabled)
			return false;
		
		if(!is_int($data) || !is_string($data))
			$data = print_r($data, true);
		
		
		echo date('H:i:s')." $data\n";
	}
	
	private $initDone=false;
	
	function init()
	{
		if($this->initDone)
			return false;
		
		$this->registerEvent('incoming_joinchanReply', [$this, 'onJoinChannel']);
		$this->registerEvent('incoming_authoriseReply', [$this, 'onAuthorise']);
		
		
		$this->initDone = true;
		
		return true;
	}
	
	public $channels;
	
	function onJoinChannel($data)
	{		
		if($data['data']=="SUCCESS")
		{
			$chanObj = new Channel($this, $data['channel']);
			$this->channels[$data['channel']] = $chanObj;
				
			$this->fireEvent('onjoinchan', $chanObj);
		}
	}
	
	public $is_authorised = false;
	
	function isAuthorised()
	{
		return (bool)$this->is_authorised;
	}
	
	function onAuthorise($data)
	{
		if($data['data']=="SUCCESS")
		{
			$this->is_authorised = $data['user'];
		}
	}	
	
	function messageChannel($name, $msg, $wait=false)
	{
		$data = ['channel' => $name, 'message'=>$msg];
		
		if($wait)
			$data['request_reply']=1;
		
		$msgid = $this->writeData('messagechan', $data);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	function messagePrivate($username, $msg, $wait=false, &$privmsgid=false)
	{
		//{"action":"messageprivate","":"wdm","data":"test"}
		$privmsgid =  $privmsgid ?  $privmsgid : microtime(true);
		
		$data = ['user' => $username, 'message'=>"$msg", 'privmsgid'=> $privmsgid];
		
		
		if($wait)
			$data['request_reply']=1;
		
		$msgid = $this->writeData('messageprivate', $data);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	function replyPrivate($inmsg_payload, $msg, $wait=false)
	{
		//inmsg payload
		//{"action":"messageprivate","user":"petras","data":"Hellou!","privmsgid":1492819707.7957}
		
		$data = ['user' => $inmsg_payload['user'], 'message'=>"$msg", 'replytoid'=> $inmsg_payload['privmsgid']];
		
		$msgid = $this->writeData('messageprivate', $data);
		
		if($wait)
			return $this->waitForResponse($msgid, $wait);		
	}
	
	
	
	function __fastConnect($args)
	{
		$this->init();
		
		if(!$this->is_connected)
			$this->connect();
		
		
		if(!$this->is_connected)
			return self::CANT_CONNECT;		
	}
	
	/***
	 * args
	[
		'channel' => 'yourchannel',
		'channel_pass' => 'if it is needed enter pass here, or leave blank'
	]
	 * 
	 */
	function fastChanMessage($args, $message)
	{

		$this->__fastConnect($args);
		//$this->authoriseOrRegister($args['user_name'], $args['user_pass'], 20000);
		
		//if(!$this->isAuthorised())
		//	return self::NOT_AUTHORISED;
		
		
		//$return = $this->joinChannel($args['channel'], $args['channel_pass'], 20000);
		
		
		//if(!isset($this->channels[$args['channel']]))
		//	return self::CHAN_JOIN_FAIL;
		
		
		$this->notJoinedMessageChan($args['channel'], $message);
	}
	
	function notJoinedMessageChan($chan, $message)
	{
		$return = $this->messageChannel($chan, $message, 20000);
		
		if($return && isset($return['data']) && $return['data'] == "FAIL")
			return self::CHAN_MSG_FAIL;
		
		
		return 0;		
	}
	

	
	/**
	 * 
	 * @param type $args ['username' => 'specify target user']
	 * @param type $message
	 * @param type $msgid - returns autogenerated private message id if it is not specified
	 * @param type $waitrespond  - put variable with seconds to wait, returns reply to this variable or null if timeout reached
	 * @return type
	 */
	function fastPrivateMessage($args, $message, &$msgid=false, &$waitrespond=false)
	{
		if($this->__fastConnect($args))
			return [self::CANT_CONNECT, "Can't connect"];
		
		$return = $this->messagePrivate($args['username'], $message, 20000, $msgid);
		
		if($return && isset($return['data']) && $return['data'] == "FAIL")
			return [self::PRIV_MSG_FAIL, json_encode($return)];		
		
		if($waitrespond){
			$seconds_to_wait = $waitrespond;
			$reply = null;
			
			$this->registerEvent('incoming', function($data) use ($msgid, &$reply) {
				
				//somehow type:doubles cant be compared, so i convert to string
				if(isset($data['replytoid']) && (string)$data['replytoid'] == (string)$msgid)
					$reply = $data['data'];
				
			});

			while(!$reply && $seconds_to_wait > 0){
				
				$this->heartBeat();
				
				usleep(50000);//
				$seconds_to_wait -= 0.05;
				
				//echo "secs to wait: $seconds_to_wait reply: '$reply'\n";
			}
			
			$waitrespond = $reply;
		}
	}
	
	
	function disconnected() 
	{
		$this->fireEvent('disconnected');
		
		parent::disconnected();
	}
}
