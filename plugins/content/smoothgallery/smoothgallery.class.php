<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class smoothgallery {
	
	function init($text, $settings) {
		
		// convert parameters into class variables
		foreach ( $settings as $arg => $val) {
			$this->{$arg} = $val;
		}
		
		$this->settings = $settings;
	}
	
	function get_script() {
		$script	 = "\n" . '<script type="text/javascript">' . "\n"
  				    . 'function startGallery() {' . "\n";
		$script .= "	var $this->id = new gallery($('$this->id'), {";
		$script .= "height: " . $this->height . ", ";
		$script .= "width: " . $this->width . ", ";
		if (isset($this->showArrows)) $script .=  "showArrows: " . $this->showArrows . ", ";
		if (isset($this->showCarousel)) $script .= "showCarousel: " . $this->showCarousel . ", ";
		if (isset($this->showInfopane)) $script .= "showInfopane: " . $this->showInfopane . ", ";
		if (isset($this->thumbHeight)) $script .= "thumbHeight: " . $this->thumbHeight . ", ";
		if (isset($this->thumbWidth)) $script .= "thumbWidth: " . $this->thumbWidth . ", ";
		if (isset($this->thumbSpacing)) $script .= "thumbSpacing: " . $this->thumbSpacing . ", ";
		if (isset($this->embedLinks)) $script .= "embedLinks: " . $this->embedLinks . ", ";
		if (isset($this->fadeDuration)) $script .= "fadeDuration: " . $this->fadeDuration . ", ";
		if (isset($this->timed)) $script .= "timed: " . $this->timed . ", ";
		if (isset($this->delay)) $script .= "delay: " . $this->delay . ", ";
		if (isset($this->preloader)) $script .= "preloader: '" . $this->preloader . "', ";
		if (isset($this->slideInfoZoneOpacity)) $script .= "slideInfoZoneOpacity: " . $this->slideInfoZoneOpacity . ", ";
		if (isset($this->carouselMinimizedOpacity)) $script .= "carouselMinimizedOpacity: " . $this->carouselMinimizedOpacity . ", ";
		if (isset($this->carouselMinimizedHeight)) $script .= "carouselMinimizedHeight: " . $this->carouselMinimizedHeight . ", ";
		if (isset($this->carouselMaximizedOpacity)) $script .= "carouselMaximizedOpacity: " . $this->carouselMaximizedOpacity . ", ";	
		if (isset($this->baseClass)) $script .= "baseClass: " . $this->baseClass . ", ";
		if (isset($this->textShowCarousel)) $script .= "textShowCarousel: " . $this->textShowCarousel . ", ";
		if (isset($this->lightbox)) $script .= "lightbox: " . $this->lightbox . ", ";
		$script = $this->reverse_strrchr($script, ',');
		$script .= '});' . "\n";
		$script .=	'	}' . "\n" . '	window.onDomReady(startGallery);' . "\n" . '</script>' . "\n";
		return $script;
	}
	
	function loadProfiles($settings, $images) {
		if (count($images) == 1) {
			$this->showArrows = 'false';
			$this->showCarousel = 'false';
		}
		
		// Profile: Single Image with lightbox and caption
		// Contribution by Plamen
		if (isset($this->image)) {
			$this->showArrows = 'false';
			$this->showCarousel = 'false';
		}
		
		if (isset($this->link)) $this->lightbox = 'false';
		
		if (!$this->lightbox) {
			$this->embedLinks = 'false';
		}
		
	}
	
	function get_head() {
		
		global $cur_template, $mosConfig_live_site, $mainframe;
		
		$head = '';
		if ($this->addCSS && strpos( $mainframe->getHead(), '/templates/' . $cur_template . '/css/smoothgallery.css' ) === false ) {
			$head =	 '<link href="plugins/content/smoothgallery/css/jd.gallery.css" rel="stylesheet" type="text/css" />' . "\n"
					.'<link href="plugins/content/smoothgallery/css/slightbox.css" rel="stylesheet" type="text/css" />' . "\n";
		}		
		
		if ($this->addCustomCSS) {
			$head .=	 '<link href="'.$mainframe->getCfg( 'live_site' ) . '/templates/' . $cur_template . '/css/smoothgallery.css" rel="stylesheet" type="text/css" />' . "\n";
		}
		
		// add javascript to head if it's not already added settings specify to add it
		if ($this->addJS && strpos( $mainframe->getHead(), '/plugins/content/smoothgallery/scripts/jd.gallery.js"></script>' ) === false)
			$head 	.= '<script type="text/javascript" src="plugins/content/smoothgallery/scripts/mootools.js"></script>' . "\n" 
					.'<script type="text/javascript" src="plugins/content/smoothgallery/scripts/jd.gallery.js"></script>' . "\n"
					.'<script type="text/javascript" src="plugins/content/smoothgallery/scripts/slightbox.js"></script>' . "\n";
		
		if (!empty($head)) $mainframe->addCustomHeadTag($head);
		
	}
	
	function get_gallery($images) {
		
		$css = "";
		
		$css .= $this->_get_css();
		
		$gallery = "\n" . '<div id="' . $this->id . '" '.$css.'>' . "\n";
		// Please do not remove this line, thank you. taras
		$gallery .= '<div class="candylinks"></div>';
		
		foreach ($images as $image) {
			$gallery .= $this->getImageElement($image) . "\n";
		}
		
		$gallery .= '</div>';
		
		return $gallery;
	}
	
	function _get_css() {
		
		$css='style="';
		
		if (isset($this->alignment)) {
			switch($this->alignment) {
				case 'center': $css .= 'text-align: center; margin-left: auto; margin-right: auto; '; break;
				case 'left' : $css .= 'float: left; clear:left; '; break;
				case 'right' : $css .= 'float: right; clear:right; '; break;
			}
		}

		if (isset($this->style)) $css .= $this->style;
			
		return $css . 'width: '.$this->width.'px; height: '.$this->height.'px; z-index:5; text-indent: -2000px;"';
	}

	function getImageElement($image) {
		
		$element = 	'	<div class="imageElement">' . "\n"
				   .'		<h3>' . $image['title'] . '</h3>' . "\n"
				   .'		<p>' . $image['caption'] . '</p>' . "\n";
		
		if ($this->lightbox && !isset($image['link'])) {
			$element .= '		<a href="'.$image['lightbox']['url'].'" title="'.$image['title'].'" class="open"></a>' . "\n";
		} else if ($image['link']) {
			$element .= '		<a href="'.$image['link'].'" title="'.$image['title'].'" class="open"></a>' . "\n";
		} else {
			$element .= '		<a href="#" title="'.$image['title'].'" class="open"></a>' . "\n";
		}
		
		$element .= '		<img src="'.$image['full']['url'].'" alt="'.$image['title'].'" class="full" />' . "\n";
		if ($this->showCarousel) 
			$element .= '		<img src="'  .$image['thumb']['url'].'" alt="'.$image['title'].' thumb" class="thumbnail" />' . "\n";
		$element .= '	</div>';
		
		return $element;
	}

	function getImageInfo($path, $other=null) {
		
		global $mainframe;
		
		// get file information
		$fileinfo = pathinfo($path);
		$filename = $fileinfo['basename'];
		$directory = $fileinfo['dirname'];
		$ext = $fileinfo['extension'];
		
		$image = array();
		
		$image['filename'] = $filename;
		
		//echo "<!--PATH of IMAGE SIZE: ". $path ." -->";
		list($width, $height, $type, $attr) = getimagesize($path, $info);
		
		if (isset($info["APP13"])) {
			// iptc  info
			$iptc = iptcparse($info["APP13"]);
		}
				
		if (isset($other['title'])) $image['title'] = $other['title'];
		elseif (isset($iptc['2#005'][0])) $image['title'] = $iptc['2#005'][0];
		
		if (isset($other['caption'])) $image['caption'] = $other['caption']; 
		elseif (isset($iptc['2#120'][0])) {
			$description = $iptc['2#120'][0];
			$description = str_replace("\r", "<br/>", $description);
			$description = addslashes($description);
			$image['caption'] = $description; 
		}
		
		$a = stat($path);
		
		$image['size'] = $a['size'];
		if ($a['size'] == 0) $image['sizetext'] = "-";
		else if ($a['size'] > 1024) $image['sizetext'] = (ceil($a['size']/1024*100)/100) . " K";
		else if ($a['size'] > 1024*1024) $image['sizetext'] = (ceil($a['size']/(1024*1024)*100)/100) . " Mb";
		else $image['sizetext'] = $a['size'] . " bytes";
		
		$image['width'] = $width;
		$image['height'] = $height;
		if (isset($iptc['2#025'][0])) $image['keywords'] = $iptc['2#025'][0];
		if (isset($iptc['2#080'][0])) $image['author'] = $iptc['2#080'][0];
		if (isset($iptc['2#116'][0])) $image['copyright'] = $iptc['2#116'][0];
		
		$path = str_replace( '//', '/', $path );
		$path = str_replace( '\\', '/', $path );
		$image['abs_path'] = $path;
		$image['url'] = $mainframe->getCfg( 'live_site' ) . str_replace(JPATH_SITE , '' ,$path);
		//echo "<!--IMAGE URL: ".$image['url'] ." -->";
		
		return $image;
	}
	
	function get_resized ($path, $dest, $max_width, $max_height) {
		//global $mosConfig_absolute_path;
		
		// get file information
		$fileinfo = pathinfo($path);
		$filename = $fileinfo['basename'];
		$directory = $fileinfo['dirname'];
		$ext = $fileinfo['extension'];
		
		//echo "<!-- IMAGE SIZE: ". $path ." -->";
		$size = getimagesize($path);
		//echo "<!-- SIZE: ". print_r($size) ." -->";
		
		$width = $size[0];
		$height = $size[1];
		
		// get the ratio needed
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		
		//echo "<!-- max width: ". $max_width ." -->";
		//echo "<!-- max height: ". $max_height ." -->";
		
		// if image already meets criteria, load current values in
		// if not, use ratios to load new size info
		if (($width <= $max_width) && ($height <= $max_height) ) {
			$tn_width = $width;
			$tn_height = $height;
		} else if (($x_ratio * $height) < $max_height) {
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		} else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}
		
		//echo "<!-- filename: ". $filename ." -->";
		/* Caching additions by Trent Davies */
		// first check cache
		// cache must be world-readable
		$resized = array('filename' => $tn_width.'x'.$tn_height.'-'.$filename, 
						'path' =>  $dest . $tn_width.'x'.$tn_height.'-'.$filename);
		

		//echo "<!-- RESIZED: ". print_r($resized) ." -->";
		// if file exists in cache return path to cached filed else create new file cache file and return address to it
		// fix provided by chriswarr & Adrea from www.adigitali.it
		if (file_exists($resized['path'])) {
			$resized = array_merge($resized, $this->getImageInfo($resized['path']));
			
			return $resized; 
		}
		
		
		// read image
		switch ($ext) {
			case 'jpg':     // jpg
			case 'JPG':
			case 'JPEG':
				$src = imagecreatefromjpeg($path) or notfound();
				break;
			case 'png':     // png
			case 'PNG':
				$src = imagecreatefrompng($path) or notfound();
				break;
			case 'gif':     // gif
			case 'GIF':
				$src = imagecreatefromgif($path) or notfound();
				break;
			default:
				notfound();
		}
		
		// set up canvas
		$dst = imagecreatetruecolor($tn_width,$tn_height);
		
		if (function_exists('imageantialias')) imageantialias($dst, true);
		
		// copy resized image to new canvas
		imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
		
		/* Sharpening adddition by Mike Harding */
		// sharpen the image (only available in PHP5.1)
		/*if (function_exists("imageconvolution")) {
			$matrix = array(    array( -1, -1, -1 ),
		                    array( -1, 32, -1 ),
		                    array( -1, -1, -1 ) );
			$divisor = 24;
			$offset = 0;
		
			imageconvolution($dst, $matrix, $divisor, $offset);
		}*/
		
		// send new image to save
		imagejpeg($dst, $resized['path'], $this->jpgquality); // write the thumbnail to cache as well...
		
		// clear out the resources
		imagedestroy($src);
		imagedestroy($dst);
		
		$resized = array_merge($resized, $this->getImageInfo($resized['path']));
		
		return $resized;
	}
	
	// reverse strrchr() - PHP v4.0b3 and above
	function reverse_strrchr($haystack, $needle)
	{
	   $pos = strrpos($haystack, $needle);
	   if($pos === false) {
	       return $haystack;
	   }
	   return substr($haystack, 0, $pos);
	}
}
?>