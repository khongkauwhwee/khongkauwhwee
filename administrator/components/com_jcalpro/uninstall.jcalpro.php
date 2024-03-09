<?php
/*
 **********************************************
 Copyright (c) 2006-2009 Anything-Digital.com
 **********************************************
 JCal Pro is a fork of the existing Extcalendar component for Joomla!
 (com_extcal_0_9_2_RC4.zip from mamboguru.com).
 Extcal (http://sourceforge.net/projects/extcal) was renamed
 and adapted to become a Mambo/Joomla! component by
 Matthew Friedman, and further modified by David McKinnis
 (mamboguru.com) to repair some security holes.

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This header must not be removed. Additional contributions/changes
 may be added to this header as long as no information is deleted.
 **********************************************

 $File: uninstall.jcalpro.php - uninstall file$

 Revision date: 02/20/2007

 **********************************************
 Get the latest version of JCal Pro at:
 http://dev.anything-digital.com//
 **********************************************
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.filesystem.folder');

/**
 * Writes an extension parameter to a disk file, located
 * in the /media directory
 *
 * @param string $extName the extension name
 * @param array $shConfig associative array of parameters of the extension, to be written to disk
 * @param array $pub, optional, only if module, an array of the menu item id where the module is published
 * @return boolean, true if no error
 */
function shWriteExtensionConfig( $extName, $params) {

  if (empty($params)) {
    return;
  }

  // calculate target file name
  $extFile = JPATH_ROOT.DS.'media'.DS.'jCalPro_upgrade_conf'.DS. $extName .'_'
  .str_replace('/','_',str_replace('http://', '', JURI::base())).'.php';

  // remove previous if any
  if (JFile::exists( $extFile)) {
    JFile::delete( $extFile);
  }

  // prepare data for writing
  $data = '<?php // Extension params save file for JCal Pro
//    
if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');';
  $data .= "\n";

  if (!empty( $params)) {
    foreach( $params as $key => $value) {
      $data .= '$'. $key . ' = ' . var_export($value, true) . ';';
      $data .= "\n";
    }
  }

  // write to disk
  $success = JFile::write( $extFile, $data);

  return $success !== false;
}

/**
 * Save parameters, then delete a module
 *
 * @param string $modName, the module name (matching 'module' column in modules table
 */
function shSaveDeleteModuleParams( $modName) {

  $db = & JFactory::getDBO();
  $modIdsList = array();

  // read module param from db
  $sql = 'SELECT * FROM `#__modules` WHERE `module`= \''.$modName.'\';';
  $db->setQuery($sql);
  $result = $db->loadAssocList();

  if (!empty( $result)) {
    // delete previously saved params, in case a module
    // instance has been deleted
    $baseFolder = JPATH_ROOT.DS.'media'.DS.'jCalPro_upgrade_conf';
    $baseName = $modName . '_[0-9]{1,10}'.'_'.str_replace('/','_',str_replace('http://', '', JURI::base())).'.php';
	if (JFolder::exists( $baseFolder)) {
    $fileList = JFolder::files( $baseFolder, $baseName);
    if (!empty( $fileList)) {
      foreach( $fileList as $fileName) {
        JFile::delete( $baseFolder . DS . $fileName);
      }
    }
}

    // save data for all modules
    foreach( $result as $mod) {
      $modId = $mod['id'];
      unset($mod['id']); // we don't want to save DB id

      // find pages the module is published on
      $sql = "SELECT menuid FROM #__modules_menu WHERE moduleid = " . (int) $modId;
      $db->setQuery( $sql );
      $rows = $db->loadResultArray();
      $pub = array();
      // $pub contains page where the module is published
      if (!empty( $rows)) {
        foreach($rows as $menuid) {
          $pub[] = $menuid;
          $modIdsList[] = $menuid;
        }
      }
      // write everything on disk
      shWriteExtensionConfig( $modName .'_' .  $modId, array('shConfig'=> $mod, 'shPub' => $pub));
    }
  }

  // now remove module details from db
  // note : though we have saved params for only one instance of the module
  // we of course delete all instances from the db
  $db->setQuery( "DELETE FROM `#__modules` WHERE `module`= '" . $modName . "';");
  $db->query();

  // now remove also module publication pages (for all modules involved
  if (!empty( $modIdsList)) {
    $sql='DELETE FROM #__modules_menu WHERE moduleid in (' . implode( ',', $modIdsList) . ')';
    $db->setQuery( $sql );
    $db->query( $sql );
  }

  // delete the module files, and its subdir
  $basePath = JPATH_ROOT . DS . 'modules'. DS . $modName . DS;
  JFile::delete( array( $basePath . $modName . '.php', $basePath . $modName . '.xml'));
  if ($modName != '') {
    // protect agains deleting the whole module folder !
    JFolder::delete( $basePath);
  }
}

