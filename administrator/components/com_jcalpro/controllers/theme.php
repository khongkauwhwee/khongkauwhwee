<?php
/*
 **********************************************
 JCal Pro
 Copyright (c) 2006-2009 Anything-Digital.com
 **********************************************
 JCal Pro is a fork of the existing Extcalendar component for Joomla!
 (com_extcal_0_9_2_RC4.zip from mamboguru.com).
 Extcal (http://sourceforge.net/projects/extcal) was renamed
 and adapted to become a Mambo/Joomla! component by
 Matthew Friedman, and further modified by David McKinnis
 (mamboguru.com) to repair some security holes.

 This file is based on admin.mambots.php v329 (2005-10-02) by stingrey.

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This header must not be removed. Additional contributions/changes
 may be added to this header as long as no information is deleted.
 **********************************************

 $Id: theme.php 326 2009-06-09 21:59:13Z shumisha $

 **********************************************
 Get the latest version of JCal Pro at:
 http://dev.anything-digital.com//
 **********************************************
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.controller' );

class JCalProControllerTheme extends JController {

  function access_list( $row )
  {
    $access_list = array(
    mosHTML::makeOption( '18','Registered' ),
    mosHTML::makeOption( '19','-Author' ),
    mosHTML::makeOption( '20','--Editor' ),
    mosHTML::makeOption( '21','---Publisher' ),
    mosHTML::makeOption( '23','----Manager' ),
    mosHTML::makeOption( '24','-----Administrator' ),
    mosHTML::makeOption( '25','------Super Administrator' )
    );

    $lists['access'] = JHTML::_('select.genericlist',  $access_list, 'access', 'class="inputbox" size="1"', 'value', 'text', $row->access );

    return $lists['access'];

  }
  /**
   * Compiles a list of installed or defined modules
   */
  function viewThemes( $option, $client ) {
    global $mainframe;

    $database =& JFactory::getDBO();
    $mosConfig_list_limit = $mainframe->getCfg('list_limit');

    $limit             = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
    $limitstart     = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
    $filter_type    = $mainframe->getUserStateFromRequest( "filter_type{$option}{$client}", 'filter_type', 1 );
    $filter_order_Dir    = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',    'filter_order_Dir',    '',    'word' );
    $filter_order        = $mainframe->getUserStateFromRequest( $context.'filter_order',        'filter_order',        '',    'cmd' );
    $search         = $mainframe->getUserStateFromRequest( "search{$option}{$client}", 'search', '' );
    $search         = $database->getEscaped( JString::trim( JString::strtolower( $search ) ) );

    if ($client == 'admin') {
      $where[] = "m.client_id = '1'";
      $client_id = 1;
    } else {
      $where[] = "m.client_id = '0'";
      $client_id = 0;
    }

    // used by filter
    if ( $filter_type != 1 ) {
      $where[] = "m.type = '$filter_type'";
    }
    if ( $search ) {
      $where[] = "LOWER( m.name ) LIKE '%$search%'";
    }

    // get the total number of records
    $query = "SELECT COUNT(*)"
    . "\n FROM #__jcalpro2_themes AS m"
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
    ;
    $database->setQuery( $query );
    $total = $database->loadResult();

    jimport('joomla.html.pagination');
    $pageNav = new JPagination($total, $limitstart, $limit);

    $query = "SELECT m.*, u.name AS editor"
    . "\n FROM #__jcalpro2_themes AS m"
    . "\n LEFT JOIN #__users AS u ON u.id = m.checked_out"
    //. "\n LEFT JOIN #__groups AS g ON g.id = m.access"
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
    . "\n GROUP BY m.id"
    . "\n ORDER BY m.type ASC, m.name ASC"
    . "\n LIMIT $pageNav->limitstart, $pageNav->limit"
    ;
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
    if ($database->getErrorNum()) {
      echo $database->stderr();
      return false;
    }

    // get list of Positions for dropdown filter
    $query = "SELECT type AS value, type AS text"
    . "\n FROM #__jcalpro2_themes"
    . "\n WHERE client_id = '$client_id'"
    . "\n GROUP BY type"
    . "\n ORDER BY type"
    ;
    //$types[] = mosHTML::makeOption( 1, _SEL_TYPE );
    $types[] = JHTML::_( 'select.option', 1, _SEL_TYPE );
    $database->setQuery( $query );
    $types             = array_merge( $types, $database->loadObjectList() );
    //$lists['type']    = mosHTML::selectList( $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_type );
    $lists['type']      = JHTML::_('select.genericlist',  $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_type );

    // table ordering
    $lists['order_Dir']    = $filter_order_Dir;
    $lists['order']        = $filter_order;

    require_once( JPATH_COMPONENT.DS.'themes'.DS.'themes.html.php' );

    EXTCAL_themes::showThemes( $rows, $client, $pageNav, $option, $lists, $search );

  }

  function defaultTemplate ( )
  {
    global $mainframe;
    $database = & JFactory::getDBO();

    $query = "UPDATE
                #__jcalpro2_themes
            SET 
                published = '0'
            ";

    $database->setQuery( $query );
    $database->query();

    echo mysql_error ( );

    $cid = JRequest::getVar( 'cid', '', 'POST', 'array');
    $query = "UPDATE
                #__jcalpro2_themes
            SET 
                published = '1' 
            WHERE
                name = '" . $database->Quote( $cid['0']) . "'
            ";

    $database->setQuery( $query );
    $database->query();

    $mainframe->redirect('index.php?option=com_jcalpro&task=showthemes');
  }

  /**
   * Saves the module after an edit form submit
   */
  function saveThemes( $option, $client, $task ) {
    global $mainframe;
    $database = & JFactory::getDBO();

    $params = JRequest::getVar( 'params', '', $_POST );
    if (is_array( $params )) {
      $txt = array();
      foreach ($params as $k=>$v) {
        $txt[] = "$k=$v";
      }

      $_POST['params'] = mosParameters::textareaHandling( $txt );
    }
    require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
    $row = new extcalthemes( $database );
    if (!$row->bind( $_POST )) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if (!$row->check()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    $row->checkin();
    if ($client == 'admin') {
      $where = "client_id='1'";
    } else {
      $where = "client_id='0'";
    }
    $row->updateOrder( "type = '$row->type' AND ordering > -10000 AND ordering < 10000 AND ( $where )" );

    switch ( $task ) {
      case 'apply':
        $msg = 'Successfully Saved changes to Theme: '. $row->name;
        $mainframe->redirect( 'index.php?option='. $option .'&client='. $client .'&task=edittheme&hidemainmenu=1&id='. $row->id, $msg );
        break;
      case 'save':
      default:
        $msg = 'Successfully Saved Theme: '. $row->name;
        $mainframe->redirect( 'index.php?option='. $option .'&client='. $client .'&task=showthemes', $msg );
        break;
    }
  }

  /**
   * Compiles information to add or edit a module
   * @param string The current GET/POST option
   * @param integer The unique id of the record to edit
   */
  function editThemes( $option, $uid, $client ) {
    global $mainframe;
    $database = & JFactory::getDBO();
    $my = & JFactory::getUser();

    $lists     = array();
    require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
    $row     = new extcalthemes($database);

    // load the row from the db table
    $row->load( $uid );

    // fail if checked out not by 'me'
    if ($row->isCheckedOut( $my->id )) {
      echo "<script>alert('The module $row->title is currently being edited by another administrator'); document.location.href='index.php?option=$option'</script>\n";
      exit(0);
    }

    if ($client == 'admin') {
      $where = "client_id='1'";
    } else {
      $where = "client_id='0'";
    }

    // get list of groups
    if ($row->access == 99 || $row->client_id == 1) {
      $lists['access'] = 'Administrator<input type="hidden" name="access" value="99" />';
    } else {
      // build the html select list for the group access
      //$lists['access'] = mosAdminMenus::Access( $row );
      $lists['access'] = access_list( $row );
    }

    if ($uid) {
      $row->checkout( $my->id );

      $query = "SELECT ordering AS value, name AS text"
      . "\n FROM #__jcalpro2_themes"
      . "\n WHERE theme = '$row->theme'"
      . "\n AND published > 0"
      . "\n AND type = 'theme'"
      . "\n ORDER BY theme"
      ;

      $lists['theme'] = '<input type="hidden" name="theme" value="'. $row->theme .'" />'. $row->theme;

      // XML library
      require_once( JPATH_ROOT . DS .'includes'.DS.'domit'.DS.'xml_domit_lite_include.php' );
      // xml file for module
      $xmlfile = JPATH_ROOT . DS .'plugins'.DS.'editors'.DS.'extcal'.DS.'jscripts'.DS.'tiny_mce'.DS.'themes'. DS .$row->theme . DS . $row->theme .'.xml';
      $xmlDoc = new DOMIT_Lite_Document();
      $xmlDoc->resolveErrors( true );
      if ($xmlDoc->loadXML( $xmlfile, false, true )) {
        $root = &$xmlDoc->documentElement;
        if ($root->getTagName() == 'mosinstall' && $root->getAttribute( 'type' ) == 'extcaltheme' ) {
          $element = &$root->getElementsByPath( 'description', 1 );
          $row->description = $element ? JString::trim( $element->getText() ) : '';

        }
      }
    } else {
      $row->theme         = '';
      $row->type          = 'theme';
      $row->icon             = '';
      $row->published     = 1;
      $row->description     = '';
      $row->access        = 18;
      $row->editable      = 1;

      $folders = JFolder::files( JPATH_ROOT . DS .'plugins'.DS.'editors'.DS.'extcal'.DS.'jscripts'.DS.'tiny_mce'.DS.'themes'.DS );
      $folders2 = array();
      foreach ($folders as $folder) {
        if (is_dir( JPATH_ROOT . DS . 'plugins'.DS.'editors'.DS.'extcal'.DS.'jscripts'.DS.'tiny_mce'.DS.'themes' . DS . $folder ) && ( $folder <> 'CVS' ) ) {
          //$folders2[] = mosHTML::makeOption( $folder );
          $folders2[] = JHTML::_('select.option', $folder, $folder, 'value', 'text');
        }
      }
      //$lists['folder'] = mosHTML::selectList( $folders2, 'folder', 'class="inputbox" size="1"', 'value', 'text', null );
      $lists['folder'] = JHTML::_('select.genericlist',  $folders2, 'folder', 'class="inputbox" size="1"', 'value', 'text', null);
      $xmlfile = JPATH_ROOT . DS . 'plugins'.DS.'editors'.DS.'extcal'.DS.'jscripts'.DS.'tiny_mce'.DS.'themes' . DS .$row->theme . DS . $row->theme .'.xml';
    }

    //$lists['published'] = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );
    $lists['published'] = JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $row->published );

    // get params definitions
    //$params = new mosParameters( $row->params, $xmlfile, 'extcaltheme' );
    $params = new JParameter( $row->params, $xmlfile);
    require_once( JPATH_COMPONENT.DS.'themes'.DS.'themes.html.php' );
    EXTCAL_themes::editThemes( $row, $lists, $params, $option );
  }
  /**
   * Saves Access Levels for themes
   * @param array An array of unique category id numbers
   */
  function applyAccess( $cid=null, $option, $client ) {
    global $mainframe;

    $database = & JFactory::getDBO();
    $my = & JFactory::getUser();

    $access = JRequest::getVar( 'access', '' );

    if (count( $cid ) < 1) {
      echo "<script> alert('Select a theme to set Access Level for'); window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode( ',', $cid );

    if( $access != '' ){
      $query = "UPDATE #__jcalpro2_themes SET access = '". intval( $access ) ."'"
      . "\n WHERE id IN ( $cids )"
      . "\n AND ( checked_out = 0 OR ( checked_out = $my->id ))"
      ;
      $database->setQuery( $query );
      if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
      }

      if (count( $cid ) == 1) {
        require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
        $row = new extcalthemes( $database );
        $row->checkin( $cid[0] );
      }
    }

    $mainframe->redirect( 'index.php?option='. $option .'&client='. $client .'&task=showthemes' );
  }
  /**
   * Publishes or Unpublishes one or more modules
   * @param array An array of unique category id numbers
   * @param integer 0 if unpublishing, 1 if publishing
   */
  function publishThemes( $cid=null, $publish=1, $option, $client ) {
    global $mainframe;

    $database = & JFactory::getDBO();
    $my = & JFactory::getUser();

    if (count( $cid ) < 1) {
      $action = $publish ? 'publish' : 'unpublish';
      echo "<script> alert('Select a theme to $action'); window.history.go(-1);</script>\n";
      exit;
    }

    $cids = implode( ',', $cid );

    $query = "UPDATE #__jcalpro2_themes SET published = '". intval( $publish ) ."'"
    . "\n WHERE id IN ( $cids )"
    . "\n AND ( checked_out = 0 OR ( checked_out = $my->id ))"
    ;
    $database->setQuery( $query );
    if (!$database->query()) {
      echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
      exit();
    }

    if (count( $cid ) == 1) {
      require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
      $row = new extcalthemes( $database );
      $row->checkin( $cid[0] );
    }

    $mainframe->redirect( 'index.php?option='. $option .'&client='. $client .'&task=showthemes' );
  }

  /**
   * Cancels an edit operation
   */
  function cancelEdit( $option, $client ) {
    global $mainframe;

    $database = & JFactory::getDBO();

    require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
    $row = new extcalthemes( $database );
    $row->bind( $_POST );
    $row->checkin();

    $mainframe->redirect( 'index.php?option='. $option .'&client='. $client .'&task=showthemes' );
  }
  function cancelThemes( $option, $client ) {
    global $mainframe;

    $database = & JFactory::getDBO();

    require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
    $row = new extcalthemes( $database );
    $row->bind( $_POST );
    $row->checkin();

    $mainframe->redirect( 'index.php?option='. $option );
  }
  /**
   * Remove the selected language
   */
  function removeTheme( $cid, $option, $client ) {
    global $mainframe;

    $mainframe->redirect( 'index.php?option=com_jcalpro&task=remove&element=themes&client='. $client .'&cid[]='. $cid );
  }
  /**
   * changes the access level of a record
   * @param integer The increment to reorder by
   */
  function accessMenu( $uid, $access, $option ) {
    global $mainframe;
    $database = & JFactory::getDBO();

    switch( $access ){
      case 'access_registered':
        $access_id = 18;
        break;
      case 'access_author':
        $access_id = 19;
        break;
      case 'access_editor':
        $access_id = 20;
        break;
      case 'access_publisher':
        $access_id = 21;
        break;
      case 'access_manager':
        $access_id = 23;
        break;
      case 'access_administrator':
        $access_id = 24;
        break;
      case 'access_superadministrator':
        $access_id = 25;
        break;
    }
    require_once( JPATH_COMPONENT . DS . 'themes'.DS.'themes.class.php' );
    $row = new extcalthemes( $database );
    $row->load( $uid );

    $row->access = intval( $access_id );

    if ( !$row->check() ) {
      return $row->getError();
    }
    if ( !$row->store() ) {
      return $row->getError();
    }
    $mainframe->redirect( 'index.php?option='. $option .'&task=showthemes' );
  }
}
?>