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
class LightGalleryViewCategory
{
	function setLightGalleryCatToolbar()
	{
		JToolBarHelper::title( JText::_( 'LightGallery Manager' ), 'generic.png' );
		JToolBarHelper::addNewX( 'add' );
		JToolBarHelper::editListX( 'edit' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
	}

	function LightGalleryCategory( &$rows, &$pageNav, &$lists )
	{
		LightGalleryViewCategory::setLightGalleryCatToolbar();
		$user =& JFactory::getUser();
		JHTML::_('behavior.tooltip');
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
 			var form = document.adminForm;
			if (pressbutton == 'remove') {
				// do field validation
				if (!confirm("Do you want to delete Category, It will delete all images in this category.")) {
					return;
				}else{
					submitform( pressbutton );
				}
			}else{
				submitform( pressbutton );
			}
		}
 		//-->
		</script>
		<form action="index.php?option=com_lightgallery" method="post" name="adminForm">
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
						<?php echo JHTML::_('grid.sort',  'Category Title', 'b.category', @$lists['order_Dir'], @$lists['order'] ); ?>
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
					<th width="10%" nowrap="nowrap">
						<?php echo '#'; ?>
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
				$link		= JRoute::_( 'index.php?option=com_lightgallery&task=edit&cid[]='. $row->id );
 				
 				$row->published = $row->published;
				$published 		= JHTML::_('grid.published', $row, $i );
				$checked		= JHTML::_('grid.checkedout',   $row, $i );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>
					<td align="center">
						<?php echo $checked; ?>
					</td>
					<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo substr(JText::_($row->category),0, 200); ?>...">
						<?php
						if ( JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
							echo $row->category;
						} else {
							?>

							<a href="<?php echo $link; ?>">
								<?php echo $row->category; ?></a>
							<?php
						}
						?>
						</span>
					</td>
					<td align="center"><?php echo $row->postedname;?></td>
 					<td align="center">
						<?php echo JHTML::_('date',  $row->createdon, JText::_('DATE_FORMAT_LC1')); ?>
					</td>
					<td align="center">
						<?php echo JHTML::_('date',  $row->updatedon, JText::_('DATE_FORMAT_LC1')); ?>
					</td>
					<!--<td align="center">
						<?php echo $row->post_hits;?>
					</td>-->
					<td align="center">
						<?php echo $row->ordering;?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>
					<td align="center">
						<a href="<?php echo 'index.php?option=com_lightgallery&amp;c=images&amp;cad='. $row->id. ''; ?>">
							<?php echo 'Total Images';?> ( <?php $options['id'] = $row->id;  echo $this->fncGetTotalImages($options);?> )
						</a>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="c" value="category" />
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

		JToolBarHelper::title( $task == 'add' ? JText::_( 'LightGallery Manager - Category' ) . ': <small><small>[ '. JText::_( 'New' ) .' ]</small></small>' : JText::_( 'LightGallery Manager - Category' ) . ': <small><small>[ '. JText::_( 'Edit' ) .' ]</small></small>', 'generic.png' );
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel( 'cancel' );
	}
	
	/**
	 *function for Edit Posts
	 **/
	function editCategory( &$row, &$lists )
	{
		LightGalleryViewCategory::setLightgalleryToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		$user =& JFactory::getUser();
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'custombannercode' );
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
			if (form.category.value == "") {
				alert( "<?php echo JText::_( 'You must provide title.', true ); ?>" );
			}else{
				submitform( pressbutton );
			}
		}
 		//-->
		</script>
		<form action="index.php" method="post" name="adminForm">
		<div class="col100">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Details' ); ?></legend>
				<table class="admintable">
				<tbody>
					<tr>
						<td height="25" valign="top"><?php echo JText::_( 'Category Title' ); ?>:</td>
						<td valign="top"><div id="clsTableTdPadd"><input type="text" maxlength="150" size="70%"  class="inputbox" name="category" id="category" value="<?php echo $row->category;?>" /> *</div></td>
					</tr>
 					<tr>
						<td height="25" valign="top"><?php echo JText::_( 'Display Order' ); ?>:</td>
						<td valign="top"><div id="clsTableTdPadd"><input type="text" maxlength="10" size="5"  class="inputbox" name="ordering" id="ordering" value="<?php echo $row->ordering;?>" /></div></td>
					</tr>
 					<tr>
						<td height="25" valign="top" colspan="2" class="clsTDBorderTop"><label id="product_specialmsg" for="product_special"><?php echo JText::_( 'Do you want to publish?' ); ?>:</label>
						<? print $lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"  size="1"', ( isset($row->published)) ? $row->published : 1); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="25" align="right" class="clsTDBorderTop">
							<?php echo JText::_( '* Required' ); ?>
						</td>
					</tr>
  				</tbody>
				</table>
			</fieldset>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="c" value="category" />
		<input type="hidden" name="option" value="com_lightgallery" />
		<input type="hidden" name="createdby " value="<?php echo $user->get('id'); ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
?>