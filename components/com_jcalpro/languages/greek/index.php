<?php
	,'any_calendar' => 'Show all calendars'
	// new JCalpro 2
	,'repeat_event_detached' => 'This event was part of a repetition series, but has been modified and separated from it'
	,'repeat_event_detached_short' => 'Detached from recurrence'
	,'repeat_event_not_detached' => 'This event is part of a repetition series'
	,'repeat_edit_parent_event' => 'Edit parent event'
	,'deleted_child_events' => 'Deleted %d previous instances'
	,'created_child_events' => 'Created a total of %d repetitions of event %s. View this event by <a href="%s" >clicking here</a>.'  // Jcal Pro 2.1.x
	,'no_recur_end_date' => 'A recurring event should have an end-date or a number of occurences'
	// new JCalpro 2
	,'failed_existing_event_update' => 'Database error during update of event %s (%d)'
	,'failed_child_event_deletion' => 'Database error deleting children of event %s (%d)'
	,'failed_child_event_creation' => 'Database error creating children of event %s (%d)'
	,'no_calendar' => 'You must select a calendar from the drop down menu !'
	,'event_cal' => 'Calendar'
	,'private_event' => 'Private event'
	,'private_event_read_only' => 'Private event, others can read'
	,'public_event' => 'Public event'
	,'privacy' => 'Privacy'
	,'failed_event_creation' => 'Database error while trying to create this event'
	,'no_cats_to_delete' => 'There is no category left to delete'

	// JCAL pro 2
	// ======================================================
	// admin_calendars
	// ======================================================

	if (defined('ADMIN_CALS_PHP'))

	$lang_cal_admin_data = array(
	'section_title' => 'Calendars Administration',
	'add_cal' => 'Add New Calendar',
	'edit_cal' => 'Edit Calendar',
	'update_cal' => 'Update Calendar Info',
	'delete_cal' => 'Delete Calendar',
	'events_label' => 'Events',
	'visibility' => 'Visibility',
	'actions_label' => 'Actions',
	'users_label' => 'Users',
	'admins_label' => 'Admins',
	// General Info
	'general_info_label' => 'General Information',
	'cal_name' => 'Calendar Name',
	'cal_desc' => 'Calendar Description',
	'status_label' => 'Status',
	'calendar_label' => 'Calendar permissions',
	// Stats
	'stats_string1' => '<strong>%d</strong> calendars',
	'stats_string2' => 'Active: <strong>%d</strong>&nbsp;&nbsp;&nbsp;Total: <strong>%d</strong>&nbsp;&nbsp;&nbsp;on <strong>%d</strong> page(s)',
	// Misc.
	'add_cal_success' => 'New calendar added succesfully',
	'edit_cal_success' => 'Calendar updated succesfully',
	'delete_confirm' => 'Are you sure you want to delete this calendar ?',
	'delete_cal_success' => 'Calendar deleted succesfully',
	'active_label' => 'Active',
	'not_active_label' => 'Not Active',
	// Error messages
	'no_cal_name' => 'You must provide a name for this calendar !',
	'no_cal_desc' => 'You must provide a description for this calendar !',
	'delete_cal_failed' => 'This calendar cannot be deleted',
	'no_cals' => 'There are no calendars to display !',
	'cal_has_events' => 'Calendar #%d contains %d event(s) and therefore cannot be deleted! Please delete remaining events under this calendar and try again!',
	'default' => 'Use default from settings'
	,'no_cals_to_delete' => 'There is no calendar left to delete'
	);
