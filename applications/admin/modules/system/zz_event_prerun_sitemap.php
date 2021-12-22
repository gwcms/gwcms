<?php


if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV && $this->user->isRoot())
{
	$t = new GW_Timer;
	$msgs = GW_ADM_Sitemap_Helper::updateSitemap();

	if($msgs)
		foreach($msgs as $msg)
			$this->setMessage(['type' => GW_MSG_INFO, 'text'=>$msg, 'float'=>1, 'footer'=>$t->stop().'s']);	
}
