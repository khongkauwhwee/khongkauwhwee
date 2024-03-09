<?php
/********************************************************************
Copyright 2009-2010 Chris Gaebler
Date    :   20 March 2010
Licence :	GNU/GPL
Description:  A Joomla! 1.5 component for SPGM picture gallery
Please see the pdf documentation at http://extensions.lesarbresdesign.info
*********************************************************************
This file is part of SPGM for Joomla and Mambo
This is free software. You can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation.
*********************************************************************/
defined('_JEXEC') or die('Restricted Access'); 

// -------------------------------------------------------------------------------
// Report an error on the screen and give the user a link to continue
// If $dir is specified, user will return to that directory
//
function reportError($errorText, $errorInfo='', $dir='')
{
	echo '<h3>'.$errorText.'</h3>';
	echo '<h3>'.$errorInfo.'</h3>';
	echo '<a href="index.php?option=com_spgm&dir='.$dir.'">';
	echo JText::_('TXT_CONTINUE');
	echo '</a>';
}

//-------------------------------------------------------------------------------
// Show the Help and Support screen
//
function showHelpScreen()
{
	adminForm();
	$link_doc = "http://extensions.lesarbresdesign.info/en/downloads/category/3-spgm";
	$link_version = "http://extensions.lesarbresdesign.info/en/version-history/spgm";
	$link_rating = "http://extensions.joomla.org/extensions/photos-a-images/photo-gallery/9437";
	$link_chrisguk = "http://extensions.joomla.org/extensions/owner/chrisguk";
	$link_LAextensions = "http://extensions.lesarbresdesign.info/";
	?>
	<p style="color:#0B55C4; font-size:15px"><?php echo LA_COMPONENT_NAME.': '.JTEXT::_('HELP_TITLE');?></p>
	<?php echo JText::sprintf('VERSION', LA_COMPONENT_NAME).' '.LA_COMPONENT_VERSION?><br />
	<p><?php echo '<strong>'.JTEXT::_('HELP_CONFIG').'</strong>';?></p>
	<p><?php echo JTEXT::_('HELP_DOC').' '.JHTML::link($link_doc, "www.lesarbresdesign.info", 'target="_blank"');?></p>
	<p><?php echo JTEXT::_('HELP_CHECK').' '.JHTML::link($link_version, 'Les Arbres Design - SPGM', 'target="_blank"');?></p>
	<p><?php echo JText::sprintf('HELP_RATING', LA_COMPONENT_NAME).' '.JHTML::link($link_rating, 'Joomla! Extensions', 'target="_blank"');?></p>
	<p><?php echo JTEXT::sprintf('HELP_LES_ARBRES', LA_COMPONENT_NAME, JHTML::link($link_chrisguk, 'Joomla! Extensions', 'target="_blank"')).' '.JHTML::link($link_LAextensions, 'Les Arbres Design', 'target="_blank"');?><p>
	<table>
		<tr></tr>
		<tr>
			<td><?php echo JText::sprintf('HELP_FUND_ONE', LA_COMPONENT_NAME);?><br />
				<?php echo JText::_('HELP_FUND_TWO');?>
			</td>
			<td>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="11095351">
					<input type="image" src="<?php echo JText::_('HELP_DONATE_BUTTON');?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
				</form>
			</td>
		</tr>
	</table>
	<?php	
}

// -------------------------------------------------------------------------------
// Determine file type from file name
//
function getFileType($filename)
{
	if ($filename == SPGM_CONF)
		return FILE_TYPE_CONF;
  	$file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
   	switch ($file_ext)
   		{
   		case 'txt': $filetype = FILE_TYPE_TEXT;
   			break;
   		case 'gif': $filetype = FILE_TYPE_GIF;
   			break;
   		case 'jpg': $filetype = FILE_TYPE_JPG;
   			break;
   		case 'png': $filetype = FILE_TYPE_PNG;
   			break;
   		default: 	$filetype = FILE_TYPE_OTHER;
   		}
   	if (($filetype >= FILE_TYPE_GIF)
   		and (substr($filename, 0, THUMBNAIL_PREFIX_LEN) == THUMBNAIL_PREFIX))
   			$filetype = FILE_TYPE_THUMBNAIL;
   	return $filetype;
}

// -------------------------------------------------------------------------------
// Return filetype, size, and image dimensions
// For a thumbnail, returns the size and dimensions of the large image
//
function getFileInfo($absDir,$filename,&$filesize,&$imageX,&$imageY)
{
	$filetype = getFileType($filename);
	if ($filetype == FILE_TYPE_OTHER)
		return $filetype;
		
	$imageX = 0;
	$imageY = 0;
	
	if ($filetype == FILE_TYPE_THUMBNAIL)
		{
		$filename = substr($filename, THUMBNAIL_PREFIX_LEN);	// make name of large image
		if (!file_exists($absDir.$filename))					// if no large image...
			return FILE_TYPE_ORPHAN_THUMBNAIL;					// it's an orphan
		}

	$filesize = round((filesize($absDir.$filename) / 1024), 2).' kb';

	if ($filetype == FILE_TYPE_TEXT)
		return $filetype;
		
	$imageInfo = getimagesize($absDir.$filename);
	if ($imageInfo !== false)
		{
		$imageX = $imageInfo[0];
		$imageY = $imageInfo[1];
		}
	return $filetype;
}

// -------------------------------------------------------------------------------
// expand a directory, if necessary, to include the gallery base path
//
function getAbsolutePath($dir='')
{
	if ($dir == '')							// if blank
		$dir = GALLERY_BASE_DIR;			// .. use base
		
	if (!strstr($dir, GALLERY_BASE_DIR))	// if not within base
		$dir = GALLERY_BASE_DIR.$dir;		// .. add base
		
	$dir = realpath($dir);					// in case someone tries to use /../.
	if (strlen($dir) < strlen(GALLERY_BASE_DIR))
		$dir = GALLERY_BASE_DIR;			// force back to base
	
	if (!is_dir($dir))						// go there so we can use relative paths
		reportError(JText::_('E_DIR'),$dir);
		
	if ($dir[strlen($dir)-1] != '/')
		$dir = $dir.'/';				// always return path with a trailing /
	return $dir;
}

// -------------------------------------------------------------------------------
// return the parent directory, but not above GALLERY_BASE_DIR
//
function getParentDirectory($relDir)
{
	$absDir = getAbsolutePath($relDir);
	$absDir = realpath($absDir.'/../.');
	$newDir = substr($absDir, strlen(GALLERY_BASE_DIR));		// make new relative dir
	return $newDir;
}

