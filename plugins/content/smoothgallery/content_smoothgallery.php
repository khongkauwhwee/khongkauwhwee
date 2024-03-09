<?php
		//$GLOBALS['mosConfig_live_site']		= substr_replace(JURI::root(), '', -1, 1);
		//$GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
		
		
defined( '_JEXEC' ) or die( 'Restricted access' );

class content_smoothgallery extends smoothgallery{
	
	function init($text, $settings) {
		parent::init($text, $settings);
		
		$this->row = $text;
		//echo "<!-- ROW: " . print_r($this->row) . "-->";
	}
	
	function loadProfiles($settings, $images) {
		parent::loadProfiles($settings, $images);
		// Profile: gallery as menu
		//echo "<!-- THIS: " . print_r($this->data) . "-->";
		if (isset($this->menu)) {
			$this->lightbox = false;
			$this->timed = true;
			$this->showCarousel = false;
		}
	}
	
	function loadDefaults() {
		if (!isset($this->id)) $this->id = "gallery_" . $this->row->id . '_'. $this->counter;	
	}
	
	function get_images() {
		// if (isset($this->username) && isset($this->album)) return $this->process_images($this->getImagesFromWebAlbum($this->username, $this->album));	
		if (!empty($this->username) && !empty($this->album)) {
			//echo "<!-- Calling getImagesFromWebAlbum -->\n";
			return $this->process_images($this->getImagesFromWebAlbum($this->username, $this->album));	
		}
		if (!empty($this->album)) {
			//echo "<pre>!-- Calling getUGMAlbum for album " . $this->album . "--</pre>\n";
			return $this->process_images($this->getUGMAlbum($this->album));
		}
		if (!empty($this->folder)) {
			//echo "<pre>!-- Calling getFolderImages for folder " . $this->folder . "--<pre>\n";
			return $this->process_images($this->getFolderImages($this->folder));
		}
		if (!empty($this->image)) {
			//echo "<pre>!-- Calling getSingleImage for folder " . $this->image . "--<pre>\n";
			return $this->process_images($this->getSingleImage($this->image));
		}
		if (!empty($this->menu)) {
			echo "<pre>!-- Calling getImagesFromMenu for menu " . $this->menu . " --- This doesn't yet work. --<pre>\n";
			return $this->process_images($this->getImagesFromMenu($this->menu));
		}
		if (empty($this->folder) && !empty($this->row)) return $this->process_images($this->getMosImages($this->row));
	}

	function getImagesFromWebAlbum($username, $albumname) {
		//global $mosConfig_absolute_path;
		
		include_once("php/simplepie.inc");
		
		$cache = JPATH_SITE.'/plugins/content/smoothgallery/cache';
		
		$images = array();
		$image = array();
				
		$i = 0;

		$feed = new SimplePie();
		$feed->set_feed_url("http://picasaweb.google.com/data/feed/api/user/$username/album/$albumname?kind=photo");
		if ($this->picasa_curl) {
			$feed->force_fsockopen(true);
		}
		$feed->enable_cache();
		$feed->set_cache_location('cache/');
		$feed->init();
		$feed->handle_content_type();
		$feed->order_by_date=false;		
		
		foreach ($feed->get_items() as $item) {
    		if ($enclosure = $item->get_enclosure()) {
					$url = $enclosure->get_link();
					$url = str_replace(" ", "", $url);
					$fileinfo = pathinfo($url);
					$filename = $fileinfo['basename'];
					$directory = $fileinfo['dirname'];
					$ext = $fileinfo['extension'];
					
					$image['title'] = $feed->get_title();
					$image['caption'] = $enclosure->get_description();
					
					if (!file_exists($this->settings['cachefolder'] . $albumname)) {
						mkdir($this->settings['cachefolder'] . $albumname, 0777);
					}
					
					$dst = JPATH_SITE . '/' . $this->settings['cachefolder'] . $albumname . '/' . str_replace(" ", "", $filename);

					if (!file_exists($dst)) {
						// read image
						switch ($ext) { 
							case 'jpg':		// jpg
							case 'JPG':
							case 'JPEG':  
								$img = imagecreatefromjpeg($url) or notfound();
								break;
							case 'png':     // png
							case 'PNG':
								$img = imagecreatefrompng($url) or notfound();
								break;
							case 'gif':     // gif
							case 'GIF':
								$img = imagecreatefromgif($url) or notfound();
								break;
						default:
							notfound();
						}
						
						$canvas = imagecreatetruecolor($enclosure->width, $enclosure->height);
						
						if (function_exists('imageantialias')) imageantialias($canvas, true);
						
						imagecopyresampled ($canvas, $img, 0, 0, 0, 0, $enclosure->width, $enclosure->height, $enclosure->width, $enclosure->height);
						imagejpeg($canvas, $dst, $this->jpgquality);
					
						imagedestroy($canvas);
						imagedestroy($img); 
					}
					
					$image['path'] = $dst;
					
					$images[$i] = $this->getImageInfo($dst, $image);
					$i++;				
				
    		}
		}
		return $images;
	}

