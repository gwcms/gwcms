
{if !$nocontainer}
	
	</div> <!-- cell  -->
	</div> <!-- row  -->
	</div> <!-- container  -->
{/if}

</div>
</div>


<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '172271779814711',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) { return; }
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>




{include file="footer.tpl"}
