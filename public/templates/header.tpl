
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="{Navigator::getBase(1)}" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$page->title}</title>
<link href="no/csskode" rel="stylesheet" type="text/css" media="screen"/>
<script src="scripts/jquery.js" type="text/javascript"></script>
<script src="scripts/jquery.tipsy.js" type="text/javascript"></script>

<script src="scripts/jquery-1.js" type="text/javascript" charset="utf-8"></script>
    <script src="scripts/jquery-ui-full-1.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
{literal}
<script type="text/javascript">
        $(document).ready(function() {

            $(".signin").click(function(e) {          
				e.preventDefault();
                $("fieldset#signin_menu").toggle();
				$(".signin").toggleClass("menu-open");
            });
			
			$("fieldset#signin_menu").mouseup(function() {
				return false
			});
			$(document).mouseup(function(e) {
				if($(e.target).parent("a.signin").length==0) {
					$(".signin").removeClass("menu-open");
					$("fieldset#signin_menu").hide();
				}
			});			
			
        });
</script>
<script type="text/javascript">
        $(document).ready(function() {

            $(".mysite").click(function(e) {          
				e.preventDefault();
                $("fieldset#mysite_menu").toggle();
				$(".mysite").toggleClass("menu-open");
            });
			
			$("fieldset#signin_menu").mouseup(function() {
				return false
			});
			$(document).mouseup(function(e) {
				if($(e.target).parent("a.mysite").length==0) {
					$(".mysite").removeClass("menu-open");
					$("fieldset#mysite_menu").hide();
				}
			});			
			
        });
</script>
{/literal}    
</head>
<link rel="icon" type="image/ico" href="{Navigator::getBase(1)}images/favicon.ico"></link>
<link rel="shortcut icon" href="{Navigator::getBase(1)}images/favicon.ico"></link>
