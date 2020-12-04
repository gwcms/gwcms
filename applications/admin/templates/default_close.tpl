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





{if GW_Lang::$developLnResList}
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

