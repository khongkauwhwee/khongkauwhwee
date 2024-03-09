<?php defined('_JEXEC') or die('Restricted access');
$Itemid = JRequest::getVar( 'Itemid', 0, 'get', 'int' );
$cid = JRequest::getVar( 'cid', 0, 'get', 'int' );
?>
<link rel="stylesheet" type="text/css" href="<?php echo JURI::base(); ?>components/com_lightgallery/style.css">
<link rel="stylesheet" href="<?php echo JURI::base(); ?>components/com_lightgallery/slideshow/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_lightgallery/slideshow/prototype.js"></script>
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_lightgallery/slideshow/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_lightgallery/slideshow/lightbox.js"></script>
<form action="<?php echo JRoute::_( 'index.php?option=com_lightgallery&view=list', false ); ?>" method="post" id="josForm" name="josForm">
<div>
	<div class="clsLightGallery"><div class="clsLightGallery_title">
		<?php if($cid){?><a href="<?php echo JRoute::_( 'index.php?option=com_lightgallery&view=category', false ); ?>"><?php }?>
			<?php echo JText::_( 'Light Gallery' ); ?>
		<?php if($cid){?></a><?php }?>
		<?php if($cid){ echo ( '<span class="clsCatTtile"> - '.$this->getCategory($cid).'</span>'); }?>
		</div></div>
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
		
		<?php if (file_exists("administrator/components/com_lightgallery/lightgallery/th".$catlist->image )) { ?>
		<div class="clsFloatLeft" style="height:100px; overflow:hidden;border:2px inset #CCCCCC;">
			<a href="<?php echo JURI::base(); ?>administrator/components/com_lightgallery/lightgallery/<?php echo $catlist->image;?>" title="<?php echo $catlist->title;?>" rel="lightbox[Brussels]">
			<img src="<?php echo JURI::base(); ?>administrator/components/com_lightgallery/lightgallery/<?php echo "th".$catlist->image;?>"  id="<?php echo $count;?>"  border="0" alt="Image" class="clsImgPad" width="100px"/></a>
  			</div>
		<?php }?>
		<div class="clsFloatLeft clsImgPad" style="width:210px;">
 		<strong><?php  echo $catlist->title; ?></strong>
 		<div><?php echo $catlist->desc;?></div>
		
		</div>
		<div class="clsClearBoth"></div>
 		
 	</td>
  <?php
	$count++;
  endforeach;?>
  </tr>
</table>
<div class="developedby">* Click on the image for start slide show !</div>
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