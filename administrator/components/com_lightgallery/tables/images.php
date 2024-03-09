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
class TableImages extends JTable
{
	/*  Primary Key *  * @var int  */
	var $id = null;
	
	/*  @var string  */
	var $title = null;

	/*  @var string  */
	var $desc = null;

	/* @var string */
	var $createdby = null;
	
	/* @var string */
	var $image = null;
	
	/* @var string */
	var $createdon = null;
	
 	/* @var Date time */
	var $updatedby = null;

	/* @var date time */
	var $updatedon = null;
 	
	/* @var string */
	var $ordering = null;

	/* @var int */
	var $category_id = null;

	/* @var int */
	var $checked_out = null;
	
	/* @var int */
	var $checked_out_time = null;
	
	/* @var int */
	var $hits = null;
	
	/* String */
	var $published = null;
  
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableImages(& $db) {
		parent::__construct('#__lightgallery_images', 'id', $db);
	}
}
?>