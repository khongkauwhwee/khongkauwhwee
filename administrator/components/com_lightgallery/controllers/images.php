<?php
/**
 * @version		$Id: banner.php 10878 2008-08-30 17:29:13Z willebil $
 * @package		Joomla
 * @subpackage	Banners
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * @package		Joomla
 * @subpackage	Banners
 */
class LightGalleryControllerimages extends JController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add',			'edit' );
		$this->registerTask( 'apply',		'save' );
		$this->registerTask( 'resethits',	'save' );
		$this->registerTask( 'unpublish',	'publish' );
	}
 	
	/**
	 * Display the list of banners
	 */
	function display()
	{
		global $mainframe;

		$db =& JFactory::getDBO();
 			
		$context			= 'com_lightgallery.images.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'b.title',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$category_id		= $mainframe->getUserStateFromRequest( $context.'category_id',		'category_id',		'',			'int' );
		$filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',			'string' );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' ) {
				$where[] = 'b.published = 1';
			}
			else if ($filter_state == 'U' ) {
				$where[] = 'b.published = 0';
			}
		}
		
		if( JRequest::getVar('cad', 0, 'get', 'int') > 0){
			$category_id	= JRequest::getVar('cad', 0, 'get', 'int');
		}
		
		if ( $category_id )
		{
 			$where[] = 'b.category_id ='.$category_id ;
 		}
			


		if ($search) {
			$where[] = 'LOWER(b.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .' OR LOWER(b.desc) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
		$orderby	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', b.title';

		// get the total number of records
		$query = 'SELECT COUNT(*)'
		. ' FROM #__lightgallery_images  AS b'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );

	 	$query = 'SELECT b.*, u.name AS editor, user.name AS postedname'
		. ' FROM #__lightgallery_images  AS b'
		. ' LEFT JOIN #__users AS u ON u.id = b.checked_out'
		. ' JOIN #__users AS user ON user.id = b.createdby'
		. $where
		. $orderby
		;
		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $db->loadObjectList();

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		
		// search filter
		$lists['search']= $search;
		
		// List Category
		$query = 'SELECT c.id AS value, c.category AS text'
		. ' FROM #__lightgallery_category AS c'
		. ' ORDER BY c.ordering'
		;
		$db->setQuery( $query );
 		$categories[]		= JHTML::_('select.option',  '0', JText::_( 'Select a Category' ), 'value', 'text' );
		$categories			= array_merge( $categories, $db->loadObjectList() );
		
		//$category_id	= JRequest::getVar('category_id', '', 'post', 'int');
 		$lists['category_id'] = JHTML::_('select.genericlist',   $categories, 'category_id', 'class="inputbox" onChange="javascript:submitform();" size="1"', 'value', 'text', $category_id );
	
	
		require_once(JPATH_COMPONENT.DS.'views'.DS.'images.php');
		LightGalleryViewimages::LightGalleryImages( $rows, $pageNav, $lists );
	}
	
	function edit()
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		if ($this->_task == 'edit') {
			$cid	= JRequest::getVar('cid', array(0), 'method', 'array');
			$cid	= array((int) $cid[0]);
		} else {
			$cid	= array( 0 );
		}

		$option = JRequest::getCmd('option');
		$lists = array();

		$row =& JTable::getInstance('images', 'Table');

		$row->load( $cid[0] );

		if ($cid[0]) {
			$row->checkout( $user->get('id') );
		} else {
			$row->published = 1;
		}
		
		
 		// Build Client select list
		$sql = 'SELECT * '
		. ' FROM #__lightgallery_images  WHERE id = '.$cid[0];
		$db->setQuery($sql);
		if (!$db->query())
		{
			$this->setRedirect( 'index.php?com_lightgallery&c=images' );
			return JError::raiseWarning( 500, $db->getErrorMsg() );
		}
		
		// List Category
		$query = 'SELECT c.id AS value, c.category AS text'
		. ' FROM #__lightgallery_category AS c'
		. ' ORDER BY c.ordering'
		;
		$db->setQuery( $query );
 		$categories[]		= JHTML::_('select.option',  '0', JText::_( 'Select a Category' ), 'value', 'text' );
		$categories			= @array_merge( $categories, $db->loadObjectList() );
		
		$category_id	= JRequest::getVar('category_id', '', 'post', 'int');
 		$lists['category_id'] = JHTML::_('select.genericlist',   $categories, 'category_id', 'class="inputbox" size="1"', 'value', 'text', $row->category_id );
 	
		require_once(JPATH_COMPONENT.DS.'views'.DS.'images.php');
		LightGalleryViewimages::editimages( $row, $lists );
	}

	/**
	 * Save method
	 */
	function save()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$id	= JRequest::getVar('id', '', 'post', 'int');
		$title	= JRequest::getVar('title', '', 'post', 'string');
		
		$user =& JFactory::getUser();
		
		$this->setRedirect( 'index.php?com_lightgallery&c=images' );
		
		if(trim($title) == ''){
			$msg = JText::_( '&nbsp; Please enter image title.' );
			$link = JRoute::_('index.php?option=com_lightgallery&c=images', false);
			$this->setRedirect( $link, $msg,'error' );  return;
		}
 		
		// Chekc valid file format for Upload
		if($_FILES["image"]["size"]>0 ){
			$path = strtolower(strrchr($_FILES["image"]["name"], '.'));
			if(($path!='.jpeg') && ($path!='.jpg') && ($path!='.gif') && ($path!='.png')){
				$msg = JText::_( '&nbsp; Please attach Image in correct format(.jpg, .gif, .png only).' );
				$link = JRoute::_('index.php?option=com_lightgallery&c=images', false);
				$this->setRedirect( $link, $msg, 'error' );return;
			}
		}else if($id<=0){
			$msg = JText::_( '&nbsp; Please attach an Image.' );
			$link = JRoute::_('index.php?option=com_lightgallery&c=images', false);
			$this->setRedirect( $link, $msg );return;
		}
		
		// Initialize variables
		$db =& JFactory::getDBO();

		$post	= JRequest::get( 'post' );
 		if($id<=0){
 			$post["createdby"] =  $user->get('id');
			$createdon =& JFactory::getDate();
			$post["createdon"] =  $createdon->toMySQL();
		}
		$updatedon =& JFactory::getDate();
		$post["updatedon"] =  $updatedon->toMySQL();
		$post["updatedby"] =  $user->get('id');
		
		$row =& JTable::getInstance('images', 'Table');

 
		if (!$row->bind( $post )) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		// Resets clicks when `Reset Clicks` button is used instead of `Save` button
		$task = JRequest::getCmd( 'task' );
 
		if (!$row->check()) {
			return JError::raiseWarning( 500, $row->getError() );
		}

 		if (!$row->store()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		if(!$id || $id <= 0){
			$id = $db->insertId();
		}
		// Upload Logo -Start Here
		$upload = $this->uploadlogo($id);
		if($upload == 'invalidformat'){
			$msg = JText::_( '&nbsp; Please attach Image in correct format.' );
			$link = JRoute::_('index.php?option=com_lightgallery&c=images', false);
			$this->setRedirect( $link, $msg );return;
		}
		// Upload Logo -End Here
		
		$row->checkin();

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_lightgallery&c=images&task=edit&cid[]='. $row->id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_lightgallery&c=images';
				break;
		}

		$this->setRedirect( $link, JText::_( 'Item Saved' ) );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_lightgallery&c=images' );

		// Initialize variables
		$db		=& JFactory::getDBO();
		$post	= JRequest::get( 'post' );
		$row	=& JTable::getInstance('images', 'Table');
		$row->bind( $post );
		$row->checkin();
	}

	 
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_lightgallery&c=images' );

		// Initialize variables
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__lightgallery_images '
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids.'  )'
		. ' AND ( checked_out = 0 OR ( checked_out = ' .(int) $user->get('id'). ' ) )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $db->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$this->setRedirect( 'index.php?option=com_lightgallery&c=images' );

		$base = "../administrator/components/com_lightgallery/";
		$working_folder =$base."lightgallery/";
		// Initialize variables
		$db		=& JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$n		= count( $cid );
		JArrayHelper::toInteger( $cid );

		if ($n)
		{
			for($i=0; $i < $n; $i++){ 
				$query = "SELECT image FROM #__lightgallery_images WHERE id = ".$cid[$i];
				$db->setQuery( $query );
				$rows = $db->loadObjectList();
				if (file_exists($working_folder."th".$rows[0]->image)) 
				{ 
					@unlink($working_folder."th".$rows[0]->image);
				}
				@unlink($working_folder.$rows[0]->image);
 			}
			$query = 'DELETE FROM #__lightgallery_images'
			. ' WHERE id = ' . implode( ' OR id = ', $cid );
			$db->setQuery( $query );
			if (!$db->query()) {
				JError::raiseWarning( 500, $db->getError() );
			}
		}

		$this->setMessage( JText::sprintf( 'Items removed', $n ) );
	}
	
	/**
	 * Method to Upload image
	 *
	 **/
	function uploadlogo($id)
	{
		$base = "../administrator/components/com_lightgallery/";
		$working_folder =$base."lightgallery/";
		$db =& JFactory::getDBO();
		
 		if($_FILES["image"]["size"] > 0 ){

			$path = strtolower(strrchr($_FILES["image"]["name"], '.'));
			if(($path!='.jpeg') && ($path!='.jpg') && ($path!='.gif') && ($path!='.png')){
				return 'invalidformat';
			}
			
			if (file_exists($working_folder."th".$_POST["image_old"])) { 
				if($_POST["image_old"]){
					@unlink ($working_folder."th".$_POST["image_old"]);
				}
			}
			
			$filename = strtolower($this->fncUid().'_'.$_FILES["image"]["name"]); 
  			if(copy($_FILES["image"]["tmp_name"], $working_folder.$filename)){
				$query = "UPDATE #__lightgallery_images SET image = '".$filename."' WHERE id = '".$id."'";
				$db->setQuery( $query );
			 	if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
				$source=$working_folder.$filename;
				$dest=$working_folder."th".$filename;
				$this->imageCompression($source,150,$dest);
				$this->imageCompression($source,500,$source);
				#@unlink( $source );
			}
		}
	}
	
	/**
	 * Generate Random ID
	 *
	 **/
	 function fncUid(){
		return sprintf(
				 '%08x-%04x-%04x-%02x%02x-%012x', mt_rand(), mt_rand(0, 65535),	 bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)),
				 bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)), mt_rand(0, 255), mt_rand() );
	}

	function ErrorImage ($text) {
		global $maxw;
		$len 							= 	strlen($text);
		if($maxw < 100) $errw = 100;
		$errh 							= 	25;
		$chrlen 						= 	intval (4 * $len);
		$offset 						= 	intval (($errw - $chrlen) / 2);
		$im 							= 	imagecreate ($errw, $errh); /* Create a blank image */
		$bgc 							= 	imagecolorallocate ($im, 153, 63, 63);
		$tc 							= 	imagecolorallocate ($im, 255, 255, 255);
		imagefilledrectangle ($im, 0, 0, $errw, $errh, $bgc);
		imagestring ($im, 2, $offset, 7, $text, $tc);
		imagejpeg ($im);
		imagedestroy ($im);
		exit;
	}
	## Thumbnail Creation [With Ratio]
	function imageCompression($imgfile="",$thumbsize=0,$savePath) {
		$this->thumbnail(2, $imgfile, $savePath, $thumbsize, $thumbsize);
		/*
		if($savePath==NULL) {
				header('Content-type: image/jpeg');
		}
		list($width,$height)=getimagesize($imgfile);
		$imgratio=$width/$height; 
		if($imgratio>1) {
				$newwidth=$thumbsize;
				$newheight=$thumbsize/$imgratio;
		} else {
				$newheight=$thumbsize;       
				$newwidth=$thumbsize*$imgratio;
		}
		$thumb=imagecreatetruecolor($newwidth,$newheight); // Making a new true color image

		$source=imagecreatefromjpeg($imgfile); // Now it will create a new image from the source
		imagecopyresampled($thumb,$source,0,0,0,0,$newwidth,$newheight,$width,$height);  // Copy and resize the image
		imagejpeg($thumb,$savePath,100);
		imagedestroy($thumb);
		*/
	}
	function thumbnail ($gdver, $src, $newFile1, $maxw='' ,$maxh='') {
		$gdarr 	= 	array (1,2);
		for ($i=0; $i<count($gdarr); $i++) {
			if ($gdver != $gdarr[$i]) $test.="|";
		}
		$exp 							= 	explode ("|", $test);
		if (count ($exp) == 3) {
			$this->ErrorImage ("Incorrect GD version!");
		}
		if (!function_exists ("imagecreate") || !function_exists ("imagecreatetruecolor")) {
			$this->ErrorImage ("No image create functions!");
		}
		$size = @getimagesize ($src);
		if (!$size) {
			$this->ErrorImage ("Image File Not Found!");
		} else {
			
			if($size[0] > $maxw){
			
				$imgratio= $size[0]/$size[1]; 
				if($imgratio>1) {
						$newx=$maxw;
						$newy=$maxw/$imgratio;
				} else {
						$newy=$maxw;       
						$newx=$maxw*$imgratio;
				}
			}
 			if ($gdver == 1) {
				$destimg =  imagecreate ($newx, $newy );
			} else {
				$destimg = @imagecreatetruecolor ($newx, $newy ) or die ($this->ErrorImage ("Cannot use GD2 here!"));
			}
			if ($size[2] == 1) {
				if (!function_exists ("imagecreatefromgif")) {
					ErrorImage ("Cannot Handle GIF Format!");
				} else {
					$sourceimg = imagecreatefromgif ($src);
					if ($gdver == 1)
						imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
					else
						@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));
					imagegif ($destimg, $newFile1);
				}
			}elseif ($size[2]==2) {
				$sourceimg = imagecreatefromjpeg ($src);
				if ($gdver == 1)
					imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
				else
					@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));
				imagejpeg ($destimg, $newFile1);
				
			}elseif ($size[2] == 3) {
				$sourceimg = imagecreatefrompng ($src);
				if ($gdver == 1)
					imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
				else
					@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));
				imagepng ($destimg, $newFile1);
			}else {
				ErrorImage ("Image Type Not Handled!");
			}
		}
		imagedestroy ($destimg);
		imagedestroy ($sourceimg);
	}
 }
?>