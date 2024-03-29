<?php
/**
* @version		$Id: router.php 10752 2008-08-23 01:53:31Z eddieajau $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

function LightGalleryBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['view']))
	{
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		} else {
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem( $query['Itemid'] );
			if(!isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view']) {
				$segments[] = $query['view'];
			}
		}
		unset($query['view']);
	}
	
	if(isset($query['cid'])) {
 		if(!empty($query['Itemid'])) {
			$segments[] = $query['cid'];
			unset($query['cid']);
		}
	};
	
	return $segments;
}

function LightGalleryParseRoute($segments)
{
	$vars = array();

	$count = count($segments);
	if(!empty($count)) {
		$vars['view'] = $segments[0];
	}
 	if($count > 1) {
		$vars['cid']    = $segments[$count - 1];
	}

	return $vars;
}
?>