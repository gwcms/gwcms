<?php



##$this->msg('Test proxies');	
##$url=Navigator::backgroundRequest('admin/lt/r1/proxies?act=do:updatepresence');



$url=Navigator::backgroundRequest('admin/lt/datasources/sms?act=doRetrySend&cron=1');