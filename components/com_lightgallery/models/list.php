<?php
/** 
 * @package    Joomla
 * @subpackage Components
 * @license    GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Hello Model
 *
 * @package    Joomla
 * @subpackage Components
 */
class LightGalleryModelList extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Gets the Company Profile
	 * @return string The greeting to be displayed to the user
	 */
	function fncGetCategoryImageList($category_id)
	{
		global $mainframe, $option;
		
  		$db		=& JFactory::getDBO();				
		$query	= "SELECT * FROM #__lightgallery_images WHERE published = '1' AND category_id = '".$category_id."' ORDER BY ordering ASC";
		$result = $this->_getList( $query );
		return @$result;
	}
 }
?>