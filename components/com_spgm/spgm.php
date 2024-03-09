<?php
/********************************************************************
Copyright 2009-2010 Chris Gaebler
Date    :   3 March 2010
Licence :	GNU/GPL
Description:  A Joomla! 1.5 component for SPGM picture gallery
Please see the pdf documentation at http://extensions.lesarbresdesign.info
*********************************************************************
This file is part of SPGM for Joomla and Mambo
This is free software. You can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation.
*********************************************************************/
defined('_JEXEC') or die('Restricted Access'); 

// try to get parameters from the active menu item

$menus	= &JSite::getMenu();
$item	= $menus->getActive();
if ($item != null)			// is there an active menu item ?
	{
	$itemid = $item->id;
	$params = new JParameter($item->params);
	}
else
	if ($itemid != null)	// if not, was a menu item id passed in?
		$params = getParams($id);

if ($params == null)
	{
	echo "Unable to get menu parameters";
	return;
	}

// Draw the page title and top text if any

if ($params->get('page_hdr') != '')
	echo "\n".'<div class="componentheading">'.$params->get('page_hdr').'</div>';
if ($params->get('top_text') != '')
		echo $params->get('top_text');

// load SPGM's slideshow javascript

echo '<script language="JavaScript1.2" src="components/com_spgm/spgm/spgm.js" type="text/javascript"></script>';

// get the current site language and set it as a constant before loading spgm.conf
// then in spgm.conf, we can say: $spgm_cfg['conf']['language'] = LANG_AUTO;

$langObj = &JFactory::getLanguage();
$lang = $langObj->get('tag');
$lang = substr($lang,0,2);
if ($lang == '')
	$lang = 'en';
define("LANG_AUTO", $lang);

// define other constants used in spgm.conf

define('GALICON_NONE', 0);
define('GALICON_RANDOM', 1);
define('ORIGINAL_SIZE', 0);
define('ORIENTATION_TOPBOTTOM', 0);
define('ORIENTATION_LEFTRIGHT', 1);
define('SORTTYPE_CREATION_DATE', 0);
define('SORTTYPE_NAME', 1);
define('SORT_ASCENDING', 0);
define('SORT_DESCENDING', 1);
define('RIGHT', 0);
define('BOTTOM', 1);

// load the spgm configuration file

include_once("components/com_spgm/spgm/gal/spgm.conf");

// if Exif data is specified in spgm.conf, load overlib.js

if (isset($spgm_cfg['conf']['exifInfo']))
	if (extension_loaded('exif'))
		echo '<script language="JavaScript1.2" src="components/com_spgm/spgm/contrib/overlib410/overlib.js" type="text/javascript"></script>';
		
// Override some constants before loading the main gallery code

define("DIR_SPGM", 		"components/com_spgm/spgm/");
define("DIR_GAL", 		"components/com_spgm/spgm/gal/");
define("DIR_LANG", 		"components/com_spgm/spgm/lang/");
define("DIR_THEMES", 	"components/com_spgm/spgm/flavors/");
define("WEB_SITE", JURI::root());			// Base path of site on the web

// include the original SPGM front end code

echo '<center>';
include_once("components/com_spgm/spgm/spgm.php");
echo '</center>';

// now that SPGM has determined which theme is in use, we can add the stylesheet for it
// Joomla! will include it in the document <head> section as it builds the page
// the one restriction is that we assume the css is called "spgm_style.css"

$themeDir = $spgm_cfg['conf']['theme'];
$document = & JFactory::getDocument();
$document->addStyleSheet( 'components/com_spgm/spgm/flavors/'.$themeDir.'/spgm_style.css');

if ($params->get('bottom_text') != '')
	echo $params->get('bottom_text');
	
?>
