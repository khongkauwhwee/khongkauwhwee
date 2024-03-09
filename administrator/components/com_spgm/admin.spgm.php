<?php
/********************************************************************
Copyright 2009-2010 Chris Gaebler
Date    :   20 March 2010
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

define("LA_COMPONENT_VERSION", "2.07");
define("LA_COMPONENT", "com_spgm");
define("LA_COMPONENT_NAME", "SPGM");
define("LA_COMPONENT_LINK", "index.php?option=".LA_COMPONENT);

$webPath = JURI::root();						// Base path of site on the web
if ($webPath[strlen($webPath)-1] != '/')
	$webPath = $webPath.'/';					// $webPath now always ends with /

$serverPath = JPATH_SITE;						// Base path of site on the server
if ($serverPath[strlen($serverPath)-1] != '/')
	$serverPath = $serverPath.'/';				// $serverPath now always ends with /

define ("THUMBNAIL_H_SIZE", 100);
define ("THUMBNAIL_V_SIZE", 100);
define ("RESIZE_QUALITY", 75);					// % quality
define ("THUMBNAIL_PREFIX", "_thb_");
define ("THUMBNAIL_PREFIX_LEN", strlen(THUMBNAIL_PREFIX));
define ("SPGM_CONF", "spgm.conf");
define ("MAX_TEXT_EDIT", 20000);
define ("GALLERY_BASE_DIR", $serverPath."components/com_spgm/spgm/gal/");
define ("GALLERY_WEB_BASE_DIR", $webPath."components/com_spgm/spgm/gal/");
define ("ADMIN_WEB_IMAGE_DIR", "components/com_spgm/images/");
define ("ADMIN_BASE_IMAGE_DIR", $serverPath."administrator/components/com_spgm/images/");
define ("DELETE_NON_EMPTY_DIRECTORIES", false);
define ("FILE_TYPE_OTHER", 0);
define ("FILE_TYPE_GIF", 1);
define ("FILE_TYPE_JPG", 2);
define ("FILE_TYPE_PNG", 3);
define ("FILE_TYPE_TEXT", -1);
define ("FILE_TYPE_CONF", -2);
define ("FILE_TYPE_THUMBNAIL", -3);
define ("FILE_TYPE_ORPHAN_THUMBNAIL", -4);		// Thumbnail with no large image
define ("MAX_POPUP_X", 640);
define ("MAX_POPUP_Y", 480);
define ("STYLE_CONFIG_TABLE", 'class="paramlist admintable" cellspacing="1"');
define ("STYLE_CONFIG_KEY", 'class="paramlist_description" style="width: 500px;"');
	
require_once(JApplicationHelper::getPath('admin_html'));

$dir = stripslashes(urldecode(JRequest::getString('dir')));// dir is always relative to gallery base
$obj_name = stripslashes(urldecode(JRequest::getString('obj_name')));	// file or directory to operate on
$app = &JFactory::getApplication();

switch (JRequest::getCmd('task'))
	{
	case 'up':
		$newDir = getParentDirectory($dir);
		$app->redirect('index.php?option=com_spgm&dir='.$newDir);
		break;
		
	case 'delete_dir':
		if (deleteDir($dir,$obj_name))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'delete_file':
		if (deleteFile($dir, $obj_name))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'build':
		if (buildThumbs($dir))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;

	case 'new_dir_form':
		showHeading($dir);
		showCreateDirForm($dir);
		showDirList($dir);
		break;
		
	case 'rename_file_form':
		showHeading($dir);
		showRenameForm($dir,$obj_name);
		break;
		
	case 'rename_dir_form':
		showHeading($dir);
		showRenameForm($dir,$obj_name);
		showDirList($dir);
		break;
		
	case 'rename_dir':
		$new_name = stripslashes(urldecode(JRequest::getString('new_name')));
		if (renameDir($dir,$obj_name,$new_name))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'rename_file':
		$new_name = stripslashes(urldecode(JRequest::getString('new_name')));
		if (renameFile($dir,$obj_name,$new_name))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'create_dir':
		if (createDir($dir,$obj_name))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'upload_form':
		showHeading($dir);
		showUploadForm($dir);
		break;
		
	case 'save_uploads':
		$resize = JRequest::getString('resize');
		$x_size = JRequest::getString('x_size');
		$y_size = JRequest::getString('y_size');
		if (saveUploadedFiles($dir,$resize,$x_size,$y_size))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'edit_text_form':
		showHeading($dir);
		showTextEditor($dir,$obj_name);
		break;

	case 'save_text';
		$contents = $_POST['contents']; 	// we need it raw here
		if (saveTextFile($dir,$obj_name,$contents,false))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
			
	case 'new_textfile_form':
		showHeading($dir);
		showCreateTextFileForm($dir);
		break;
		
	case 'edit_conf_form':
		showHeading($dir);
		showConfEditor($dir);
		break;
		
	case 'save_conf':
		if (saveConfFile($dir))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'create_file':
		$contents = JRequest::getString('contents');
		if (saveTextFile($dir,$obj_name,$contents,true))
			$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
		
	case 'cancel':
		$app->redirect('index.php?option=com_spgm&dir='.$dir);
		break;
	
	case 'help':
		showHelpScreen();
		break;
	
	default:					// cold entry with no $task
		adminForm($dir);
		showHeading($dir);
		showDirList($dir);
		showThumbs($dir);
	}
?>
