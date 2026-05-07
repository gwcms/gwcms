<?php

class Module_UsersWidgets extends GW_Module
{
	public $is_widget_module = true;

	function isOnlinechatEnabled()
	{
		if (GW::s('PROJECT_ENVIRONMENT') == GW_ENV_TEST)
			return false;

		if (!GW_WebSocket_Helper2::enabled())
			return false;

		$enabled = GW_WebSocket_Helper2::chatConfigValue('onlinechat_widget_enabled');
		if ($enabled !== null && $enabled !== '' && !(int)$enabled)
			return false;

		return true;
	}

	function viewOnlinechat()
	{
		if (!$this->isOnlinechatEnabled())
			return 'skiptemplate';

		$this->tpl_vars['chat_endpoint'] = $this->app->buildUri('users/chat');
		$this->tpl_vars['ws_path'] = GW_WebSocket_Helper2::enabled() ? GW_WebSocket_Helper2::buildWsPath() : '';
		$this->tpl_vars['wss_log_to_console'] = ((int)GW_WebSocket_Helper2::chatConfigValue('full_chat_debug') || (int)GW_WebSocket_Helper2::chatConfigValue('wss_log_to_console')) ? 1 : 0;
		$this->tpl_vars['chat_list_url'] = $this->app->buildUri('users/chat');
		$this->tpl_vars['new_private_url'] = $this->app->buildUri('users/chat', ['act' => 'doNewPrivate']);
		$this->tpl_vars['show_ws_protocol_link'] = $this->app->user && $this->app->user->isRoot() ? 1 : 0;
		$this->tpl_vars['ws_protocol_url'] = $this->app->buildUri('users/chat/testlivechatprotocol');
		$this->tpl_vars['is_root_user'] = $this->app->user && $this->app->user->isRoot() ? 1 : 0;
		$this->tpl_vars['show_last_request_uri_debug'] = $this->app->user && (int)$this->app->user->id === 9 ? 1 : 0;
		$this->tpl_vars['show_last_request_uri_debug_on'] = (int)GW_Config::singleton()->get('gw_users/onlinechat_show_last_request_uri') ? 1 : 0;
		$this->tpl_vars['toggle_last_request_uri_url'] = $this->app->buildUri('users/chat', [
			'act' => 'doToogleConfigShowRequestUri',
			'return_to' => $_SERVER['REQUEST_URI'] ?? '',
		]);
	}
}
