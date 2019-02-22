{include file="default_form_open.tpl" form_width=1000px}


{call e field=sum}
{call e field=object}

{call e field=platform}
{call e field=strategy type=textarea height="50px" autoresize=1}

{call e field=take_profit}
{call e field=stop_loss}

{call e field=profit}
{call e field=termination_date type=date}






{include file="default_form_close.tpl"}