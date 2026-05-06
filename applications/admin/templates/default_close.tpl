{if !isset($smarty.get.clean) && !$smarty.get.clean && !$no_standart_cms_frame && !$smarty.get.print_view}
				</div>
                <!--===================================================-->
                <!--End page content-->


            </div>
            <!--===================================================-->
            <!--END CONTENT CONTAINER-->
			
			
			
			
			{include file="menu.tpl"}
</div>

		<footer id="footer">{include file="footer.tpl"}</footer>
        <!--===================================================-->
        <!-- END FOOTER -->


        <!-- SCROLL PAGE BUTTON -->
        <!--===================================================-->
        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>
        <!--===================================================-->



    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->
{/if}






{if GW_Lang::$developLnResList && $app->path!="system/translations/flatedit"}
	{$app->innerProcess('system/translations/flatedit1')}
{/if}


{include "default_close_clean.tpl"}

{*

<br /><br />

        <span class="cleaner"></span>
    </div>
    <div id="push"></div>
</div>

<div id="footer">
    
</div>

{/if}




</body>
</html>
*}



{if $app->site->favico}
	{$image=$app->site->favico}
	<style>
		#content-container{ background-color: transparent; }
		
		body::before{
			content: "";
			position: fixed;
			inset: 0;
			background: url('/applications/admin/static/img/brand_logo_background.webp') center center / min(80vw, 820px) no-repeat;
			filter: grayscale(100%);
			opacity: 0.1;
			pointer-events: none;
			z-index: -1;
		}		
	</style>	
{/if}

