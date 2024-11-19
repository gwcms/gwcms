{include file="default_form_open.tpl" action=crop form_width="100%"}

{$image = $item}
{$item->url}&timestamp={$item->timestamp}

  <div class="cropcontainer">

    <div>
	<img  id="cropimage" src="{$item->url}?timestamp={$item->timestamp}" border="{$border|default:0}" />
    </div>

	  

	  <input id="cropdata" name="data" style="width:100%">
  

  </div>


{$m->addIncludes("cropper/css", 'css', "`$app_root`static/pack/cropper/cropper.css")}

	{capture append=footer_hidden}

	<script type="text/javascript">
		
require(['gwcms'], function(){  require(['pack/cropper/cropper_gw_mod'], function(){ initGWcropper() }) });


function initGWcropper()
{
	var image = document.querySelector('#cropimage');
	  
      var cropper = new Cropper(image, {
        ready: function (event) {
          // Zoom the image to its natural size
          //cropper.zoomTo(1);
        },

        crop: function (event) {
          document.querySelector('#cropdata').value = JSON.stringify(cropper.getData());
          //cropBoxData.textContent = JSON.stringify(cropper.getCropBoxData());
        },

        zoom: function (event) {
          // Keep the image in its natural size
          if (event.detail.oldRatio === 1) {
            event.preventDefault();
          }
        },
      });
}

window.addEventListener('DOMContentLoaded', function () {
		
	
    
	  
	  /*
	  setTimeout(function(){
		  //document.querySelector('.cropper-container').style.cssText = "width: 500px; height: 500px;"
	
		  console.log(document.querySelector('.cropper-container'));
	  }, 1000)
	  */
    });
    
</script>
  <style>
    .cropcontainer {
      max-width: 100%;
      max-height: 80vh;
      margin: 20px auto;
      min-height: 300px;
    }

    #cropimage {
      max-width: 100%;
      max-height: 80vh;
      
    }
  </style>	
{/capture}

{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time,size_human,full_filename,dimensions,extension]}