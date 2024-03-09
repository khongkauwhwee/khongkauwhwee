<?php
/**
 * @version		$Id: events.php 573 2010-02-09 09:39:03Z shumisha $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Main events model
 *
 */
class JcalproModelEvents extends JModel {

  /**
   * Events total
   *
   * @var integer
   */
  private $_total = null;

  // the various parameters needed to fetch events from DB
  private $_params = null;

  /**
   * Constructor set default values for some parameters
   * @return none
   */
  public function __construct() {

    $this->_params->calId = 0;
    $this->_params->catId = 0;
    $this->_params->calList = '';
    $this->_params->catList = '';
    $this->_params->privateEventsMode = null;
    $this->_params->showRecurringEvents = JCL_SHOW_RECURRING_EVENTS_ALL;
    $this->_params->lastUpdated = false;
    $this->_params->multiDayEvents = null;
    $this->_params->maxNumberOfEvents = 5;
    $this->_params->orderDir = 'ASC';
    $this->_params->dispatchByDay = true;
    $this->_params->profiledUserId = 0;
  }

  /**
   * Inject private properties into the object
   *
   * @param properties an array holding the names/values pairs
   * @return none
   */
  public function injectProperties( $properties) {

    if (!empty( $properties)) {
      foreach( $properties as $name => $value) {
        $this->_params->$name = $value;
      }
    }
  }

  /**
   * Method to get events for rss feeds requests
   *
   * @access public
   * @return array
   */
  public function getFeedsEvents() {

    global $CONFIG_EXT;

    if ($CONFIG_EXT['enable_feeds']) {
      $events = $this->_getFeedsEventList();
    } else {
      $events = null;
    }

    return $events;
  }