	function getUGMAlbum($album_id) {
		
		//global $database, $mosConfig_absolute_path ;
		global $database;
		//echo "<pre>!-- getUGMAlbum called for album " . $album_id . " --</pre>\n";
		$fullpath = JPATH_SITE.'/images/stories/ugm/'.$album_id.'/images/';
	
		$database =& JFactory::getDBO();
	
		$images = array();
		$info = array();
		
		// Checking to see if UGM Component has been installed
		$query = "SELECT COUNT( * ) FROM #__components WHERE link LIKE '%com_ugm%'";
		//echo "<pre>";
		//echo print_r($database);
		//echo "</pre>\n";

		$database->setQuery($query);
		$answer = $database->loadResult();
		//echo "<pre>---answer------------------------------------------------\n";
		//echo print_r($answer);
		//echo "\n---------------------------------------------------------</pre>\n";
		
		// echo "<pre>" . print_r($database) ."</pre>\n";
		if ($answer)
		{
			// Check to see if requested album exists!
			//echo "<pre>!--- going to query the database for pictures ---</pre>\n";
			//$query = 'SELECT * FROM #__ugmalbum WHERE id='.$album_id.';' ;
			$query = "SELECT * FROM #__ugmalbum WHERE id='" . $album_id . "'";
			$database->setQuery($query);
			//$database->loadObject($album) ;
			$album = $database->loadAssoc();
			
		
			//echo "<pre>---album-from-" . $query . "-----------------------------------------------\n";
			//echo print_r($album);
			//echo "\n------------" . $album['caption'] . "---------------------------------------------</pre>\n";

			if ($album)
			{
				$this->ugm = true ;
				$query = 'SELECT * FROM #__ugmimage WHERE album_id='.$album_id ;
				$database->setQuery($query) ;
				$imgArray = $database->loadAssocList() ;

				//echo "<pre>---imgArray----------------------------------------------\n";
				//echo print_r($imgArray);
				//echo "\n---------------------------------------------------------</pre>\n";
				
				$info['title'] = $album['caption'];
				
				$i = 0;
				foreach ($imgArray as $key => $val)
				{
					$info['caption'] = $val['caption'];
					$images[$i] = $this->getImageInfo( $fullpath . $val['name'], $info);
					$i++;
				}
				//echo "<pre>---images------------------------------------------------\n";
				//echo print_r($images);
				//echo "\n---------------------------------------------------------</pre>\n";
				return $images;
			}
		}
	}

	function getFolderImages($path) {
		//global $mosConfig_absolute_path;
		//$GLOBALS['mosConfig_live_site']		= substr_replace(JURI::root(), '', -1, 1);
		//$GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
		
		//echo "<pre>!-- getFolderImages called for path " . $path . " --</pre>\n";
		
  		$files = array();
   		$images = array();
   		//echo "<!-- JPATH_SITE: " . print_r($mainframe) . "-->";
		$fullpath = JPATH_SITE . "/" . $path . "/";

		//echo "<pre>!-- fullpath = " . $fullpath . " --</pre>\n";

   		if (is_dir($fullpath)) {
       		if ($dh = opendir($fullpath)) {
           		while (($file = readdir($dh)) !== false) {
           			//$ext = end(explode('.',$file));
					// Added strtolower to make comparision case insensitive
           			$ext = strtolower(end(explode('.',$file)));
					// if an image (need to change to case insensitive)
               		if ($ext == "jpg" || $ext == "png" || $ext == "gif" ) {
						array_push($images, $this->getImageInfo( $fullpath . $file));
					}
           		}
           		closedir($dh);
       		} else die ("Cannot open directory: " . $fullpath);
   		} else die ("Path is not a directory: " .$fullpath); 
   		
		//echo "<pre>---images------------------------------------------------\n";
		//echo print_r($images);
		//echo "\n---------------------------------------------------------</pre>\n";
		
		if (isset($this->sort) && $this->sort =='random') shuffle($images);
   		else $images = $this->array_sort($images,$this->sort);
		
   		return $images;
	}
	
