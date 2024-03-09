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

class LightGalleryViewCategory extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$db			=	& JFactory::getDBO();
		$user				= JFactory::getUser();
		// Push a model into the view
		$model				= &$this->getModel();
		$modelCategoryList	= &$this->getModel( 'category' );
		
		$catlists = $modelCategoryList->fncGetCategoryList();
  		
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
	function getCatImage($catId)
	{
		$db =	& JFactory::getDBO();
		$query = "SELECT image FROM #__lightgallery_images WHERE published = '1' AND category_id  = '".$catId."' ORDER BY RAND()";
		$db->setQuery( $query );
		$id =  $db->loadResult();
		return ($id);
	}
  		
 }
?>