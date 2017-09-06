<?php
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for unban Profiler
 * @author Simon Roberts (simon@chronolabs.org.au)
 * @copyright copyright (c) 2000-2009 XOOPS.org
 * @package kernel
 */
class unbanCategories extends XoopsObject
{

    function unbanCategories($fid = null)
    {
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_name', XOBJ_DTYPE_TXTBOX, null, true, 128);	
        $this->initVar('category_type', XOBJ_DTYPE_ENUM, null, true, false, false, file($GLOBALS['xoops']->path('/include/libs/category-enumeration.txt')));
    }

}


/**
* XOOPS unban Profiler handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@chronolabs.org.au>
* @package kernel
*/
class unbanCategoriesHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "ban_categories", 'unbanCategories', "category_id", "category_name");
    }
	
}
?>