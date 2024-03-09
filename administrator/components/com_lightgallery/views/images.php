<?php
/**
 * @version		$Id: banner.php 10554 2008-07-15 17:15:19Z ircmaxell $
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

/**
* @package		Joomla
* @subpackage	Banners
*/
class LightGalleryViewimages
{
 	function setLightGalleryImgToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');
		JToolBarHelper::title( JText::_( 'LightGallery Manager' ), 'generic.png' );
 		JToolBarHelper::title( $task == 'add' ? JText::_( 'LightGallery Manager: - Images' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'LightGallery Manager: - Images' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
		JToolBarHelper::addNewX( 'add' );
		JToolBarHelper::editListX( 'edit' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();

		JToolBarHelper::cancel( 'cancel' );
	}
	
	function LightGalleryImages( &$rows, &$pageNav, &$lists )
	{
		LightGalleryViewimages::setLightGalleryImgToolbar();
		$user =& JFactory::getUser();
		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_lightgallery&c=images" method="post" name="adminForm">
		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Filter Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo JText::_( 'Select : ' );
 					echo $lists['category_id'];
				?>
			</td>
			
			<td nowrap="nowrap">
				<?php
 					echo $lists['state'];
				?>
			</td>
			
			
		</tr>
		</table>

			<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="20">
						<input type="checkbox" name="toggle" value=""  onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th nowrap="nowrap" class="title">
						<?php echo JText::_('#'); ?>
					</th>
					<th nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',  'Title', 'b.title', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'Created By', 'b.createdby', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
 					<th width="10%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'Created On', 'b.createdon', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'Updated On', 'b.updatedon', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<!--<th width="5%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',  'Views', 'b.hits', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>-->
					<th width="5%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',  'Order', 'b.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="5%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',  'Published', 'b.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
 				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];

				$row->id	= $row->id;
				$link		= JRoute::_( 'index.php?option=com_lightgallery&c=images&task=edit&cid[]='. $row->id );
 				
 				$row->published = $row->published;
				$published 		= JHTML::_('grid.published', $row, $i );
				$checked		= JHTML::_('grid.checkedout',   $row, $i );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>
					<td align="center"><?php echo $checked;?></td>
					<td align="center" width="10%">
					<?php if($row->image){?>
					<div align="left">
						<img src="<?php echo JURI::base(); ?>components/com_lightgallery/lightgallery/<?php echo "th".$row->image;?>"  border="0" alt="Image" width="100px"  class="clsImgPad" />
					</div>
					<? }else{?>
					<img src="<?php echo JURI::base(); ?>components/com_lightgallery/lightgallery/no_photo_small.gif"  alt="Image" border="1" width="100px"  class="clsImgPad" />
					<?php } ?>

					</td>
					<td valign="top">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo substr(JText::_($row->title),0, 200); ?>...">
						<?php
						if ( JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
							echo $row->title;
						} else {
							?>

							<a href="<?php echo $link; ?>">
								<?php echo $row->title; ?></a>
							<?php
						}
						?>
						</span>
						<div><?php echo $row->desc; ?></a></div>
					</td>
					<td align="center"><?php echo $row->postedname;?></td>
 					<td align="center">
						<?php echo JHTML::_('date',  $row->createdon, JText::_('DATE_FORMAT_LC1')); ?>
					</td>
					<td align="center">
						<?php echo JHTML::_('date',  $row->updatedon, JText::_('DATE_FORMAT_LC1')); ?>
					</td>
					<!--<td align="center">
						<?php echo (int)$row->hits;?>
					</td>-->
					<td align="center">
						<?php echo $row->ordering;?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>
 				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="c" value="images" />
		<input type="hidden" name="option" value="com_lightgallery" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

	function setLightgalleryToolbar()
	{
		$task = JRequest::getVar( 'task', '', 'method', 'string');
 		JToolBarHelper::title( $task == 'add' ? JText::_( 'LightGallery Manager - Images' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'LightGallery Manager - Images' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel( 'cancel' );
	}

	
	/**
	 *function for Edit Posts
	 **/
	function editimages( &$row, &$lists )
	{
 		LightGalleryViewimages::setLightgalleryToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		$user =& JFactory::getUser();
 		?>
  		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.category_id.selectedIndex <= 0) {
				alert( "<?php echo JText::_( 'You must select a category.', true ); ?>" );
			}else if (form.title.value == "") {
				alert( "<?php echo JText::_( 'You must provide image title.', true ); ?>" );
			}
			else{
				submitform( pressbutton );
			}
  		}
 		//-->
		</script>
 		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
		<div class="col100">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Image Details' ); ?></legend>
				<table class="admintable" width="100%" border="0">
				<tbody>
				  <tr>
					<td valign="top" align="left">
						<table>
							<tr>
								<td height="25" valign="top"><?php echo JText::_( 'Select Category' ); ?>:</td>
								<td><div id="clsTableTdPadd"><?php echo $lists['category_id'];?></div></td>
							</tr>
							<tr>
								<td height="25" valign="top" colspan="2"><?php echo JText::_( 'Image Title' ); ?>:
								<div id="clsTableTdPadd"><input type="text" maxlength="250" size="75%"  class="inputbox required" name="title" id="title" value="<?php echo $row->title;?>" /> *</div></td>
							</tr>
							<tr>
								<td height="25" valign="top" colspan="2"><label id="descriptionmsg" for="description"><?php echo JText::_( 'Description' ); ?>:</label>
								<div id="clsTableTdPadd"><input type="text" maxlength="250" size="75%"  class="inputbox" name="desc" id="desc" value="<?php echo $row->desc;?>" /></div></td>
							</tr>
							<tr>
								<td height="25" valign="top"><?php echo JText::_( 'Upload Image' ); ?></td>
								<td valign="top">
								<div id="clsTableTdPadd">
									<input type="file" name="image" id="file-upload" /> (.jpeg, .gif, .png only)
									<input type="hidden" name="image_old" id="image_old" value="<?php echo $row->image;?>" />
								</div>
								</td>
							</tr>
							<tr>
								<td height="25" valign="top"><?php echo JText::_( 'Display Order' ); ?>:</td>
								<td valign="top"><div id="clsTableTdPadd"><input type="text" maxlength="10" size="5"  class="inputbox" name="ordering" id="ordering" value="<?php echo $row->ordering;?>" /></div></td>
							</tr>
							<tr>
								<td height="25" valign="top" colspan="2" class="clsTDBorderTop"><label id="publishedmsg" for="published"><?php echo JText::_( 'Publish?' ); ?>:</label>
								<? print $lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"  size="1"', ( isset($row->published)) ? $row->published : 1); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="25" align="left" class="clsTDBorderTop">
									<?php echo JText::_( '* Required' ); ?>
								</td>
							</tr>
 						</table>
					</td>
					<td valign="top" align="left" colspan="2">
						<div id="clsTableTdPadd">
							<?php 
 							if($row->image){?>
 							<div align="left">
								<img src="<?php echo JURI::base(); ?>components/com_lightgallery/lightgallery/<?php echo "th".$row->image;?>"  border="0" alt="Image"/>
							</div>
							<? }else{?>
							<img src="<?php echo JURI::base(); ?>components/com_lightgallery/lightgallery/no_photo_small.gif"  alt="Image" border="1"/>
							<?php } ?>
						</div>
					</td>
				  </tr>
				  </tbody>
				</table>
  			</fieldset>
		</div>
		<div class="clr"></div>
 		<input type="hidden" name="c" value="images" />
		<input type="hidden" name="option" value="com_lightgallery" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="createdby " value="<?php echo $user->get('id'); ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
 		<?php
	}
}
?>