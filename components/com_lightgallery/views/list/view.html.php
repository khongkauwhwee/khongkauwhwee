<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_2
 * @license    GNU/GPL
	*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */

class LightGalleryViewList extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$db			=	& JFactory::getDBO();
 
 		$cid	= JRequest::getVar('cid', '', 'get', 'int');
		if($cid <= 0 || trim($cid) == ''){
			$db =	& JFactory::getDBO();
			$query = "SELECT id FROM #__lightgallery_category WHERE published = '1' ORDER BY ordering ASC";
			$db->setQuery( $query );
			$cid =  $db->loadResult();
 		}
		// Push a model into the view
		$model				= &$this->getModel();
		$modelCategoryList	= &$this->getModel( 'list' );
		
		
		$catlists = $modelCategoryList->fncGetCategoryImageList($cid);
  		
  		$this->assignRef('catlists'  , $catlists);
		parent::display($tpl);

	}
 	
	// function for getTotal
	function getTotalImages($catId)
	{
		$db =	& JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__lightgallery_images WHERE published = '1' AND category_id  = '".$catId."'";
		$db->setQuery( $query );
		$id =  $db->loadResult();
		return ($id);
	}
	
	// function for getTotal
	function getCategory($catId)
	{
		$db =	& JFactory::getDBO();
		$query = "SELECT category FROM #__lightgallery_category WHERE published = '1' AND id  = '".$catId."'";
		$db->setQuery( $query );
		$id =  $db->loadResult();
		return ($id);
	}
  		
 }
?>