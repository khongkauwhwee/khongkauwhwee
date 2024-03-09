<?php
/**
 * @version		$Id: admin.lightgallery.php 10381 2009-06-06 03:35:53Z Aneesh $
 * @package		Joomla
 * @subpackage	com_lightgallery
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure the user is authorized to view this page
$user = & JFactory::getUser();
if (!$user->authorize( 'com_lightgallery', 'manage' )) {
	#$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_lightgallery'.DS.'tables');

$controllerName = JRequest::getCmd( 'c', 'category' );

if($controllerName == 'category') {
	JSubMenuHelper::addEntry(JText::_('LightGallery Category'), '', true );
	JSubMenuHelper::addEntry(JText::_('LightGallery Images'), 'index.php?option=com_lightgallery&c=images');
}
if($controllerName == 'images') {
	JSubMenuHelper::addEntry(JText::_('LightGallery Category'), 'index.php?option=com_lightgallery' );
	JSubMenuHelper::addEntry(JText::_('LightGallery Images'),'', true);
}

switch ($controllerName)
{
	default:
		$controllerName = 'category';
		// allow fall through

	case 'category' :
	case 'images':
 		// Temporary interceptor
		$task = JRequest::getCmd('task');
		if ($task == 'listcategory') {
			$controllerName = 'category';
		}
		if ($task == 'listimages') {
			$controllerName = 'images';
		}
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
		$controllerName = 'LightGalleryController'.$controllerName;

		// Create the controller
		$controller = new $controllerName();

		// Perform the Request task
		$controller->execute( JRequest::getCmd('task') );

		// Redirect if set by the controller
		$controller->redirect();
		break;
}






echo '<div align="center" class="developedby"><a href="http://www.dev.aarthikaindia.com" target="_blank">Developed By Aneesh S, R&amp;D, aarthika Technologies(P) Ltd.</a></div>';
?>