	function getMosImages($row) {
		//global $mosConfig_absolute_path;
		// assemble the images
		$images = array();
	
		// split on \n the images fields into an array
		$row->images = explode( "\n", $row->images );
		//print_r($row->images);

		// needed to stopping loading of images for the introtext
		$start = 0;
		$total 	= count( $row->images );
		
		for ( $i = $start; $i < $total; $i++ ) {
			$img = trim( $row->images[$i] );

			// split on pipe the attributes of the image
			if ( $img ) {
				$attrib = explode( '|', trim( $img ) );
				// $attrib[0] image name and path from /images/stories

				// $attrib[2] alt & title
				if ( !isset($attrib[2]) || !$attrib[2] ) $attrib[2] = '';
				else $attrib[2] = htmlspecialchars( $attrib[2] );
				
				$info = Array();
				$info['title'] =  $attrib[2];
				$info['caption'] =  $attrib[4];
				
				$imagepath =  JPATH_SITE.'/'. $this->imagesfolder .'/'. $attrib[0];
								
				$images[$i] = $this->getImageInfo($imagepath , $info);	
			}
		}
		return $images;
	}
	
	function getSingleImage($path) {
		//global $mosConfig_absolute_path;
		
		$images = array();
		
		//if (!file_exists(JPATH_SITE.$path)) {
		if (!file_exists(JPATH_SITE.'/'.$path)) {
			echo JPATH_SITE.'/'.$path .' is not a valid path';
			die;
		}
		
		//Commented out the next several lines.  Doesn't seem to do anything
		//$path_parts = pathinfo($path);
		
		// get name
		//$name = $path_parts['basename'];
						
		// get directory
		//$directory = $path_parts['dirname'] . '/';
		
		$images[0] = $this->getImageInfo($path);
		
		if (isset($this->title)) $images[0]['title'] = $this->title;
		if (isset($this->caption)) $images[0]['caption'] = $this->caption;
		if (isset($this->link)) $images[0]['link'] = $this->link;

		//echo "<pre>---this--------------------------------------------------\n";
		//echo print_r($this);
		//echo "\n---------------------------------------------------------</pre>\n";
		//echo "<pre>---images------------------------------------------------\n";
		//echo print_r($images);
		//echo "\n---------------------------------------------------------</pre>\n";
		
		return $images;
	}
	
	function getImagesFromMenu($menu) {
	
		global $database;
		$database =& JFactory::getDBO();
		
		//Look up all items in menu
		//$query 	= "SELECT distinct menu.menutype,menu.name,menu.type,menu.link,menu.componentid,menu.ordering,menu.published,menu.params" 
		//		. "\n FROM #__menu AS menu "
		//		. "\n WHERE menu.menutype='$menu' AND menu.published='1'"
		//		. "\n ORDER BY ordering ASC";
		//$query 	= "SELECT distinct menu.menutype,menu.name,menu.type,menu.link,menu.componentid,menu.ordering,menu.published,menu.params FROM #__menu AS menu WHERE menu.menutype='" . $menu . "' AND menu.published='1' ORDER BY ordering ASC";
		$query 	= "SELECT * FROM #__menu WHERE menutype='mainmenu'";
				
		$database->setQuery($query);
		//$menus = $database->loadObjectList();
		$menus = $database->loadResult();
		
		echo "<pre>---query-------------------------------------------------\n";
		echo print_r($query);
		echo "\n---------------------------------------------------------</pre>\n";
		echo "<pre>---database----------------------------------------------\n";
		echo print_r($database);
		echo "\n---------------------------------------------------------</pre>\n";		
		echo "<pre>---menus-------------------------------------------------\n";
		echo print_r($menus);
		echo "\n---------------------------------------------------------</pre>\n";

		$images = array();
		foreach ($menus as $menu) {
			$params = new mosParameters( $menu->params );
			$params = $params->_params;
			$image = $this->getImageInfo( 'images/stories/').$params->menu_image;
			if (isset($this->caption)) $image['caption'] = $this->caption;
			$image['title'] = $menu->name;
			$image['link'] = $menu->link;
			array_push($images, $image);
		}
		
		return $images;
	}
	
