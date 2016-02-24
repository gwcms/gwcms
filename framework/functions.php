<?php

function dump() {
		$args = func_get_args();
		call_user_func_array(Array('d', 'dump'), $args);
}
