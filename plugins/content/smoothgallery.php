<?php
ini_set("display_errors", 0);
/** Name:	SmoothGallery + Lightbox for Joomla 1.5
	Date:	February 4, 2009
	Author:	Stacy D. Coil
	Web:	Sambaworks.com
	email:	sdcoil-at-sambaworks-dot-com
	
	This a re-release of what I found in the extension directory for 1.5.
	I waited for someone to implement Smooth Gallery + Lightbox from Oscandy.com
	for Joomla 1.5.  When I finally found it, I tried it; but it is severely broken.
	I've fixed it as best as I can.
	
	Please see the readme for changes and to do list
	
**/
/**
	SmoothGallery2 Joomla Plugin is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	SmoothGallery2 Joomla Plugin is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with JonDesign's SmoothGallery; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// global $database;
$mainframe->registerEvent( 'onPrepareContent', 'smoothgallery_onprepare' );

include('smoothgallery/smoothgallery.class.php');
include('smoothgallery/content_smoothgallery.php');

/**
* smoothgallery Joomla Mambot
* please see http://www.oscandy.com/author/taras for instructions on how to use this mambot
* email tarasm@gmail.com
*/

function smoothgallery_onprepare( &$row, &$params, $page=0 ) {
	// expression to search for with params

	$regex = '/{(smoothgallery)\s*(.*?)}/i';

	// simple performance check to determine whether plugin should process further

	if ( JString::strpos( $row->text, 'smoothgallery' ) === false ) {		
		return true;
	} 
	
	global $mainframe, $database;

	//echo "<pre>---Params------------------------------------------------\n";
	//echo print_r($params);
	//echo "\n---------------------------------------------------------</pre>\n";
	//echo "<pre>---Row---------------------------------------------------\n";
	//echo print_r($row);
	//echo "\n---------------------------------------------------------</pre>\n";
	//echo "<pre>---mainframe---------------------------------------------\n";
	//echo print_r($mainframe);
	//echo "\n---------------------------------------------------------</pre>\n";
	
	$database =& JFactory::getDBO();

	//echo "<pre>---Database----------------------------------------------\n";
	//echo print_r($database);
	//echo "\n---------------------------------------------------------</pre>\n";

	// find all instances of mambot and put in $matches

	$matches = array();

	preg_match_all( $regex, $row->text, $matches, PREG_SET_ORDER );

	$output = '';

	$css='';

	$settings['imagesfolder'] = 'images/stories';

	$settings['cachefolder'] = 'plugins/content/smoothgallery/cache/';

	// check if param query has previously been processed

	if ( !isset($mainframe->_content_plugin_params['smoothgallery']) ) {

		// load plugins params info
		// Is this necessary?
		//$query = "SELECT params"
		//. "\n FROM #__plugins"
		//. "\n WHERE element = 'smoothgallery'"
		//. "\n AND folder = 'content'"
		//;
		$query = "SELECT params FROM #__plugins WHERE element = 'smoothgallery' AND folder = 'content'";

		$database->setQuery( $query );

		// $database->loadObject($plugin);
		$plugin = $database->loadResult();

		//echo "<pre>---plugin------------------------------------------------\n";
		//echo print_r($plugin);
		//echo "\n---------------------------------------------------------</pre>\n";

		
		// save query to class variable
		$mainframe->_content_plugin_params['smoothgallery'] = $plugin;
	}

	//echo "<pre>---mainframe-after params--------------------------------\n";
	//echo print_r($mainframe);
	//echo "\n---------------------------------------------------------</pre>\n";


	// pull query data from class variable

	//$plugin = $mainframe->_content_plugin_params['smoothgallery'];
	$paramString = "parameter=\n";

	$plugin =&JPluginHelper::getPlugin('content', 'smoothgallery');
	
 	$Params = new JParameter( $plugin->params );
	//echo "<!-- botParams: " . print_r($botParams) . "-->";
	//$botParams = $Params->_raw;

	//echo "<pre>---plugin------------------------------------------------\n";
	//echo print_r($plugin);
	//echo "\n---------------------------------------------------------</pre>\n";
	//echo "<pre>---Params------------------------------------------------\n";
	//echo print_r($Params);
	//echo "\n---------------------------------------------------------</pre>\n";

	//	echo "<!-- botParams: " . print_r($Params) . "-->";

	$i=0;

	

	// for each occurance of mambot

	foreach ($matches as $match) {

		$i++;
		$settings['counter'] = $i;

		unset($gallery);

		$html = '';

		// convert mambot parameters to array 
		
		//print_r(array_values($match));

		parse_str( html_entity_decode( $match[2] ), $args );

		//print_r(array_values($settings));

		$settings = array_merge((array)$Params->_registry[_default][data], $settings, $args);
		//echo "<!-- SETTINGS: " . print_r($settings) . "-->";

		// Declare Smooth Gallery Class
		$gallery = new content_smoothgallery();

		// pass arguments to initialize the gallery
		$gallery->init($row, $settings);

		// pass settings to defaults loader
		$gallery->loadDefaults($settings);

		// get images
		$images = $gallery->get_images();

		// pass settings to profile loader
		$gallery->loadProfiles($settings, $images);

		// get gallery html
		$html .= $gallery->get_gallery($images);

		// add window.onDomReady Script
		$html .= $gallery->get_script();

		// get head from 
		$gallery->get_head();

		// place the generated text back into the original text instead of the match
		$row->text = str_replace($match[0], $html, $row->text);	
	}

	return true; 
}

?>