// -------------------------------------------------------------------------------
// Gets info for a gallery directory:
// 		An array of all the .txt files in the directory
//		An array of all the image files in the directory
// 		The number of images
//		The number of thumbnails
//		The number of text files
//		The number of directories
//		Flag if it has a spgm.conf file
// $absDir is absolute with no trailing /
//
function getDirInfo($absDir,&$textFiles,&$imageFiles,&$imageCount,&$thumbCount,&$textCount,
		&$dirCount,&$configFile)
{
	$handle = opendir($absDir);
	if (!$handle)
		return;

	while (($filename = readdir($handle)) != false)
	    {
    	if ($filename == '.' or $filename == '..')
    		continue;
        if (is_dir($absDir."/".$filename))
        	{
        	$dirCount ++;
        	continue;
        	}
        $filetype = getFileType($filename);
        if ($filetype == FILE_TYPE_TEXT)
			{
			$textFiles[] = $filename;
			$textCount ++;
			}
        if ($filetype == FILE_TYPE_THUMBNAIL)
			$thumbCount ++;
        if ($filetype >= FILE_TYPE_GIF)
			{
			$imageFiles[] = $filename;
			$imageCount ++;
			}
		if ($filetype == FILE_TYPE_CONF)
			$configFile = true;
	    }
    closedir($handle);
}

// -------------------------------------------------------------------------------
// Rename a directory
// $relDir is the current directory
// $obj_name is the directory to rename
// $new_name is the new name
// Returns true for success, false for failure
//
function renameDir($relDir, $obj_name, $new_name) 
{
	if ($new_name == '')
		{
		reportError(JText::_('E_DIR_RENAME'),$obj_name.' -> '.$new_name,$relDir);
		return false;
		}
	$new_name = utf8_decode($new_name);
	$new_name = str_replace(" ","_",$new_name);			// change spaces to underscores
	
	$absDir = getAbsolutePath($relDir);
	
	if (file_exists($absDir.$new_name))						// and check if it already exists
		{
		reportError(JText::_('E_DIR_EXISTS'),$new_name,$relDir);
		return false;
		}
		
	$cwd = getcwd();									// get current working directory
	if (!chdir($absDir))								// go there so we can use relative rename
		{
		reportError(JText::_('E_DIR'),$absDir,$relDir);
		chdir($cwd);									// go back where we were
		return false;
		}
		
	if (!rename($obj_name,$new_name))
		{
		reportError(JText::_('E_DIR_RENAME'),$obj_name.' -> '.$new_name,$relDir);
		chdir($cwd);									// go back where we were
		return false;
		}
	chdir($cwd);										// go back where we were
	return true;
}

// -------------------------------------------------------------------------------
// Create a new directory
// $relDir is the current directory, $new_name is the new directory to create in it
// Returns true for success, false for failure
//
function createDir($relDir,$new_name) 
{
	if ($new_name == '')
		{
		reportError(JText::_('E_CREATE_DIR'),$new_name,$relDir);
		return false;
		}
	$absDir = getAbsolutePath($relDir);
	$new_name = utf8_decode($new_name);
	$new_name = str_replace(" ","_",$new_name);			// change spaces to underscores
	$abs_new_name = $absDir.$new_name;
	if (is_dir($abs_new_name))
		{
		reportError(JText::_('E_DIR_EXISTS'),$new_name,$relDir);
		return false;
		}
	if (!mkdir($abs_new_name))
		{
		reportError(JText::_('E_CREATE_DIR'),$new_name,$relDir);
		return false;
		}
	return true;
}

// -------------------------------------------------------------------------------
// Recursive directory delete (from php manual)
//
function _deldir($dir)
{
	$current_dir = opendir($dir);
	while($entryname = readdir($current_dir))
		{
	    if (is_dir("$dir/$entryname") and ($entryname != "." and $entryname != ".."))
			deldir("${dir}/${entryname}");
    	elseif ($entryname != "." and $entryname!="..")
	       	unlink("${dir}/${entryname}");
 		}
 	closedir($current_dir);
 	rmdir(${dir});
}

// -------------------------------------------------------------------------------
// Delete a directory
// Returns true for success, false for failure
//
function deleteDir($dir,$obj_name)
{
	$absDir = getAbsolutePath($dir);
	
	if (DELETE_NON_EMPTY_DIRECTORIES)
		_deldir($absDir.$obj_name);
	else
		{
		if (!@rmdir($absDir.$obj_name))
			{
			reportError(JText::_('E_DELETE_DIR'),$obj_name,$dir);
			return false;
			}
		}
	return true;
}

// -------------------------------------------------------------------------------
// Read a text file
// Returns true for success, false for failure
// Also returns the number of files added to pic-desc or pic-sort files
//
function readTextFile($relDir,$file,&$contents,&$addedCount,&$removedCount)
{
	if (filesize(GALLERY_BASE_DIR.$relDir."/".$file) > MAX_TEXT_EDIT)
		{
		reportError(JText::_('E_TOO_BIG'),$file,$relDir);
		return false;
		}
		
	$file_lines = file(GALLERY_BASE_DIR.$relDir."/".$file);
	if ($file_lines === false)
		{
		reportError(JText::_('E_READ_FAIL'),$file,$relDir);
		return false;
		}
		
// if it's not a pic-desc or pic-sort file, we're done

	$subfile = substr($file, 0, 8);
	if (($subfile != 'pic-desc') and ($subfile != 'pic-sort'))
		{
		$contents = implode("",$file_lines);
		return true;
		}

// pic-desc files have multiple lines like this:
// filename.jpg | picture caption as long as you like
//
// first get a list of image files in the directory

	$textFiles = array();		// array of text files in the directory (don't use this here)
	$dirFiles = array();		// array of filenames in the directory
	$imageCount = 0;
	$thumbCount = 0;
	$textCount = 0;
	$dirCount = 0;
	$configFile = false;
	getDirInfo(GALLERY_BASE_DIR.$relDir,$textFiles,$dirFiles,$imageCount,$thumbCount,$textCount,$dirCount,$configFile);
	sort($dirFiles);
	
// now comment out filenames in the file that are not in the directory
		
	$picDescFiles = array();
	$contents = '';
	foreach ($file_lines as $line)
		{
		if (substr($line,0,1) == ';')					// if line is commented out
			{
			$contents = $contents.$line;				// .. just copy the line
			continue;
			}
		if ($subfile == 'pic-desc')
			{
			$pos = strpos($line,"|");	
			if ($pos === false)							// must use === here
				{										// if no | ..
				$contents = $contents.$line;			// .. just copy the line
				continue;
				}
			$filename = trim(substr($line,0,$pos));		// extract the filename
			}
		else											// it's pic-sort
			$filename = trim($line);					// so take the whole line
		$picDescFiles[] = $filename;					// save it for later
		if (array_search($filename,$dirFiles) !== false)	// must use === here
			$contents = $contents.$line;				// just copy the line
		else											// if file not prsent
			{
			$contents = $contents.'; '.$line;			// comment it out
			$removedCount ++;
			}
		}
		
// now find images that are in the directory but not in the file, and add blank entries for them

	foreach ($dirFiles as $dirFile)
		{
		if (array_search($dirFile,$picDescFiles) === false)	// must use === (array_search returns the key)
			{
			if ($addedCount == 0)
				$contents = $contents."\n";
			if ($subfile == 'pic-desc')
				$contents = $contents.$dirFile." | \n";		// pic-desc
			else
				$contents = $contents.$dirFile."\n";		// pic-sort
			$addedCount ++;
			}
		}
		
	return true;
}

