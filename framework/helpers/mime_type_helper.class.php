<?php

class Mime_Type_Helper
{

	static function getByFilename($filename)
	{

		$mime_types = array(
		    'txt' => 'text/plain',
		    'htm' => 'text/html',
		    'html' => 'text/html',
		    'php' => 'text/html',
		    'css' => 'text/css',
		    'js' => 'application/javascript',
		    'json' => 'application/json',
		    'xml' => 'application/xml',
		    'swf' => 'application/x-shockwave-flash',
		    'flv' => 'video/x-flv',
		    // images
		    'png' => 'image/png',
		    'jpe' => 'image/jpeg',
		    'jpeg' => 'image/jpeg',
		    'jpg' => 'image/jpeg',
		    'gif' => 'image/gif',
		    'bmp' => 'image/bmp',
		    'ico' => 'image/vnd.microsoft.icon',
		    'tiff' => 'image/tiff',
		    'tif' => 'image/tiff',
		    'svg' => 'image/svg+xml',
		    'svgz' => 'image/svg+xml',
		    // archives
		    'zip' => 'application/zip',
		    'rar' => 'application/x-rar-compressed',
		    'exe' => 'application/x-msdownload',
		    'msi' => 'application/x-msdownload',
		    'cab' => 'application/vnd.ms-cab-compressed',
		    // audio/video
		    'mp3' => 'audio/mpeg',
		    'qt' => 'video/quicktime',
		    'mov' => 'video/quicktime',
		    // adobe
		    'pdf' => 'application/pdf',
		    'psd' => 'image/vnd.adobe.photoshop',
		    'ai' => 'application/postscript',
		    'eps' => 'application/postscript',
		    'ps' => 'application/postscript',
		    // ms office
		    'doc' => 'application/msword',
		    'rtf' => 'application/rtf',
		    'xls' => 'application/vnd.ms-excel',
		    'ppt' => 'application/vnd.ms-powerpoint',
		    // open office
		    'odt' => 'application/vnd.oasis.opendocument.text',
		    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);


		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		} elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		} else {
			return 'application/octet-stream';
		}
	}
	
	static function icon($filename, $contenttype=false) {
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		
		

		switch ($extension) {
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'gif':
			case 'svg':
			case 'xcf':
				return 'fa fa-file-image-o';
			case 'ods':
			case 'xls':
			case 'xlsx':
				return 'fa fa-file-excel-o';
			case 'odt':
			case 'doc':
			case 'doc':
			case 'docx':
				return 'fa fa-file-word-o';
			case 'ppt':
			case 'pptx':
				return 'fa fa-file-powerpoint-o';
			case 'zip':
			case 'rar':
			case 'tar':
			case 'bz2':
			case 'xz':
			case 'gz':
				return 'fa fa-file-archive-o';
			case 'mp3':
			case '3gp':
			case 'aac':
			case 'wav':
				return 'fa fa-file-audio-o';
			case 'avi':
			case 'mov':
			case 'mkv':
				return 'fa fa-file-video-o';
			case 'php':
			case 'json':
			case 'js':
			case 'html':
			case 'css':
			case 'sh':
				return 'fa fa-file-code-o';
			case 'pdf':
				return 'fa fa-file-pdf-o';
			case 'txt':
				return 'fa fa fa-file-text-o';
		}

		return 'fa fa-file-o';
	}	
	
}
