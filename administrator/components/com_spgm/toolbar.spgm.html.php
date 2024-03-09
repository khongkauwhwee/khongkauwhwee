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

class TOOLBAR_spgm
{
	function _NEW_DIR() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_NEW_DIR').']</small></small>', 'cpanel' );
		JToolBarHelper::save('create_dir');
		JToolBarHelper::cancel();
	}
	
	function _REN_DIR() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_RENAME').']</small></small>', 'cpanel' );
		JToolBarHelper::save('rename_dir');
		JToolBarHelper::cancel();
	}
	
	function _REN_FILE() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_RENAME').']</small></small>', 'cpanel' );
		JToolBarHelper::save('rename_file');
		JToolBarHelper::cancel();
	}
	
	function _NEW_TEXT() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_NEW_TEXT_FILE').']</small></small>', 'cpanel' );
		JToolBarHelper::save('create_file');
		JToolBarHelper::cancel();
	}
	
	function _EDIT_TEXT() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_TEXT_FILE').']</small></small>', 'cpanel' );
		JToolBarHelper::save('save_text');
		JToolBarHelper::cancel();
	}
	
	function _EDIT_CONF() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_CONFIG_FILE').']</small></small>', 'cpanel' );
		JToolBarHelper::save('save_conf');
		JToolBarHelper::cancel();
	}
	
	function _UPLOAD() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('TXT_UPLOAD').']</small></small>', 'cpanel' );
		JToolBarHelper::cancel();
	}
	
	function _HELP() 
	{
		JToolBarHelper::title('SPGM: <small><small>['.JText::_('HELP').']</small></small>', 'cpanel' );
		JToolBarHelper::cancel();
	}
	
	function _DEFAULT() 
	{
		JToolBarHelper::title('SPGM Admin', 'cpanel' );
		JToolBarHelper::custom('new_textfile_form', 'publish.png', 'publish_f2.png', JText::_('TXT_NEW_TEXT_FILE'), false);
		JToolBarHelper::custom('new_dir_form', 'new.png', 'new_f2.png', JText::_('TXT_NEW_DIR'), false);
		JToolBarHelper::custom('upload_form', 'upload.png', 'upload_f2.png', JText::_('TXT_UPLOAD'), false);
		JToolBarHelper::custom('build', 'apply.png', 'apply_f2.png', JText::_('TXT_BUILD_THUMB'), false);
		JToolBarHelper::custom('help', 'help.png', 'help_f2.png', JText::_('HELP'), false);
	}
}
?>