// -------------------------------------------------------------------------------
// Save a text file
// if $isNew is true, we are creating a new file
// Returns true for success, false for failure
//
function saveTextFile($relDir,$obj_name,$contents,$isNew)
{
	if ($obj_name == '')
		{
		reportError(JText::_('E_CREATE_FILE'),$obj_name,$relDir);
		return false;
		}
		
	$absDir = getAbsolutePath($relDir);
	if ($isNew)
		{
		$obj_name = str_replace(" ","_",$obj_name);
	  	$file_ext = strtolower(pathinfo($obj_name, PATHINFO_EXTENSION));
	  	if (($file_ext != 'txt') and ($file_ext != 'conf'))
			$obj_name = $obj_name.'.txt';
		if ($isNew and is_file($absDir.$obj_name))
			{
			reportError(JText::_('E_FILE_EXISTS'),$obj_name,$relDir);
			return false;
			}
		}
		
	$handle = fopen($absDir.$obj_name, 'w');
	if (!$handle)
		{
		reportError(JText::_('TXT_OPEN_WRITE_FAIL'),$obj_name,$relDir);
		return false;
		}

	$contents = stripslashes($contents);
	if (strlen($contents) > 0)
		{
		if (!fwrite($handle,$contents))
			{
			reportError(JText::_('E_WRITE_FAIL'),$obj_name,$relDir);
			return false;
			}
		}
	fclose($handle);
	return true;
}

// -------------------------------------------------------------------------------
// Delete a file
// Returns true for success, false for failure
//
function deleteFile($relDir, $obj_name)
{
	$absDir = getAbsolutePath($relDir);

	if (!unlink($absDir.$obj_name))
		{
		reportError(JText::_('E_DELETE_FILE'),$obj_name,$relDir);
		return false;
		}
	$filetype = getFileType($obj_name);
	if ($filetype >= FILE_TYPE_GIF)
		if (file_exists($absDir.THUMBNAIL_PREFIX.$obj_name))
			if (!unlink($absDir.THUMBNAIL_PREFIX.$obj_name))
				{
				reportError(JText::_('E_DELETE_FILE'),THUMBNAIL_PREFIX.$obj_name,$relDir);
				return false;
				}
	return true;
}

// -------------------------------------------------------------------------------
// Rename a file
// $relDir is the current directory
// $obj_name is the file to rename. For an image it's the large image file name.
// $new_name is the new name
// Returns true for success, false for failure
//
function renameFile($relDir, $obj_name, $new_name) 
{
	$newFileType = getFileType($new_name);
	if ($newFileType == FILE_TYPE_OTHER)		// only permit extensions we show
		{
		reportError(JText::_('E_BAD_FILETYPE'),$obj_name.' -> '.$new_name,$relDir);
		return false;
		}

	$absDir = getAbsolutePath($relDir);
	
	$new_name = utf8_decode($new_name);
	if (file_exists($absDir.$new_name))					// check if name already exists
		{
		reportError(JText::_('E_FILE_EXISTS'),$obj_name,$relDir);
		return false;
		}
		
	$cwd = getcwd();									// get current working directory
	if (!chdir($absDir))								// go there so we can use relative rename
		{
		reportError(JText::_('E_DIR'),$absDir,$relDir);
		chdir($cwd);									// go back where we were
		return false;
		}
		
	if (!rename($obj_name,$new_name))
		{
		reportError(JText::_('E_FILE_RENAME'),$obj_name.' -> '.$new_name,$relDir);
		chdir($cwd);									// go back where we were
		return false;
		}
		
	$filetype = getFileType($new_name);
	if ($filetype >= FILE_TYPE_GIF)								// if file is an image
		if (file_exists($absDir.THUMBNAIL_PREFIX.$obj_name))	// and its thumbnail exists
			if (!rename(THUMBNAIL_PREFIX.$obj_name,THUMBNAIL_PREFIX.$new_name))		// rename that too
				{
				reportError(JText::_('E_FILE_RENAME'),THUMBNAIL_PREFIX.$obj_name.' -> '.THUMBNAIL_PREFIX.$new_name,$relDir);
				chdir($cwd);									// go back where we were
				return false;
				}

	chdir($cwd);										// go back where we were
	return true;
}

// -------------------------------------------------------------------------------
// Return true if supplied argument is a positive integer, else false
//
function is_posint($arg)
{
	if (!is_numeric($arg))
		return false;
	if ((intval($arg) == $arg) and ($arg >= 0))
		return true;
	else
		return false;
}

