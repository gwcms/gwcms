
<div class="btn-group dropright gwcmsAction" style="display: inline">

	<i class="fa fa-bars   gwcms-ajax-dd" data-toggle="dropdown" data-url="{$app->buildUri("{$modpath}/itemactions",['id'=>$item->id,frontend=>1],[app=>admin])}"></i>	
	    <ul class="dropdown-menu dropdown-menu-right">
		<li><i class="fa fa-spinner fa-pulse"></i></li>
	    </ul>
</div>									    

{if !isset($GLOBALS.dropdown_init_done)}
	{$GLOBALS.dropdown_init_done=1}	    
<style>

	.gw_dl_actions {
    white-space-collapse: discard;
    font-size: 0;
    padding: 0px !important;
}

	.dropdown-menu {
    border: 0;
    border-radius: 0;
    box-shadow: 0 5px 12px 2px rgba(0, 0, 0, 0.25);
    font-size: 13px;
    margin: 0;
    padding: 0;
}
.gwcmsAction .dropdown-menu li a {
    color: #333;
}
.gwcmsAction .dropdown-menu:not(.head-list)>li>a {
    padding: 5px 10px;
}
.gwcmsAction .dropdown-menu>li>a {
    color: #758697;
}
.gwcmsAction .dropdown-menu>li>a {
    display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333;
    white-space: nowrap;
}

.gwcmsLinksInDD .fa {
    margin-right: .3em;
    width: 1.28571429em;
    text-align: center;
}
.gw_dl_actions i {
    font-size: 16px;
    padding: 2px 4px 2px 4px;
}


.gwcmsAction .text-light,a.text-light:hover,a.text-light:focus
{
	color: #fff;
}
.gwcmsAction .text-muted,a.text-muted:hover,a.text-muted:focus
{
	color: #afb9c3;
}
.gwcmsAction .text-primary,a.text-primary:hover,a.text-primary:focus
{
	color: #128ef2;
}
.gwcmsAction .text-info,a.text-info:hover,a.text-info:focus
{
	color: #008fa1;
}
.gwcmsAction .text-success,a.text-success:hover,a.text-success:focus
{
	color: #71a436;
}
.gwcmsAction .text-warning,a.text-warning:hover,a.text-warning:focus
{
	color: #f29000;
}
.gwcmsAction .text-danger,a.text-danger:hover,a.text-danger:focus
{
	color: #eb2521;
}
.gwcmsAction .text-main,a.text-main:hover,a.text-main:focus
{
	color: #2b425b;
}
.gwcmsAction .text-mint,a.text-mint:hover,a.text-mint:focus
{
	color: #1c7d74;
}
.gwcmsAction .text-purple,a.text-purple:hover,a.text-purple:focus
{
	color: #a844b9;
}
.gwcmsAction .text-pink,a.text-pink:hover,a.text-pink:focus
{
	color: #e2175b;
}
.gwcmsAction .text-dark,a.text-dark:hover,a.text-dark:focus
{
	color: #11171a;
}

.text-brown{ color:#795548}
</style>

{/if}