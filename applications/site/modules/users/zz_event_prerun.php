<?php

GW::s('version2022',1);


if($this->user && $this->user->isRoot()){
	GW::s('version2022',1);
}