// -------------------------------------------------------------------------------
// Save a conf file
// Returns true for success, false for failure
//
function saveConfFile($relDir)
{
	$absDir = getAbsolutePath($relDir);
	$handle = fopen($absDir.SPGM_CONF, 'w');
	if (!$handle)
		{
		reportError(JText::_('TXT_OPEN_WRITE_FAIL'),SPGM_CONF,$relDir);
		return false;
		}
	if (!fwrite($handle,"<?php\n"))
		{
		reportError(JText::_('E_WRITE_FAIL'),SPGM_CONF,$relDir);
		return false;
		}
	fwrite($handle,"# ".date("l d F Y, H:i")."\n");
	
	$conf_newStatusDuration = JRequest::getString('conf_newStatusDuration');
	if (!is_posint($conf_newStatusDuration))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_NEWSTATUSDURATION').': '.$conf_newStatusDuration,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['newStatusDuration'] = ".JRequest::getString('conf_newStatusDuration').";\n");
	
	$conf_thumbnailsPerPage = JRequest::getString('conf_thumbnailsPerPage');
	if (!is_posint($conf_thumbnailsPerPage))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_THUMBNAILSPERPAGE').': '.$conf_thumbnailsPerPage,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['thumbnailsPerPage'] = ".JRequest::getString('conf_thumbnailsPerPage').";\n");
	
	$conf_thumbnailsPerRow = JRequest::getString('conf_thumbnailsPerRow');
	if (!is_posint($conf_thumbnailsPerRow))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_THUMBNAILSPERROW').': '.$conf_thumbnailsPerRow,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['thumbnailsPerRow'] = ".JRequest::getString('conf_thumbnailsPerRow').";\n");

	$conf_galleryListingCols = JRequest::getString('conf_galleryListingCols');
	if (!is_posint($conf_galleryListingCols))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_GALLERYLISTINGCOLS').': '.$conf_galleryListingCols,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['galleryListingCols'] = ".JRequest::getString('conf_galleryListingCols').";\n");
	
	fwrite($handle,'$spgm_cfg'."['conf']['galleryCaptionPos'] = ".JRequest::getString('conf_galleryCaptionPos').";\n");
	
	$conf_subGalleryLevel = JRequest::getString('conf_subGalleryLevel');
	if (!is_posint($conf_subGalleryLevel))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_SUBGALLERYLEVEL').': '.$conf_subGalleryLevel,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['subGalleryLevel'] = ".JRequest::getString('conf_subGalleryLevel').";\n");
	
	fwrite($handle,'$spgm_cfg'."['conf']['galleryOrientation'] = ".JRequest::getString('conf_galleryOrientation').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['gallerySortType'] = ".JRequest::getString('conf_gallerySortType').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['gallerySortOptions'] = ".JRequest::getString('conf_gallerySortOptions').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['pictureSortType'] = ".JRequest::getString('conf_pictureSortType').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['pictureSortOptions'] = ".JRequest::getString('conf_pictureSortOptions').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['pictureInfoedThumbnails'] = ".JRequest::getString('conf_captionedThumbnails').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['captionedThumbnails'] = ".JRequest::getString('conf_captionedThumbnails').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['pictureCaptionedThumbnails'] = ".JRequest::getString('conf_pictureCaptionedThumbnails').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['filenameWithThumbnails'] = ".JRequest::getString('conf_filenameWithThumbnails').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['filenameWithPictures'] = ".JRequest::getString('conf_filenameWithPictures').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['enableSlideshow'] = ".JRequest::getString('conf_enableSlideshow').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['enableDropShadows'] = ".JRequest::getString('conf_enableDropShadows').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['fullPictureWidth'] = ".JRequest::getString('conf_fullPictureWidth').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['fullPictureHeight'] = ".JRequest::getString('conf_fullPictureHeight').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['popupOverFullPictures'] = ".JRequest::getString('conf_popupOverFullPictures').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['popupPictures'] = ".JRequest::getString('conf_popupPictures').";\n");
	
	$conf_popupWidth = JRequest::getString('conf_popupWidth');
	if (!is_posint($conf_popupWidth))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_POPUPWIDTH').': '.$conf_popupWidth,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['popupWidth'] = ".JRequest::getString('conf_popupWidth').";\n");
	
	$conf_popupHeight = JRequest::getString('conf_popupHeight');
	if (!is_posint($conf_popupHeight))
		{
		reportError(JText::_('E_INVALID').' '.JText::_('CFG_TITLE_POPUPHEIGHT').': '.$conf_popupHeight,
			JText::_('E_CONF_FILE_NOT_SAVED'),$relDir);
		return false;
		}
	fwrite($handle,'$spgm_cfg'."['conf']['popupHeight'] = ".JRequest::getString('conf_popupHeight').";\n");
	
	fwrite($handle,'$spgm_cfg'."['conf']['popupFitPicture'] = ".JRequest::getString('conf_popupFitPicture').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['filters'] = '".JRequest::getString('conf_filters')."';\n");
	fwrite($handle,'$spgm_cfg'."['conf']['zoomFactors'] = array(".JRequest::getString('conf_zoomFactors').");\n");
	$conf_exifInfo = JRequest::getString('conf_exifInfo');
	if ($conf_exifInfo == '')
		$str = '';
	else
		{
		$exifArray = explode(',',$conf_exifInfo);
		$str = "'".implode("','",$exifArray)."'";
		}
	fwrite($handle,'$spgm_cfg'."['conf']['exifInfo'] = array(".$str.");\n");
	fwrite($handle,'$spgm_cfg'."['conf']['galleryIconType'] = ".JRequest::getString('conf_galleryIconType').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['galleryIconHeight'] = ".JRequest::getString('conf_galleryIconHeight').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['galleryIconWidth'] = ".JRequest::getString('conf_galleryIconWidth').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['stickySpgm'] = ".JRequest::getString('conf_stickySpgm').";\n");
	$conf_language = JRequest::getString('conf_language');
	if ($conf_language == 'LANG_AUTO')
		fwrite($handle,'$spgm_cfg'."['conf']['language'] = ".$conf_language.";\n"); // no quotes if using the defined constant LANG_AUTO
	else
		fwrite($handle,'$spgm_cfg'."['conf']['language'] = '".$conf_language."';\n");	// with quotes for a string, e.g. 'en'
	fwrite($handle,'$spgm_cfg'."['conf']['theme'] = '".JRequest::getString('conf_theme')."';\n");
	fwrite($handle,'$spgm_cfg'."['conf']['multiLanguageCaptions'] = ".JRequest::getString('conf_multiLanguageCaptions').";\n");
	fwrite($handle,'$spgm_cfg'."['conf']['spgmLink'] = ".JRequest::getString('conf_spgmLink').";\n");
	fwrite($handle,"?>\n");
	fclose($handle);
	return true;
}

// -------------------------------------------------------------------------------
// Return the language dependent image html for a text file e.g. pic-desc_en.txt
//
function textImage($file)
{
	$dotpos = strrpos($file, ".txt");		// we want the two characters before the dot
	if (($dotpos > 2) and (substr($file, $dotpos - 3, 1) == '_'))
		$lang = '_'.substr($file, $dotpos - 2, 2);
	else
		$lang = '';
	$name = 'text'.$lang.'.png';			// language dependent image
	if (!file_exists(ADMIN_BASE_IMAGE_DIR.$name))	// have we got it?
		$name = 'text.png';					// if not, default to this one
	$html = '<img border="0" width="100" height="75" align="left" src="'.ADMIN_WEB_IMAGE_DIR.$name.'" alt="" />';
	return $html;
}

// -------------------------------------------------------------------------------
// Delete all thumbnails in a directory
//
function clearThumbs($relDir)
{
	$absDir = getAbsolutePath($relDir);
	$handle = opendir($absDir);
	if (!$handle)
		reportError(JText::_('E_DIR'),$absDir,$relDir);

	while (($filename = readdir($handle)) != false)
		{
		$filetype = getFileType($filename);
		if ($filetype == FILE_TYPE_THUMBNAIL)
			if (!unlink($absDir."/".$filename))
				reportError(JText::_('E_DELETE_FILE'),$filename,$relDir);
		}
	closedir($handle);
}

// -------------------------------------------------------------------------------
// Create a resized copy of an image
// Returns true for success, false for failure
//
function resizeImage($relDir,$inputFilePath,$outputFilePath,$targetHsize,$targetVsize)
{
	if (!is_file($inputFilePath))
		return;
	
	$filetype = getFileType($inputFilePath);
	switch ($filetype) 
		{
		case FILE_TYPE_GIF:
			if (!($srcImage = ImageCreateFromGIF($inputFilePath)))
				{
				reportError(JText::_('E_GDLIB'),$inputFilePath, $relDir) ;
				return false;
				}
			break;
		case FILE_TYPE_JPG:
			if (!($srcImage = ImageCreateFromJPEG($inputFilePath)))
				{
				reportError(JText::_('E_GDLIB'),$inputFilePath, $relDir) ;
				return false;
				}
			break;
		case FILE_TYPE_PNG:
			if (!($srcImage = ImageCreateFromPNG($inputFilePath)))
				{
				reportError(JText::_('E_GDLIB'),$inputFilePath, $relDir);
				return false;
				}
			break;
		default:
			{
			reportError(JText::_('E_NOT_IMAGE'),$inputFilePath, $relDir);
			return false;
			}
		}

	$srcWidth = ImageSX ($srcImage);			// Get dimensions
	$srcHeight = ImageSY ($srcImage);

	if ($srcHeight > $targetVsize)			// If image is too high
		{
		if (($srcWidth / $srcHeight) * $targetVsize > $targetHsize)
			{
			$destWidth = $targetHsize;
			$destHeight = ($srcHeight / $srcWidth) * $targetHsize;
			}
		else
			{
			$destHeight = $targetVsize;
			$destWidth = ($srcWidth / $srcHeight) * $targetVsize;
			}
		}
	elseif ($srcWidth > $targetHsize)
		{										// Height OK, but too wide
		$destWidth = $targetHsize;
		$destHeight = ($srcHeight / $srcWidth) * $targetHsize;
		}
	else
		{
		$destWidth = $srcWidth;
		$destHeight = $srcHeight;
		}

	// Create new image with correct dimensions
	
	$destImage = imagecreateTrueColor($destWidth, $destHeight); 

	ImageCopyResized($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight); 

	if (!imageJPEG($destImage,$outputFilePath,RESIZE_QUALITY))
		{
		reportError(JText::_('E_RESIZE_ERROR'),$inputFilePath, $relDir);
		ImageDestroy($srcImage); 
		ImageDestroy($destImage);
		return false;
		}

	ImageDestroy($srcImage); 
	ImageDestroy($destImage);
	return true;
}
	
// -------------------------------------------------------------------------------
// Build all thumbnails for a directory
// Returns true for success, false for failure
//
function buildThumbs($relDir)
{
	clearThumbs($relDir);				// Clear existing thumbnails

	$absDir = getAbsolutePath($relDir);
	$handle = opendir($absDir);
	if (!$handle)
		{
		reportError(JText::_('E_DIR'),$relDir,$relDir);
		return false;
		}

	while (($filename = readdir($handle)) != false)
		{
    	if ($filename == '.' or $filename == '..')
    		continue;
        if (is_dir($absDir."/".$filename))
        	continue;
		$filetype = getFileType($filename);
		if ($filetype >= FILE_TYPE_GIF)
			if (!resizeImage($relDir,
						$absDir.$filename,
						$absDir.THUMBNAIL_PREFIX.$filename,
						THUMBNAIL_H_SIZE,THUMBNAIL_V_SIZE))
				{
				closedir($handle);
				return false;
				}
		}
	closedir($handle);
	return true;
}

// -------------------------------------------------------------------------------
// Save newly uploaded files
// Returns true for success, false for failure
//
function saveUploadedFiles($relDir,$resize,$x_resize,$y_resize)
{
	$b_resize = false;
	if (strcmp($resize,"yes") == 0)					// sort out the resize dimensions
		{
		if (($x_resize == '') and ($y_resize == ''))	// one or other can be blank but not both
			{
			reportError(JText::_('E_RESIZE_DIMENSIONS'),'0,0',$relDir);
			return false;
			}
		if (($x_resize > 4096) or ($y_resize > 4096))
			{
			reportError(JText::_('E_RESIZE_DIMENSIONS'),$x_resize.', '.$y_resize,$relDir);
			return false;
			}
		if ($y_resize == '')
			$y_resize = 10000;
		if ($x_resize == '')
			$x_resize = 10000;
		if (($x_resize < 100) or ($y_resize < 100))
			{
			reportError(JText::_('E_RESIZE_DIMENSIONS'),$x_resize.', '.$y_resize,$relDir);
			return false;
			}
		$b_resize = true;
		}
			
	$absDir = getAbsolutePath($relDir);
	
	for ($i=0; $i<10; $i++)
		{
		$tmp_name = $_FILES["upfile"]["tmp_name"][$i];
		if ($tmp_name == '')
			continue;
		$filename = $_FILES["upfile"]["name"][$i];
		$filename = utf8_decode(stripslashes($filename));
		$error = $_FILES["upfile"]["'error'"][$i];	// should really test this!

		$filetype = getFileType($filename);
		if ($filetype <= FILE_TYPE_THUMBNAIL)
			{
			reportError(JText::_('E_BAD_FILETYPE'),$filename,$relDir);
			return false;
			}
			
		if (!is_uploaded_file($tmp_name))
			{
			reportError(JText::_('E_UPLOAD_FILE'),$tmp_name,$relDir);
			return false;
			}
			
		$absfilename = $absDir.$filename;

		if (!copy($tmp_name, $absfilename))
			{
			reportError(JText::_('E_COPY_FILE'),$absfilename,$relDir);
			return false;
			}
			
		if (($b_resize) and ($filetype >= FILE_TYPE_GIF))
			{
			$filesize = 0;
			$imageX = 0;
			$imageY = 0;
			getFileInfo($absDir,$filename,$filesize,$imageX,$imageY);
			if (($imageX > $x_resize) or ($imageY > $y_resize))
				if (!resizeImage($relDir,$absfilename,$absfilename,$x_resize,$y_resize))
					{
					reportError(JText::_('E_RESIZE_ERROR'),$absfilename,$relDir);
					return false;
					}
			}
			
		if (!unlink($tmp_name))
			{
			reportError(JText::_('E_DELETE_TMP_FILE'),$tmp_name,$relDir);
			return false;
			}
		if ($filetype >= FILE_TYPE_GIF)			// create thumbnail ?
			if (!resizeImage($relDir,$absfilename,$absDir.THUMBNAIL_PREFIX.$filename,THUMBNAIL_H_SIZE,THUMBNAIL_V_SIZE))
				{
				reportError(JText::_('E_CREATE_THUMB'),$filename,$relDir);
				return false;
				}
		}
	return true;
}

// -------------------------------------------------------------------------------
// Draw the form to create a new directory
//
function showCreateDirForm($relDir)
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	echo '<form action="index.php" method="post" name="adminForm">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo JText::_('TXT_NEW_DIR').'&nbsp;';
	echo '<input type="Text" size="150" name="obj_name">';
	echo '</form><br />';
}

// -------------------------------------------------------------------------------
// Draw the form to rename a file OR directory
//
function showRenameForm($relDir,$obj_name)
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	$encObjName = rawurlencode($obj_name);
	echo '<form action="index.php" method="post" name="adminForm">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo '<input type="hidden" name="obj_name" value="'.$encObjName.'" />';
	echo '<h3>'.JText::_('TXT_RENAME').'&nbsp;'.utf8_encode($obj_name).'</h3>';
	echo JText::_('TXT_NEW_NAME').'&nbsp;';
	echo '<input type="Text" size="150" name="new_name" value="'.utf8_encode($obj_name).'">';
	echo '</form><br />';
}

// -------------------------------------------------------------------------------
// Draw the form to upload files
//
function showUploadForm($relDir)
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	echo JText::_('TXT_UPLOAD_PROMPT').'<br />';
	echo '<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="save_uploads" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo '<table>';
	echo '<tr><td><table>';
	for ($i=0; $i<10; $i++)
		echo '<tr><td><input type="file" size="100" name="upfile[]"></td></tr>';
	echo '</table></td><td valign="top" width=200>';
	echo '<table width="100%"><tr><td>'.JText::_('TXT_RESIZE').'</td><td><Input Type="Checkbox" Name="resize" Value="yes"></td></tr>';
	echo '<tr><td>X</td><td><input type="Text" size="10" name="x_size"></td></tr>';
	echo '<tr><td>Y</td><td><input type="Text" size="10" name="y_size"></td></tr>';
	$up_img = '<img src="images/upload_f2.png" width="32" height="32" border="0" alt="" />';
	echo '<tr><td colspan="2" align="center">';
	echo '<button onclick="this.form.submit();"><div style="font:Bold 18px Verdana;line-height:40px";margin-bottom: 10px;>'.JText::_('TXT_UPLOAD').'</div>'.$up_img.'</button>';
	echo '</td></tr></table>';
	
	echo '</td></tr></table></form>';

}

