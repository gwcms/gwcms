<?php


$this->msg('doRemoveUnconfirmedUsers');	
$url=Navigator::backgroundRequest('admin/lt/customers/users?act=doRemoveUnconfirmedUsers');
