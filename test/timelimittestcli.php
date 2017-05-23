<?php

if(isset($_SERVER['HTTP_HOST']))
{
	shell_exec("php '".__FILE__."'");
}

set_time_limit(101);


for($i=0;$i<100;$i++)
{
	echo "$i ";
	sleep(1);
}