// -------------------------------------------------------------------------------
// Draw the form to create a new text file
//
function showCreateTextFileForm($relDir)
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	echo '<form action="index.php" method="post" name="adminForm">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo JText::_('TXT_CREATE_FILE').'&nbsp;';
	echo '<input type="Text" size="40" name="obj_name"><br /><br />';
	echo '<textarea name="contents" rows="25" cols="125"></textarea>';
	echo '</form>';
}

// -------------------------------------------------------------------------------
// Draw the form to edit an existing text file
// Returns true for success, false for failure
//
function showTextEditor($relDir,$file)
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	$contents = '';
	$addedCount = 0;
	$removedCount = 0;
	if (!readTextFile($relDir,$file,$contents,$addedCount,$removedCount))
		return false;
	
	echo '<h3>'.$file.'</h3>';
	if ($addedCount != 0)
		echo $addedCount.'&nbsp;'.JText::_('TXT_FILES_ADDED');
	if ($removedCount != 0)
		echo '&nbsp;&nbsp;'.$removedCount.'&nbsp;'.JText::_('TXT_FILES_COMMENTED');
	echo '<form action="index.php" method="post" name="adminForm">';
	echo '<textarea name="contents" rows="25" cols="125">'.$contents .'</textarea>';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo '<input type="hidden" name="obj_name" value="'.$file.'" />';
	echo '<input type="hidden" name="hidemainmenu" value="0" />';
	echo '</form><br />';
	return true;
}

