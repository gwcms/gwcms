#!/usr/bin/php
<?php

include __DIR__.'/../init_basic.php';


//console application
if($argv[1]=='console')
	while($line = GW_App_Base::readStdIn())
		eval($line);
		
if($argv[1]=='daemon')
	die(GW_App_System::runSelf());
	
		
//system application

new GW_App_System;

