<?php


class GW_Image_Manipulation
{
    var $width;
    var $height;
    var $alignment;
    var $type;
    
    var $im;
    
    function __construct( $file )
    {
		$this->file=$file;
		$this->initImage();
    }
    

    function initImage()
    {
     
        list( $this->width, $this->height, $type) = @getimagesize( $this->file );

        $this->alignment = $this->width > $this->height ? "v" : "h";
        
        switch ($type)
        {
            case 1:
                $this->im = ImageCreateFromGIF($this->file);
                $this->type = "gif";
                break;
            
            case 2:
                $this->im = ImageCreateFromJPEG($this->file);
                $this->type = "jpg";
                break;

            case 3:
                $this->im = ImageCreateFromPNG($this->file);
                $this->type = "png";
                break;
                
            default:
                die( "Error: unsupported image format" );               
        }
    }
    
    
	/**
	 * required params
	 * width or height
	 * method - auto | crop | strict | fill | forcrop
	 * 
	 */    
    
    function resize($params=Array())
    {
    	$w = (int)$params['width'];
    	$h = (int)$params['height'];
    	
        // resize method
        $method = empty($params['method']) ? 'auto' : $params['method'];
        
        // background color
        $bgcolor = empty($params['bgcolor']) ? 'FFFFFF' : $params['bgcolor'];
        list($bg_red, $bg_green, $bg_blue) = $this->hex2array($bgcolor);

        // calculate size of resized image
        list($rw, $rh) = $this->calcResizedSize($w, $h, $this->width, $this->height, $method);
        
        // calculate size of resized image (with auto method)
		list($arw, $arh) = $this->calcResizedSize($w, $h, $this->width, $this->height, ($method=='crop'?'forcrop':'auto') );

        
        // create new image
        $image_resized = imagecreatetruecolor($rw, $rh);
        


        
		if($this->type=='png' || $this->type=='gif')
		{
			$trnprt_indx = imagecolortransparent($this->im);
   
			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {
   
				// Get the original image's transparent color's RGB values
				$trnprt_color    = imagecolorsforindex($this->im, $trnprt_indx);
   
				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				   
				// Completely fill the background of the new image with allocated color.
				imagefill($image_resized, 0, 0, $trnprt_indx);
				   
				// Set the background color for new image to transparent
				imagecolortransparent($image_resized, $trnprt_indx);
  			
		
			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($this->type=='png') {
   
				// Turn off transparency blending (temporarily)
				imagealphablending($image_resized, false);
   
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
   
				// Completely fill the background of the new image with allocated color.
				imagefill($image_resized, 0, 0, $color);
   
				// Restore transparency blending
				imagesavealpha($image_resized, true);
			}
		}
		else //jpeg
		{
	        $bgColor = imagecolorallocate($image_resized, $bg_red, $bg_green, $bg_blue);
	        // fill background
	        imagefill($image_resized, 0, 0, $bgColor);		
		}
        
        // calculate destination position
        $dx = floor($rw / 2 - $arw / 2);
        $dy = floor($rh / 2 - $arh / 2);
        
        // copy image depending on resize method
        switch ($method)
        {
            case 'strict':
                imagecopyresampled($image_resized, $this->im, 0, 0, 0, 0, $rw, $rh, $this->width, $this->height);
                break;
                
            default:
                imagecopyresampled($image_resized, $this->im, $dx, $dy, 0, 0, $arw, $arh, $this->width, $this->height);
        }                
        
        imagedestroy($this->im);

        $this->im =& $image_resized;
        $this->width = $rw;
        $this->height = $rh;
    }   
    
    /**
     * Calculate resized image size
     * @static
     * @param int $w preferred width
     * @param int $h preferred height
     * @param int $cw current width
     * @param int $ch current height
     * @param string $method resize method
     * @return array items: 0 - width, 1 - height, 2 - reserved for future, 3 - img attributes
     */
    function calcResizedSize($w, $h, $cw, $ch, $method = 'auto')
    {
        switch ($method)
        {
            case 'crop':
            case 'strict':
            case 'fill':
                $rw = $w;
                $rh = $h;
                break;
                
            case 'forcrop':
                // if image is smaller then preferred - do not resize
                if ($cw < $w && $ch < $h)
                {
                    $rw = $cw;
                    $rh = $ch;
                    break;
                }
                
                $k1 = @($cw / $w);
                $k2 = @($ch / $h);
                $k = $k1 < $k2 ? $k1 : $k2;
                $rw = (int)($cw / $k);
                $rh = (int)($ch / $k);      
                break;
                            
            default:
                // if image is smaller then preferred - do not resize
                if ($cw < $w && $ch < $h)
                {
                    $rw = $cw;
                    $rh = $ch;
                    break;
                }
            
                $k1 = @($cw / $w);
                $k2 = @($ch / $h);
                $k = $k1 > $k2 ? $k1 : $k2;
                $rw = (int)($cw / $k);
                $rh = (int)($ch / $k);      
        }
        
        return array($rw, $rh, NULL, "width='{$rw}' height='{$rh}'");
    }
    
    function clean()
    {
        imagedestroy( $this->im );
    }
    
    function hex2array($hex_color)
    {
        $hex_color = str_replace('#', '', $hex_color);
        sscanf($hex_color, "%2x%2x%2x", $red, $green, $blue);
        return array($red, $green, $blue);
    }
    
    function save($file, $type='auto', $file_mode=0666)
    {
       // if ( $this->type == "gif" ) $this->convert("png");
    	
    	if($type=='auto')
    		$type=$this->type;
    	
    	
        switch ( $type )
        {
            case 'png': imagepng($this->im, $file);break;
            case 'jpg': imagejpeg($this->im, $file, 85);break;
			case 'gif': imagegif($this->im, $file);break;
        }
        
        @chmod($file, $file_mode);
        
        return $file;
    }
    
	function isAnimatedGif($filename)
	{
		$filecontents=file_get_contents($filename);
	
		$str_loc=0;
		$count=0;
		while ($count < 2) # There is no point in continuing after we find a 2nd frame
		{
			$where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
			if ($where1 === FALSE)
				break;
	
			$str_loc=$where1+1;
	
			if ( ($where2=strpos($filecontents,"\x00\x2C",$str_loc)) === FALSE)
				break;
				
			if ($where1+8 == $where2)
				$count++;
					
			$str_loc=$where2+1;
		}
		return $count > 1;
	}
    
}
