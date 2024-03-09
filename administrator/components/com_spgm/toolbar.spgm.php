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

require_once( JApplicationHelper::getPath('toolbar_html'));

switch ($task)
{
	case 'new_dir_form':
		TOOLBAR_spgm::_NEW_DIR();
		break;

	case 'rename_dir_form':
		TOOLBAR_spgm::_REN_DIR();
		break;

	case 'rename_file_form':
		TOOLBAR_spgm::_REN_FILE();
		break;

	case 'new_textfile_form':
		TOOLBAR_spgm::_NEW_TEXT();
		break;
	
	case 'edit_text_form':
		TOOLBAR_spgm::_EDIT_TEXT();
		break;
		
	case 'edit_conf_form':
		TOOLBAR_spgm::_EDIT_CONF();
		break;

	case 'upload_form':
		TOOLBAR_spgm::_UPLOAD();
		break;

	case 'help':
		TOOLBAR_spgm::_HELP();
		break;

	default:
		TOOLBAR_spgm::_DEFAULT();
		break;
}

?>