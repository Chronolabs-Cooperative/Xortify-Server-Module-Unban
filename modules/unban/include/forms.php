<?php

	include_once $GLOBALS['xoops']->path("/class/xoopsformloader.php");
	
	// Include Internal Form Objects
	include_once $GLOBALS['xoops']->path("/modules/unban/class/formselectcategory.php");
	include_once $GLOBALS['xoops']->path("/modules/unban/class/formselectmember.php");
	include_once $GLOBALS['xoops']->path("/modules/unban/class/formrecaptcha.php");
	
	// Include Forms
	include_once $GLOBALS['xoops']->path("/modules/unban/include/members.forms.php");
	include_once $GLOBALS['xoops']->path("/modules/unban/include/categories.forms.php");
	$module_handler =& xoops_gethandler( 'module' );
	$config_handler =& xoops_gethandler( 'config' );
	$xoModule =& $module_handler->getByDirname('unban');
	$GLOBALS['xoopsModuleConfig'] = $config_handler->getConfigList( $xoModule->getVar( 'mid' ) );
	
?>