	function process_images($images) {
		
		//global $mosConfig_absolute_path;
		
		$processed = array();

		foreach ($images as $image) {
			$cachefolder = $this->MakeSafePath($this->settings['cachefolder'], $image['abs_path']);
									
			if ($this->showCarousel) {
				$thumb = $this->get_resized($image['abs_path'], JPATH_SITE.'/'.$cachefolder, $this->thumbWidth, $this->thumbHeight);
				$image['thumb'] = $thumb;
				$image['thumb']['url'] = $cachefolder.$image['thumb']['filename'];				
			}
			
			$full = $this->get_resized($image['abs_path'], JPATH_SITE.'/'.$cachefolder, $this->width, $this->height);
			$image['full'] = $full;
			$image['full']['url'] = $cachefolder.$image['full']['filename'];
			
			if ($this->lightbox) {
				$lightbox = $this->get_resized($image['abs_path'], JPATH_SITE.'/'.$cachefolder, $this->lightboxWidth, $this->lightboxHeight);
				$image['lightbox'] = $lightbox;
				$image['lightbox']['url'] = $cachefolder.$image['lightbox']['filename'];	
			}
			
			array_push($processed, $image);
		}
		
		return $processed;
	}
	
	// cross platform path maker by Vauntie
	function MakeSafePath($cachebase, $path='', $mode = NULL) {
		global $mosConfig_dirperms, $mosConfig_absolute_path;
		echo "<!-- PATH: " . $path . " -->";
		// convert windows paths
		$path = str_replace( '\\', '/', $path );
		$path = str_replace( '//', '/', $path );
		
		// get root
		$JPATH = str_replace("\\", "/", JPATH_SITE);
		$path = str_replace($JPATH , '', $path);
		echo "<!-- DOC ROOT: " . $JPATH . " -->";
		echo "<!-- PATH After: " . $path . " -->";
		
		// get file information
		$fileinfo = pathinfo($path);
		$filename = $fileinfo['basename'];
		$path = $fileinfo['dirname'];
		$ext = $fileinfo['extension'];
		
		
		$result = rtrim($cachebase,'/').'/'.ltrim($path,'/');
		
		// check if dir exists
		if (file_exists( $result )) return $result . '/';
			
		// set mode
		$origmask = NULL;
		if (isset($mode)) {
			$origmask = @umask(0);
		} else {
			if ($mosConfig_dirperms=='') {
				// rely on umask
				$mode = 0755;
			} else {
				$origmask = @umask(0);
				$mode = octdec($mosConfig_dirperms);
			} // if
		} // if
		
		$parts = explode( '/', $path );
		$n = count( $parts );
		
		if ($n < 1) {
			if (substr( $cachebase, -1, 1 ) == '/') {
				$cachebase = substr( $cachebase, 0, -1 );
				$fname = $path.'index.html';
			}else{
				$fname = $path.'/index.html';
			}                      
	
			if (@mkdir($cachebase, $mode)){
				if (!file_exists( $fname )) {
					file_put_contents($fname,'<html><\html>');
				}
			}
		} else {

			$path = $cachebase;
			
			for ($i = 0; $i < $n; $i++) {
								//echo "<!--PATH: ".$path ." -->";
				$path .= $parts[$i] . '/';
								//echo "<!--PATH2: ".$path ." -->";
				if (!file_exists( $path )) {
					
					if (!@mkdir(substr($path,0,-1),$mode)) {
						break;
					}
				}
				
				$fname = $path.'index.html';
				
				if (!file_exists( $fname )) {
					file_put_contents($fname,'<html><\html>');
				}
			}
		}

		if (isset($origmask)) {
			@umask($origmask);
		}
		
		return $path;
	}
	
	function array_sort($array, $type='asc'){
	   $result=array();
	   foreach($array as $var => $val){
		   $set=false;
		   foreach($result as $var2 => $val2){
			   if($set==false){
				   if($val>$val2 && $type=='desc' || $val<$val2 && $type=='asc'){
					   $temp=array();
					   foreach($result as $var3 => $val3){
						   if($var3==$var2) $set=true;
						   if($set){
							   $temp[$var3]=$val3;
							   unset($result[$var3]);
						   }
					   }
					   $result[$var]=$val;   
					   foreach($temp as $var3 => $val3){
						   $result[$var3]=$val3;
					   }
				   }
			   }
		   }
		   if(!$set){
			   $result[$var]=$val;
		   }
	   }
	   return $result;
	}
}
?>