// -------------------------------------------------------------------------------
// The following functions are for the conf file editor
// -------------------------------------------------------------------------------
// Draw a config item array field for showConfEditor() 
//
function drawConfArrayField($fieldname)
{
global $spgm_cfg;
	$FieldUpper = strtoupper($fieldname);
	$titleLangText = 'CFG_TITLE_'.$FieldUpper;
	$descLangText = 'CFG_DESCR_'.$FieldUpper;
	echo '	<tr><td '.STYLE_CONFIG_KEY.'>';
	echo '	<strong>'.JText::_($titleLangText).'</strong><br />';
	echo 			  JText::_($descLangText);
	if ($fieldname == 'exifInfo')
		if (extension_loaded('exif'))
			echo ' '.JText::_('EXIF_ENABLED');
		else
			echo ' '.JText::_('EXIF_DISABLED');
	echo '		</td><td class="paramlist_value">';
	echo '		<input type="Text" size="60" name="conf_'.$fieldname.'" value = "'.implode(',',$spgm_cfg['conf'][$fieldname]).'">';
	echo '		</td></tr>';
}

// -------------------------------------------------------------------------------
// Draw a config item text field for showConfEditor() 
//
function drawConfTextField($fieldname)
{
global $spgm_cfg;
	$FieldUpper = strtoupper($fieldname);
	$titleLangText = 'CFG_TITLE_'.$FieldUpper;
	$descLangText = 'CFG_DESCR_'.$FieldUpper;
	echo '	<tr><td '.STYLE_CONFIG_KEY.'>';
	echo '	<strong>'.JText::_($titleLangText).'</strong><br />';
	echo 			  JText::_($descLangText);
	echo '		</td><td class="paramlist_value">';
	echo '		<input type="Text" size="25" name="conf_'.$fieldname.'" value = "'.$spgm_cfg['conf'][$fieldname].'">';
	echo '		</td></tr>';
}

// -------------------------------------------------------------------------------
// Draw a config item list field for showConfEditor() 
//
function drawConfListField($fieldname,$listItems)
{
global $spgm_cfg;
	$FieldUpper = strtoupper($fieldname);
	$titleLangText = 'CFG_TITLE_'.$FieldUpper;
	$descLangText = 'CFG_DESCR_'.$FieldUpper;
	$value = $spgm_cfg['conf'][$fieldname];
	if ($spgm_cfg['conf'][$fieldname] === true)
		$value = 'true';
	if ($spgm_cfg['conf'][$fieldname] === false)
		$value = 'false';
	echo '	<tr><td '.STYLE_CONFIG_KEY.'>';
	echo '	<strong>'.JText::_($titleLangText).'</strong><br />';
	echo 			  JText::_($descLangText);
	echo '		</td><td class="paramlist_value">';
	echo '      <select name="conf_'.$fieldname.'">';
	foreach ($listItems as $listItem)
		{
		if ($listItem == $value)
			$selected = ' selected';
		else
			$selected = '';
		echo '<option value="'.$listItem.'"'.$selected.'>'.$listItem.'</option>';
		}
	echo '		</select>';
	echo '		</td></tr>';
}

