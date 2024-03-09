<?php defined('_JEXEC') or die('Restricted access');
$Itemid = JRequest::getVar( 'Itemid', 0, 'get', 'int' );

?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl; ?>/components/com_lightgallery/style.css">
<form action="<?php echo JRoute::_( 'index.php?option=com_lightgallery&view=category', false ); ?>" method="post" id="josForm" name="josForm">
<div>
	<div class="clsLightGallery"><div class="clsLightGallery_title"><?php echo JText::_( 'Light Gallery' ); ?></div></div>
</div>
	
<div id="clsWebpageBlueBorder" class="clsCompanyOverView">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  <?php 
  $count=0;
  foreach( $this->catlists as $catlist):
  	if($count % 2 == 0){ print('<tr>');}
   ?>
	<td class="clsMouseOut" id="light_<?php echo $catlist->id;?>" width="50%" align="left" valign="top" onmouseover="javscript:fncMouseOver('<?php echo $catlist->id;?>');" onmouseout="javscript:fncMouseOut('<?php echo $catlist->id;?>');">
		
		<?php if (file_exists("administrator/components/com_lightgallery/lightgallery/th".$this->getCatImage($catlist->id) )) { ?>
		<div class="clsFloatLeft" style="height:100px; overflow:hidden;border:2px inset #CCCCCC;">
			<a href="<?php echo JRoute::_( 'index.php?option=com_lightgallery&view=list&cid='.$catlist->id, false ); ?>">
			<img src="<?php echo JURI::base(); ?>administrator/components/com_lightgallery/lightgallery/<?php echo "th".$this->getCatImage($catlist->id);?>"  border="0" alt="Image" class="clsImgPad" width="100px"/>
			</a>
			</div>
		<?php }?>
		<div class="clsFloatLeft clsImgPad" >
		<a href="<?php echo JRoute::_( 'index.php?option=com_lightgallery&view=list&cid='.$catlist->id, false ); ?>">
		<?php 
 			echo ' <strong>'.$catlist->category.'</strong> ( '.(int)$this->getTotalImages($catlist->id).' )';
 		?>
		</a>
		</div>
		<div class="clsClearBoth"></div>
  	</td>
  <?php
	$count++;
  endforeach;?>
  </tr>
</table>
</div>

<input type="hidden" name="option" value="com_blog" />
<input type="hidden" name="view" value="blog" />
<input type="hidden" name="controller" value="blog" />
<input type="hidden" name="task" value="save_comment" />
</form>
<script language="javascript" type="text/javascript">
	function fncMouseOver(strid){
		var strdivid = "light_"+strid;
 		document.getElementById(strdivid).className = 'clsMouseOver';
	}
	function fncMouseOut(strid){
		var strdivid = "light_"+strid;
		document.getElementById(strdivid).className = 'clsMouseOut';
	}
</script>