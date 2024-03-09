<?php
/**
 * com_lightgallery table class
 * Component Name 	: 	com_lightgallery
 * Created On 		:	June 06, 2009
 * Author			:	Aneesh s(aneesh@aarthikaindia.com) 
 * @package    		:	com_lightgallery
 * @subpackage 		:	Components
 * @license			:	GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * com_lightgallery Table class
 * @subpackage Components
 */
class TableCategory extends JTable
{
	/*  Primary Key *  * @var int  */
	var $id = null;
	
	/*  @var int  */
	var $category = null;
	
	/* @var int */
	var $createdby = null;
	
	/* @var Datetime */
	var $createdon = null;
	
	/* @var Date time */
	var $updatedon = null;

	/* @var Date time */
	var $updatedby = null;

	/* @var int */
	var $checked_out = null;
 	
	/* @var date time */
	var $checked_out_time = null;

	/* @var int */
	var $ordering = null;

	/* @var int */
	var $hits = null;
	
	/* @var tinyint */
	var $published = null;

   
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableCategory(& $db) {
		parent::__construct('#__lightgallery_category', 'id', $db);
	}
}
?>