// -------------------------------------------------------------------------------
// Draw the form to create or edit an spgm.conf file
// Returns true for success, false for failure
//
function showConfEditor($relDir)
{
define('GALICON_NONE', "GALICON_NONE");
define('GALICON_RANDOM', "GALICON_RANDOM");
define('ORIGINAL_SIZE', "ORIGINAL_SIZE");
define('ORIENTATION_TOPBOTTOM', "ORIENTATION_TOPBOTTOM");
define('ORIENTATION_LEFTRIGHT', "ORIENTATION_LEFTRIGHT");
define('SORTTYPE_CREATION_DATE', "SORTTYPE_CREATION_DATE");
define('SORTTYPE_NAME', "SORTTYPE_NAME");
define('SORT_ASCENDING', "SORT_ASCENDING");
define('SORT_DESCENDING', "SORT_DESCENDING");
define('RIGHT', "RIGHT");
define('BOTTOM', "BOTTOM");
define('LANG_AUTO',"LANG_AUTO");
global $spgm_cfg;

	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	$absDir = getAbsolutePath($relDir);
	if (!is_file($absDir.SPGM_CONF))
		return false;
		
	// set defaults values for all items
	
	$spgm_cfg = array();
	$spgm_cfg['conf']['newStatusDuration']       = 30;
	$spgm_cfg['conf']['thumbnailsPerPage']       = 9;
	$spgm_cfg['conf']['thumbnailsPerRow']        = 3;
	$spgm_cfg['conf']['galleryListingCols']      = 2;
	$spgm_cfg['conf']['galleryCaptionPos']       = "RIGHT";
	$spgm_cfg['conf']['subGalleryLevel']         = 1;
	$spgm_cfg['conf']['galleryOrientation']      = "ORIENTATION_TOPBOTTOM";
	$spgm_cfg['conf']['gallerySortType']         = "SORTTYPE_NAME";
	$spgm_cfg['conf']['gallerySortOptions']      = "SORT_ASCENDING";
	$spgm_cfg['conf']['pictureSortType']         = "SORTTYPE_NAME";
	$spgm_cfg['conf']['pictureSortOptions']      = "SORT_ASCENDING";
	$spgm_cfg['conf']['pictureInfoedThumbnails'] = "false";
	$spgm_cfg['conf']['captionedThumbnails']     = "false";
	$spgm_cfg['conf']['pictureCaptionedThumbnails'] = "false";
	$spgm_cfg['conf']['filenameWithThumbnails']  = "false";
	$spgm_cfg['conf']['filenameWithPictures']    = "false";
	$spgm_cfg['conf']['enableSlideshow']         = "false";
	$spgm_cfg['conf']['enableDropShadows']       = "true";
	$spgm_cfg['conf']['fullPictureWidth']        = "ORIGINAL_SIZE";
	$spgm_cfg['conf']['fullPictureHeight']       = "ORIGINAL_SIZE";
	$spgm_cfg['conf']['popupOverFullPictures']   = "false";
	$spgm_cfg['conf']['popupPictures']           = "false";
	$spgm_cfg['conf']['popupFitPicture']         = "true";
	$spgm_cfg['conf']['popupWidth']              = 750;
	$spgm_cfg['conf']['popupHeight']             = 600;
	$spgm_cfg['conf']['filters']                 = '';
	$spgm_cfg['conf']['exifInfo']                = array();
	$spgm_cfg['conf']['zoomFactors']             = array();
	$spgm_cfg['conf']['galleryIconType']         = "GALICON_NONE";
	$spgm_cfg['conf']['galleryIconHeight']       = "ORIGINAL_SIZE";
	$spgm_cfg['conf']['galleryIconWidth']        = "ORIGINAL_SIZE";
	$spgm_cfg['conf']['stickySpgm']              = "false";
	$spgm_cfg['conf']['theme']                   = "default";
	$spgm_cfg['conf']['language']                = "LANG_AUTO";
	$spgm_cfg['conf']['multiLanguageCaptions']   = "false";
	$spgm_cfg['conf']['spgmLink']                = "true";

	// pull in the conf file
	
	require_once($absDir.SPGM_CONF);
	
	echo '<h3>'.SPGM_CONF.'</h3>';
	echo '<form action="index.php" method="post" name="adminForm">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo '<input type="hidden" name="hidemainmenu" value="0" />';
	echo '<table '.STYLE_CONFIG_TABLE.'>';
	drawConfTextField('newStatusDuration');
	drawConfTextField('thumbnailsPerPage');
	drawConfTextField('thumbnailsPerRow');
	drawConfTextField('galleryListingCols');
	drawConfListField('galleryCaptionPos',array('RIGHT','BOTTOM'));
	drawConfTextField('subGalleryLevel');
	drawConfListField('galleryOrientation',array('ORIENTATION_TOPBOTTOM','ORIENTATION_LEFTRIGHT'));
	drawConfListField('gallerySortType',array('SORTTYPE_CREATION_DATE','SORTTYPE_NAME'));
	drawConfListField('gallerySortOptions',array('SORT_ASCENDING','SORT_DESCENDING'));
	drawConfListField('pictureSortType',array('SORTTYPE_CREATION_DATE','SORTTYPE_NAME'));
	drawConfListField('pictureSortOptions',array('SORT_ASCENDING','SORT_DESCENDING'));
	drawConfListField('pictureInfoedThumbnails',array('true','false'));
	drawConfListField('captionedThumbnails',array('true','false'));
	drawConfListField('pictureCaptionedThumbnails',array('true','false'));
	drawConfListField('filenameWithThumbnails',array('true','false'));
	drawConfListField('filenameWithPictures',array('true','false'));
	drawConfListField('enableSlideshow',array('true','false'));
	drawConfListField('enableDropShadows',array('true','false'));
	drawConfTextField('fullPictureWidth');
	drawConfTextField('fullPictureHeight');
	drawConfListField('popupOverFullPictures',array('true','false'));
	drawConfListField('popupPictures',array('true','false'));
	drawConfTextField('popupWidth');
	drawConfTextField('popupHeight');
	drawConfListField('popupFitPicture',array('true','false'));
	drawConfTextField('filters');
	drawConfArrayField('zoomFactors');
	drawConfArrayField('exifInfo');
	drawConfListField('galleryIconType',array('GALICON_NONE','GALICON_RANDOM'));
	drawConfTextField('galleryIconHeight');
	drawConfTextField('galleryIconWidth');
	drawConfListField('stickySpgm',array('true','false'));
	drawConfTextField('language');
	drawConfTextField('theme');
	drawConfListField('multiLanguageCaptions',array('true','false'));
	drawConfListField('spgmLink',array('true','false'));
	echo '</table>';
	echo '</form>';

	return true;
}

// -------------------------------------------------------------------------------
// Show the heading
//
function showHeading($relDir)
{
	if ($relDir == '')
		$dir = JText::_('TXT_GALLERY_BASE_DIR');
	else
		$dir = utf8_encode($relDir);
	echo "<h2>$dir</h2>";
}

// -------------------------------------------------------------------------------
// The page must have a form called "adminForm" with a variable called "task"
// So that the javascript behind the toolbar buttons will work
// But we must not have multiple adminForm! so only call this when there is no other form data
//
function adminForm($relDir='')
{
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	echo '<form action="index.php" method="get" name="adminForm">';
	echo '<input type="hidden" name="option" value="com_spgm" />';
	echo '<input type="hidden" name="dir" value="'.$encRelDir.'" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="hidemainmenu" value="0">';
	echo '</form>';
	
}

// -------------------------------------------------------------------------------
// Show the gallery directories ($relDir is relative to gallery base)
//
function showDirList($relDir='')
{
	$absDir = getAbsolutePath($relDir);
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));
	
	echo "\n".'<table class=adminlist>';
	echo '<tr><td>';
	if ($absDir != GALLERY_BASE_DIR)	// if not base dir show up link
		{
		echo '<a href="index.php?option=com_spgm&dir='.$encRelDir.'&task=up"'.'">';
		echo '<img border="0" width="22" height="22" align="left" src="'.ADMIN_WEB_IMAGE_DIR.'up.png" alt="" title="'.JText::_('TXT_DIR_UP').'" />';
		echo '&nbsp;'.JText::_('TXT_DIR_UP');
		echo '</a>';
		}
	echo '</td><td>'.JText::_('TXT_IMAGES').'</td><td>'.JText::_('TXT_THUMBNAILS').'</td><td>'.JText::_('TXT_DIRECTORIES').'</td><td>'.JText::_('TXT_FILES').'</td><td>'.JText::_('TXT_ACTION').'</td></tr>';
	$handle = opendir($absDir);
	if (!$handle)
		reportError(JText::_('E_DIR'),$relDir,$relDir);

	while (($file = readdir($handle)) != false)
		{
		if ((is_dir($absDir.$file)) && ($file !='.') && ($file != '..'))	// directories only
			{
    		$textFiles = array();
    		$imageFiles = array();
    		$imageCount = 0;
    		$thumbCount = 0;
    		$textCount = 0;
    		$dirCount = 0;
    		$configFile = false;
			getDirInfo($absDir.$file,$textFiles,$imageFiles,$imageCount,$thumbCount,$textCount,$dirCount,$configFile);
			sort($textFiles);
			if ($relDir == '')
				$dirLink = $file;
			else
				$dirLink = $relDir.'/'.$file;
			$encFile = implode("/", array_map("rawurlencode", explode("/", $file)));
			$encDirLink = implode("/", array_map("rawurlencode", explode("/", $dirLink)));
			echo "\n".'<tr><td>';
			echo '<a href="index.php?option=com_spgm&dir='.$encDirLink.'">';
			echo '<img border="0" width="22" height="22" align="left" src="'.ADMIN_WEB_IMAGE_DIR.'dir.png" alt="" />';
			echo '&nbsp;'.utf8_encode($file);
			echo '</a>';
			echo '<td>'.$imageCount.'</td><td>'.$thumbCount.'</td><td>'.$dirCount.'</td><td>';
			foreach($textFiles as $txtfile)
				echo $txtfile.'&nbsp;&nbsp;&nbsp;';
			echo '</td><td>';
			echo '<a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$encFile.'&task=delete_dir" '; 
			if (DELETE_NON_EMPTY_DIRECTORIES)
				echo 'onclick="javascript:return confirm(\''.JText::_('TXT_DELETE_DIR_CONFIRMATION').'\')"';
			echo '><img border="0" width="22" height="22" src="'.ADMIN_WEB_IMAGE_DIR.'delete.png" alt="" title="'.JText::_('TXT_DELETE').'" />';
			echo '&nbsp;'.JText::_('TXT_DELETE');
			echo '</a>&nbsp;&nbsp;';
			echo '<a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$encFile.'&task=rename_dir_form" '; 
			echo '<img border="0" width="22" height="22" src="'.ADMIN_WEB_IMAGE_DIR.'rename.png" alt="" title="'.JText::_('TXT_RENAME').'" />';
			echo '&nbsp;'.JText::_('TXT_RENAME');
			echo '</a></td></tr>';
			}
		}
	echo '</table>';
	closedir ($handle);
}