  /**
   * Method to get the total number of content items for the frontpage
   *
   * @access public
   * @return integer
   */
  public function getTotal() {
    // Lets load the content if it doesn't already exist
    if (empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }

  /**
   * Get current request calendar information
   * @TODO : move to helper, or to its own model
   * @TODO : inject properties instead of use Jrequest
   * @return object
   */
  public function getCalendar() {

    global $mainframe;

    // get current page parameters
    $pageParams = & $mainframe->getPageParameters( 'com_jcalpro');
    $pageCalList = $pageParams->get( 'cal_list');
    $calList = jclValidateList( $pageCalList);
    // cal_id in request overrides the rest
    $reqCalId = JRequest::getInt( 'cal_id', null);

    // if only one calendar, get its id and title
    $id = empty( $reqCalId) ? intval( $calList) : $reqCalId;
    $cal = new stdClass();
    $cal->id = $id;
    $cal->title = empty( $id) ? '' : jclGetCalendarName( $id);

    // @TODO if more than one calendar selected, we should build the title string also

    return $cal;
  }

  /**
   * Get current request category information
   * @TODO : move to helper, or to its own model
   * @TODO : inject properties instead of use Jrequest
   * @return object
   */
  public function getCategory() {

    global $mainframe;

    // get current page parameters
    $pageParams = & $mainframe->getPageParameters( 'com_jcalpro');
    $pageCatList = $pageParams->get( 'cat_list');
    $catList = jclValidateList( $pageCatList);
    // cat_id in request overrides the rest
    $reqCatId = JRequest::getInt( 'cat_id', null);

    // if only one calendar, get its id and title
    $catId = empty( $reqCatId) ? intval( $catList) : $reqCatId;
    $cat = new stdClass();
    $cat->id = $catId;
    $cat->title = empty( $catId) ? '' : jclGetCategoryName( $catId);

    // @TODO if more than one calendar selected, we should build the title string also

    return $cat;
  }

  /**
   * Get all events from db, in a time range, complying with calendars, categories and permissions restrictions
   * Date selection is perfomed based on the last_updated column
   *
   * @param $first_date UTC DB format date  of the first day in range
   * @param $last_date UTC DB format date   of the last day in range
   * @return array an array of event objects, possibly empty, false if bad parameters
   */
  public function getEvents( $firstDate = null, $lastDate = null, $dispatchByDay = null) {

    // cache as many requests as we can
    static $resultCache = null;

    // return events that occur at a specific date WARNING: this timestamp must be based on a UTC time.
    // events are stored as UTC times, so a local date may actually span over 2 days
    // get_events does the conversion
    global $CONFIG_EXT, $today, $mainframe;
    /*@comment_do_not_remove@*/
    $db = &JFactory::getDBO();

    // preliminary checks

    // we were asked to return 0 events!
    if (isset( $this->_params->maxNumberOfEvents) && $this->_params->maxNumberOfEvents < 1) {
      return false;
    }

    // check params, and store them if needed
    if(empty($firstDate)) {
      $firstDate = empty( $this->_params->dateBoundaries->start) ? JCL_DATE_MIN : $this->_params->dateBoundaries->start;
    } else {
      $this->_params->dateBoundaries->start = $firstDate;
    }
    if(empty( $lastDate)) {
      $lastDate = empty( $this->_params->dateBoundaries->end) ? JCL_DATE_MAX : $this->_params->dateBoundaries->end;
    } else {
      $this->_params->dateBoundaries->end = $lastDate;
    }

    if (is_null( $dispatchByDay)) {
      $dispatchByDay = $this->_params->dispatchByDay;
    } else {
      $this->_params->dispatchByDay = $dispatchByDay;
    }

    // validate categories and calendars
    if (!empty( $this->_params->catId) && !has_priv ( 'category' . $this->_params->catId)) {
      // user not allowed to see this cat
      return false;
    }
    if (empty( $this->_params->catList)) {
      // if list of cats is empty, we read all cats
      $this->_params->catList = jclGetCategories();
    }
    $this->_params->catList = jclValidateAndCheckAuthList( $this->_params->catList, 'category');
    if (empty( $this->_params->catList)) {
      // not allowed to see any cat in the current category list
      return false;
    }

    if (!empty( $this->_params->calId) && !has_priv ( 'calendar' . $this->_params->calId)) {
      // use not allowed to see this calendar
      return false;
    }
    if (empty( $this->_params->calList)) {
      $this->_params->calList = jclGetCalendars();
    }
    $this->_params->calList = jclValidateAndCheckAuthList( $this->_params->calList, 'calendar');
    if (empty( $this->_params->calList)) {
      // not allowed to see any calendar in the current calendar list
      return false;
    }

    // now all cat or cal selectors are validated against user authorizations

    // unix timestamps of start and end dates
    $startTS = jcUTCDateToTs( $firstDate);
    $endTS = jcUTCDateToTs( $lastDate);

    // do not display past events
    $todayUserTime = jcUserTimeToUTC( 0, 0, 0, $today['month'], $today['day'], $today['year']);
    $todayTS = jcUTCDateToTs($todayUserTime);
    if( !$CONFIG_EXT['archive'] && $startTS < $todayTS) {
      $first_date = $todayUserTime;
      // update timestamp
      $startTS = $todayTS;
    }

    // generate the sql query for a specific date
    $event_condition = '';

    // convert to mysql date time, for query
    $startOfFirstDaySql = $db->Quote( $firstDate);
    $endOfLastDaySql = $db->Quote( $lastDate);

    // are we trying to fetch events based on last update date ?
    if ($this->_params->lastUpdated) {
      $event_condition .= (empty( $event_condition) ? '' : ' AND ') . " e.last_updated >= $startOfFirstDaySql AND e.last_updated <= $endOfLastDaySql";
    } else {
      // conditions on date of the event
      $event_condition  = "(    ( e.start_date <= $startOfFirstDaySql AND e.end_date >= $endOfLastDaySql"
      ." AND e.end_date != " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE)
      ." AND e.end_date != " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE_LEGACY)
      ." AND e.end_date != " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE_LEGACY_2)
      . ") ";
      $event_condition .= " OR ((e.start_date >= $startOfFirstDaySql AND e.start_date < $endOfLastDaySql) "
      ." AND (e.end_date = " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE)
      ." OR e.end_date = " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE_LEGACY)
      ." OR e.end_date = " . $db->Quote( JCL_ALL_DAY_EVENT_END_DATE_LEGACY_2)
      . ")) ";
      $event_condition .= "  OR ( e.start_date > $startOfFirstDaySql AND e.start_date <= $endOfLastDaySql)";
      $event_condition .= "  OR ( e.end_date > $startOfFirstDaySql AND e.end_date <= $endOfLastDaySql ) )";

    }

    // apply settings on showing reccuring events
    if ($this->_params->showRecurringEvents == JCL_SHOW_RECURRING_EVENTS_FIRST_ONLY) {
      // if only show the first occurence in a series :
      $event_condition .= (empty( $event_condition) ? '' : ' AND ') . ' e.rec_id = ' . $db->Quote('0');
    }


    // read from db - remember to select also primary key, or else Joomfish won't translate
    $query = "SELECT e.*, c.cat_id, cal.cal_id, cal.cal_name, c.cat_name, c.color, c.description as cat_desc from " . $CONFIG_EXT['TABLE_EVENTS'] . ' AS e'
    . " LEFT JOIN " . $CONFIG_EXT['TABLE_CATEGORIES'] . " AS c ON e.cat=c.cat_id "
    . " LEFT JOIN " . $CONFIG_EXT['TABLE_CALENDARS'] . " AS cal ON e.cal_id=cal.cal_id ";
    $query .= "WHERE ".$event_condition." AND c.published = '1' AND e.published = '1' AND cal.published='1' AND approved = '1' ";

    // apply category restrictions
    if(!empty($this->_params->catId) && is_numeric($this->_params->catId)) {
      $query .= "AND e.cat = ".$db->Quote( $this->_params->catId) . ' ';
    } else if (!empty( $this->_params->catList)) {
      // if not a specific cat requested, apply categories list restrictions
      $query .= "AND e.cat IN (" . $this->_params->catList . ") ";
    }

    // apply calendar restrictions
    if (!empty( $this->_params->calId)) {
      $query .= "AND e.cal_id = ".$db->Quote( $this->_params->calId) . ' ';
    } else if (!empty( $this->_params->calList)) {
      // if not a specific cal requested, apply calendars list restrictions
      $query .= "AND e.cal_id IN (" . $this->_params->calList . ") ";
    }

    // apply private event restrictions
    $user = &JFactory::getUser();
    switch ($this->_params->privateEventsMode) {
      case JCL_DO_NOT_SHOW_PRIVATE_EVENTS :
        $query .= "AND e.private = '" . JCL_EVENT_PUBLIC . "' ";
        break;
      case  JCL_SHOW_ONLY_PRIVATE_EVENTS :
        $query .= "AND e.owner_id = " . $db->Quote( $user->id) . ' AND e.private in ( \'' . JCL_EVENT_PRIVATE . '\', \'' . JCL_EVENT_PRIVATE_READ_ONLY . '\')  ';
        break;
      case  JCL_SHOW_ONLY_OWN_EVENTS :
        // if we are looking at the profile of another user than the one logged in
        if (!empty( $profiledUserId)) {
          // make sure we display on the tab only those events that belong to that particular user
          $query .= ' AND ( (e.owner_id=' . $db->Quote( $this->_params->profiledUserId) . ' AND e.private in ( \'' . JCL_EVENT_PRIVATE . '\', \'' . JCL_EVENT_PRIVATE_READ_ONLY . '\'))  '
          . 'OR (e.owner_id=' . $db->Quote( $this->_params->profiledUserId) . ' AND '. $db->Quote( $this->_params->profiledUserId) .'!='. $db->Quote( $user->id) . ' AND e.private in ( \'' . JCL_EVENT_PRIVATE_READ_ONLY . '\', \'' . JCL_EVENT_PUBLIC . '\') ' . ')'
          . '  )';
        } else {
          // possibly any user looking, so need to show private events only to their owner
          $query .= "AND e.owner_id = " . $db->Quote( $user->id);
        }
        break;
      default:
        $query .= "AND (e.private in ('".JCL_EVENT_PUBLIC ."', '".JCL_EVENT_PRIVATE_READ_ONLY."') "
        . "OR (e.owner_id = " . $db->Quote( $user->id) . " AND e.private = '". JCL_EVENT_PRIVATE ."')) ";
        break;
    }

    // apply recurrency restrictions
    if ($this->_params->showRecurringEvents == JCL_SHOW_RECURRING_EVENTS_NONE) {
      $query .= "AND e.rec_type_select = '". JCL_REC_TYPE_NONE ."' ";
    }

    // finalize query
    $query .= 'ORDER BY start_date ' . $this->_params->orderDir . ',title' ;

    // query the db if no cache hit
    $queryId = md5( $query);
    if (empty( $resultCache) || !isset( $resultCache[$queryId])) {
      $db->setQuery( $query);
      $events = $db->loadObjectList();
      jclKeepOnlyRecurNext( $events, $endOfLastDaySql);
      $resultCache[$queryId] = $events;
    }

    $eventsRead = $resultCache[$queryId];

    // build record for returning results
    $events = array();
    if (!empty( $eventsRead)) {
      $recIdsList = array();
      foreach( $eventsRead as $event) {
        $events[] = array( $event->extid, jcUTCDateToTs( $event->start_date), jcUTCDateToTs($event->end_date), $event);
      }
    }

    // we have an array with all events in the range.
    // We must show multidays events settings by discarding some events if
    // we are not supposed to display them
    // optionnally, we can dispatch events by the day they appear on
    // like : $returnEvents[12] will be an array with all events appearing on the 12 of (a) month
    // WARNING: this works only if the requested date range does not span over more than a month
    // this will not work otherwise if events are dispatched by day, as events on, say,
    // march 12 will be grouped with events on april 16
    // note that if searching by last update date, we don't apply event date restrictions
    $returnEvents = false;
    $eventsCount = 0;
    foreach( $events as $event) {
      if ($eventsCount >= $this->_params->maxNumberOfEvents) {
        // end when we have what we need
        break;
      }
      if (jclIsAllDay( $event[3]->end_date) || jclIsNoEndDate( $event[3]->end_date)) {
        // all-day and no-end date events : special case
        $endOfDayTS = startOfDayInUserTime( $event[1]) + 86399;  // end of day where start of event falls
        if ($endOfDayTS >= $startTS || $lastUpdated) {
          if ($dispatchByDay) {
            $day = intval(jcUTCDateToFormat( $event[1], '%d'));
            $returnEvents[$day][] = $event;
          } else {
            $returnEvents[] = $event;
          }
          $eventsCount++;
        }
      } else {
        // apply multi_days settings
        switch ($this->_params->multiDayEvents) {
          case 'start':
            // show only start day
            $endOfDayTS = startOfDayInUserTime( $event[1]) + 86399;  // end of day where start of event falls
            if ($endOfDayTS >= $startTS || $this->_params->lastUpdated) {
              $this->_storeEventInList( $returnEvents, $event[1], $event);
              $eventsCount++;
            }
            break;
          case 'bounds':
            // show start and end day of events
            $startOfDayStartDay = startOfDayInUserTime( $event[1]);
            $endOfDayTS =  $startOfDayStartDay + 86399;  // end of day where start of event falls
            if (($endOfDayTS >= $startTS && $endOfDayTS <= $endTS) || $this->_params->lastUpdated) {
              $this->_storeEventInList( $returnEvents, $event[1], $event);
              $eventsCount++;
            }
            // show end date, but only if we are not on the same day
            // as beginning, or else event will show twice
            $startOfDayTS = startOfDayInUserTime( $event[2]);
            if ($startOfDayStartDay != $startOfDayTS) {
              if (($startOfDayTS >= $startTS && $startOfDayTS <= $endTS) || $this->_params->lastUpdated) {
                $this->_storeEventInList( $returnEvents, $event[2], $event);
                $eventsCount++;
              }
            }
            break;
          default:
            // show all days of events, no restriction
            $startOfDayTS = startOfDayInUserTime( $event[1]);  // start of day where start of event falls
            while ( $startOfDayTS <= $event[2]) {  // until we're past end of event
              if ($eventsCount >= $this->_params->maxNumberOfEvents) {
                // end when we have what we need
                break;
              }
              $endOfDayTS = $startOfDayTS + 86399; // end of day where start of event falls
              if ($this->_params->lastUpdated || (($endOfDayTS >= $startTS && $endOfDayTS <= $endTS) || ($startOfDayTS >= $startTS && $startOfDayTS <= $endTS))) {  // either of those fall within the time range we are displaying
                $this->_storeEventInList( $returnEvents, $startOfDayTS, $event);
                $eventsCount++;
                if (!$this->_params->dispatchByDay) {
                  // if we don't display events split by day, then we only include
                  // a repeat event once, even if it spans over several days
                  break;
                }
              }
              // move 24 hrs forward
              $startOfDayTS += 86400;
            }

            break;
        }
      }
    }

    return $returnEvents;
  }

  /**
   * Store an event, starting on a given timestamp
   * either directly in the target event array, or
   * indexed on the event day number, depending on the
   * value of a parameter
   *
   * @param $eventList
   * @param $dayTimestamp
   * @param $event
   * @return none
   */
  private function _storeEventInList( & $eventList, $dayTimestamp, $event) {

    if ($this->_params->dispatchByDay) {
      $day = intval(jcUTCDateToFormat( $dayTimestamp, '%d'));
      $eventList[$day][] = $event;
    } else {
      $eventList[] = $event;
    }
  }

  private function _getFeedsEventList($date = '')	{

    // function to display monthly events
    global $CONFIG_EXT, $today;

    if (!$CONFIG_EXT['enable_feeds']) {
      return null;
    }

    // Check date. if no date is passed as argument, then we pick today
    if (empty($date)) {
      $day = $today['day'];
      $month = $today['month'];
      $year = $today['year'];
    } else {
      $day = $date['day'];
      $month = $date['month'];
      $year = $date['year'];
    }

    // get all events in one query
    $dateString = jcUserTimeToUTC( 0,0,0,$month,$day,$year) ;  // we must use user time, not UTC
    $dateStamp = jcUTCDateToTs( $dateString) - JCL_FEEDS_DAYS_TO_INCLUDE_IN_FEED * 86400;
    $dateOfFirstDay = jcUTCDateToFormat( $dateStamp, '%Y-%m-%d %H:%M:%S');
    $dateOfLastDay = jcUserTimeToUTC( 23,59,59,$month,$day,$year);
    $properties = array('showRecurringEvents' => $CONFIG_EXT['show_recurrent_events'], 'lastUpdated' => true);
    $this->injectProperties( $properties);
    $eventListByDay = $this->getEvents( $dateOfFirstDay, $dateOfLastDay);

    // events are indexed by day, also, there is a full record with more details than we need
    // must remove that and provide a simple list
    $returnEvents = false;
    if (!empty($eventListByDay)) {
      foreach($eventListByDay as $day => $eventListForADay) {
        if (!empty( $eventListForADay)) {
          foreach( $eventListForADay as $event) {
            // adjust start_date for repeating events, when not on start day
            $eventStartDay = jcUTCDateToFormat( $event[3]->start_date, '%d');
            if ( $day != $eventStartDay) {
              // we are on another day than start day of the event
              // get current month/year
              $curMonth = jcUTCDateToFormat( $event[3]->start_date, '%m');
              $curYear = jcUTCDateToFormat( $event[3]->start_date, '%Y');
              $eventCurDateTS = jcUserTimeToUTC( 0,0,0,$curMonth,$day, $curYear);
              $event[3]->start_date = jcUTCDateToFormat( $eventCurDateTS, '%Y-%m-%d %H:%M:%S');
            }
            $returnEvents[] = clone($event[3]);
          }
        }
      }
    }

    return is_array($returnEvents)? $returnEvents : false;

  }

}
