<?php



##$this->msg('Test proxies');	
##$url=Navigator::backgroundRequest('admin/lt/r1/proxies?act=do:updatepresence');


$this->msg('Send mails');
$url=Navigator::backgroundRequest('admin/lt/emails/messages?act=doSendBackground&cron=1');