// -------------------------------------------------------------------------------
// Show the thumbnails in the specified directory ($relDir is relative to gallery base)
//
function showThumbs($relDir)
{
	$absDir = getAbsolutePath($relDir);		// $absDir always has a trailing '/'
	$encRelDir = implode("/", array_map("rawurlencode", explode("/", $relDir)));

	?>		
	<script>function spgmPopup(url, x, y)
	{document.open(url, '', 'width='+x+',height='+y+',scrollbars=1,location=0,menubar=0,resizable=1');}	
	</script>
	<?php

	// get arrays of filenames
	
	$textFiles = array();
	$imageFiles = array();
	$imageCount = 0;
	$thumbCount = 0;
	$textCount = 0;
	$dirCount = 0;
	$filesize = '';
	$imageX = 0;
	$imageY = 0;
	$configFile;
	getDirInfo($absDir,$textFiles,$imageFiles,$imageCount,$thumbCount,$textCount,$dirCount,$configFile);
	sort($imageFiles);
	sort($textFiles);
	$rowCount = 0;
	
	echo "\n<br /><hr><hr><br />".'<table class="adminlist">'."\n";
	foreach($imageFiles as $largeImage)
		{
		$encLargeImage = rawurlencode($largeImage);
		if ($relDir == '')
			$dirLink = '';
		else
			$dirLink = $relDir.'/';
		$encDirLink = implode("/", array_map("rawurlencode", explode("/", $dirLink)));
		$encWebImage = GALLERY_WEB_BASE_DIR.$encDirLink.$encLargeImage;
		$fsThumb = $absDir.THUMBNAIL_PREFIX.$largeImage;
		if (file_exists($fsThumb))
			$encWebThumb = GALLERY_WEB_BASE_DIR.$encDirLink.THUMBNAIL_PREFIX.$encLargeImage;
		else
			$encWebThumb = ADMIN_WEB_IMAGE_DIR.'no_thumb.png';
		
		$filetype = getFileInfo($absDir,$largeImage,$filesize,$imageX,$imageY);
		
		if ($imageX > MAX_POPUP_X)
			$popupX = MAX_POPUP_X;
		else
			$popupX = $imageX + 20;
			
		if ($imageY > MAX_POPUP_Y)
			$popupY = MAX_POPUP_Y;
		else
			$popupY = $imageY + 20;
			
		if ($rowCount == 0)
			echo '<tr>';
		echo "\n".'<td valign="top" width="16%">';
		echo "\n".' <a href="#" onclick="spgmPopup(\''.addslashes($encWebImage).'\', '.$popupX.','.$popupY.');">';
		echo "\n".'  <img hspace="0" vspace="0" border="0" src="'.$encWebThumb.'"></a></td>';
		echo "\n".'<td valign="top" width="16%"><b>'.utf8_encode($largeImage).'</b><br />';
		echo $filesize.'<br />'.$imageX.'x'.$imageY.'<br />';
		echo "\n".' <a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$encLargeImage.'&task=delete_file"> '; 
		echo JText::_('TXT_DELETE').'</a><br />';
		echo "\n".' <a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$encLargeImage.'&task=rename_file_form"> '; 
		echo JText::_('TXT_RENAME')."</a></td>\n";
		$rowCount++;
		if ($rowCount == 3)
			{
			echo "</tr>\n";
			$rowCount=0;
			}
		}

	// sort the text file names and display thumbnails

	sort($textFiles);
	foreach($textFiles as $textFile)
		{
		$filetype = getFileInfo($absDir,$textFile,$filesize,$imageX,$imageY);
		if ($rowCount == 0)
			echo "\n".'<tr>';
		echo "\n".'<td valign="top" width="16%">';
		echo "\n".' <a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$textFile.'&task=edit_text_form"> '; 
		echo textImage($textFile);
		echo '</a></td>';
		echo "\n".'<td valign="top" width="16%"><b>'.$textFile.'</b><br />';
		echo $filesize.'<br />';
		echo "\n".' <a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$textFile.'&task=delete_file"> '; 
		echo JText::_('TXT_DELETE').'</a><br />';
		echo "\n".' <a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.$textFile.'&task=rename_file_form"> '; 
		echo JText::_('TXT_RENAME')."</a></td>\n";
		$rowCount++;
		if ($rowCount == 3)
			{
			echo '</tr>';
			$rowCount=0;
			}
		}
		
	// if there's a spgm.conf file, show it
	
	if ($configFile)
		{
		if ($rowCount == 0)
			echo "\n".'<tr>';
		echo '<td valign="top" width="16%">';
		echo '<a href="index.php?option=com_spgm&dir='.$encRelDir.'&task=edit_conf_form"> '; 
		echo '<img border="0" width="100" height="75" align="left" src="'.ADMIN_WEB_IMAGE_DIR.'conf.png" alt="" />';
		echo '</a></td>';
		echo '<td valign="top" width="16%"><b>'.SPGM_CONF.'</b><br />';
		if ($relDir != '')
			{				// don't show delete link for root conf file
			echo '<a href="index.php?option=com_spgm&dir='.$encRelDir.'&obj_name='.SPGM_CONF.'&task=delete_file"> '; 
			echo JText::_('TXT_DELETE').'</a>';
			}
		echo '</td>';
		$rowCount++;
		if ($rowCount == 3)
			{
			echo '</tr>';
			$rowCount=0;
			}
		}

	if (($rowCount > 0) and ($rowCount < 3))
		{
		$colsleft = (3 - $rowCount) * 2;
		echo '<td colspan="'.$colsleft.'"></td>';
		echo '</tr>';
		}
	echo '</table>';
}

?>