/**
 * Save parameters, then delete a plugin
 *
 * @param string $pluginName, the plugin name, mathcing 'element' column in plugins table
 * @param string $folder, the plugin folder (ie : 'content', 'search', 'system',...
 */
function shSaveDeletePluginParams( $pluginName, $folder, $folders) {

  $db = & JFactory::getDBO();

  // read plugin param from db
  $sql = 'SELECT * FROM `#__plugins` WHERE `element`= \''.$pluginName.'\';';
  $db->setQuery($sql);
  $result = $db->loadAssocList();

  if (!empty( $result)) {
    // remove plugin db id
    unset($result[0]['id']);

    // write everything on disk
    shWriteExtensionConfig( $pluginName, array('shConfig' => $result[0]));

    // now remove plugin details from db
    $db->setQuery( "DELETE FROM `#__plugins` WHERE `element`= '" . $pluginName . "';");
    $db->query();
  }

  // delete the plugin files
  $basePath = JPATH_ROOT.DS.'plugins'. DS . $folder . DS;
  if ($folder != '' && JFile::exists($basePath . $pluginName.'.php')) {
    JFile::delete( array( $basePath . $pluginName.'.php', $basePath . $pluginName.'.xml'));
  }

  // delete plugin additional folders
  if (!empty( $folders)) {
    foreach ($folders as $aFolder) {
      JFolder::delete( $basePath . $aFolder);
    }
  }
}

/**
 * Save parameters, then delete a Community builder plugin
 *
 * @param string $pluginName, the plugin name, mathcing 'element' column in plugins table
 */
function shSaveDeleteCBPluginParams( $pluginName) {

  $db = & JFactory::getDBO();

  // check if CB is still installed, in case user removed it in the mean time
  $sql = 'SHOW TABLES LIKE "%_comprofiler_%"';
  $db->setQuery($sql);
  $hasCB = $db->loadResult();

  if (!$hasCB) {
    return;
  }
  // read plugin param from db
  $sql = 'SELECT * FROM `#__comprofiler_plugin` WHERE `element`= \''.$pluginName.'\';';
  $db->setQuery($sql);
  $pluginParams = $db->loadAssocList();
   
  // save plugin params
  if (!empty( $pluginParams)) {
    // remove plugin db id
    $pluginId = $pluginParams[0]['id'];
    unset($pluginParams[0]['id']);
  } else {
    $pluginId = 0;
  }

  // read tab param from db
  $sql = 'SELECT * FROM `#__comprofiler_tabs` WHERE `pluginid`= \''.$pluginId.'\';';
  $db->setQuery($sql);
  $tabParams = $db->loadAssocList();

  if (!empty( $tabParams)) {
    // remove plugin db id
    unset($tabParams[0]['id']);
    // write everything on disk
    shWriteExtensionConfig( $pluginName, array('shPlugin' => $pluginParams[0], 'shTab' => $tabParams[0]));

    // now remove plugin details from db
    $db->setQuery( "DELETE FROM `#__comprofiler_plugin` WHERE `element`= '" . $pluginName . "';");
    $db->query();
    $db->setQuery( "DELETE FROM `#__comprofiler_tabs` WHERE `pluginid`= " . $pluginId . ";");
    $db->query();
  }

  // delete the plugin files
  $basePath = JPATH_ROOT . DS . 'components' . DS . 'com_comprofiler' . DS . 'plugin' . DS . 'user' . DS . 'plug_' . $pluginName . DS;
  if (JFolder::exists($basePath)) {
    JFolder::delete( $basePath);
  }
}

function com_uninstall() {

  jimport('joomla.filesystem.file');
  jimport('joomla.filesystem.folder');

  // save and remove latest events module details
  shSaveDeleteModuleParams( 'mod_jcalpro_latest_J15');
  // save and remove minical module details
  shSaveDeleteModuleParams( 'mod_jcalpro_minical_J15');

  // save and remove latest events plugins
  $folders = array();
  shSaveDeletePluginParams( 'bot_jcalpro_latest_events', 'content', $folders);
  // save and remove search plugin
  shSaveDeletePluginParams( 'bot_jcalpro_search', 'search', $folders);

  // save and remove Community builder plugins
  shSaveDeleteCBPluginParams( 'cbjcalproevents');
  shSaveDeleteCBPluginParams( 'cbjcalprominical');

  // save and remove Jomsocial builder plugins
  shSaveDeletePluginParams( 'jsjcalproevents', 'community', $folders);
  shSaveDeletePluginParams( 'jsjcalprominical', 'community', $folders);

  // save and remove JCal pro libraries plugins
  $folders = array( 'jcl.shhttpcomm');
  shSaveDeletePluginParams( 'jcllibraries', 'system', $folders);
  $folders = array( ('jcl.recaptcha'));
  shSaveDeletePluginParams( 'jclrecaptcha', 'jcalpro', $folders);

  echo 'JCal pro has been uninstalled. Content of database tables has been left untouched, in order to allow for transparent upgrade.';
}

?>
