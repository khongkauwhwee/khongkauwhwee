<?php
/*
 **********************************************
 JCal Pro v1.5.3
 Copyright (c) 2006-2010 Anything-Digital.com
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

 $File: cal_search.php - Calendar search$

 Revision date: 03/08/2007

 **********************************************
 Get the latest version of JCal Pro at:
 http://dev.anything-digital.com//
 **********************************************
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

global $mainframe, $CONFIG_EXT;

$acl = &JFactory::getACL();
$my = &JFactory::getUser();
$db = & JFactory::getDBO();

require_once( JPATH_BASE."/components/com_jcalpro/config.inc.php" );

// Modified for Mambo integration. No longer a standalone page; used as an include file in the main file extcalendar.php

$num_rows = 0;

$extcal_search  = JRequest::getString( 'extcal_search');
$extcal_search_clean = $db->Quote( '%'.$db->getEscaped( $extcal_search, true ).'%', false );

if (strlen($extcal_search) >= 3) {
  // must be longer or equal to 3 characters !

  if($CONFIG_EXT['archive']) {
    $query = "SELECT extid,title,e.description,e.recur_type,url,cat,cat_name,e.start_date, color, c.cat_id FROM ".$CONFIG_EXT['TABLE_EVENTS']." AS e LEFT JOIN ".$CONFIG_EXT['TABLE_CATEGORIES']." AS c ON e.cat=c.cat_id ";
    $query .= 'WHERE (title LIKE ' . $extcal_search_clean . ' OR e.description LIKE ' . $extcal_search_clean . ') AND c.published = \'1\' AND approved = \'1\' ';
    $query .= "ORDER BY e.start_date";
  } else {
    $day_pattern = sprintf("%04d%02d%02d",$today['year'],$today['month'],1); // day pattern: 20041231 for 'December 31, 2004'
    $query = "SELECT extid,title,e.description,e.recur_type,url,cat,cat_name,e.start_date, color, c.cat_id FROM ".$CONFIG_EXT['TABLE_EVENTS']." AS e LEFT JOIN ".$CONFIG_EXT['TABLE_CATEGORIES']." AS c ON e.cat=c.cat_id ";
    $query .= 'WHERE (title LIKE ' . $extcal_search_clean . ' OR e.description LIKE ' . $extcal_search_clean . ') AND c.published = \'1\' AND approved = \'1\' ';
    $query .= "AND (DATE_FORMAT(e.start_date,'%Y%m%d') >= $day_pattern OR DATE_FORMAT(e.end_date,'%Y%m%d') >= $day_pattern) ";
    $query .= "ORDER BY e.start_date";
  }

  $db->setQuery( $query);
  $rows = $db->loadObjectList();

  $count = 0;
  $search_results = array();

  if (!empty( $rows)) {
    foreach( $rows as $row) {
      if ( has_priv ( 'category' . $row->cat ) ) {
        $title = format_text($row->title,false,$CONFIG_EXT['capitalize_event_titles']);
        $search_results[$count]['search_title'] = highlight($extcal_search,$title,"<span class='titlehighlight'>","</span>");
         
        # popup or not ?
        if ($CONFIG_EXT['popup_event_mode']){
          $non_sef_href = "index.php?option=" . $option . $Itemid_Querystring ."&amp;extmode=view&amp;extid=".$row->extid. '&amp;tmpl=component';
          $search_results[$count]['search_link'] = 'href="'.JRoute::_($non_sef_href).'" class="jcal_modal" rel="{handler: \'iframe\'}" ';

        } else {
          $sef_href = JRoute::_( $CONFIG_EXT['calendar_calling_page']."&amp;extmode=view&amp;extid=".$row->extid );
          $search_results[$count]['search_link'] = "href='$sef_href'";
        }

        $description = process_content(format_text(sub_string($row->description,100,"..."),false,false));

        $search_results[$count]['search_desc'] = highlight($extcal_search,$description,"<span class='highlight'>","</span>");
         
        $search_results[$count]['cat_id'] = $row->cat;
        $search_results[$count]['cat_name'] = $row->cat_name;
        $search_results[$count]['date'] = jcUTCDateToFormat( $row->start_date, $lang_date_format['day_month_year'] );
        $count++;
         
        $num_rows = $count;
      }
    }
  }

}

theme_search_results($search_results, $num_rows);
?>
