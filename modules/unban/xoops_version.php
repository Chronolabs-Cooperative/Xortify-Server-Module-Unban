<?php
if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}
$modversion['dirname'] = basename(dirname(__FILE__));
$modversion['name'] = ucfirst(basename(dirname(__FILE__)));
$modversion['version']     = "2.01";
$modversion['releasedate'] = "2010-03-24";
$modversion['status']      = "Stable";
$modversion['description'] = _MI_UNBAN_DESC;
$modversion['credits']     = "Unbanner Profile";
$modversion['author']      = "Wishcraft";
$modversion['help']        = "";
$modversion['license']     = "This unbanning profiler.";
$modversion['official']    = 0;
$modversion['image']       = "images/unban_slogo.png";

//Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "unban_search";

// Main
$modversion['hasMain'] = 1;

// Main Menu
$modversion['sub'][1]['name'] = _UNBAN_MI_LATESTUNBANS;
$modversion['sub'][1]['url'] = "index.php?op=latest&num=35";
$modversion['sub'][2]['name'] = _UNBAN_MI_UNBAN;
$modversion['sub'][2]['url'] = "index.php?op=unban";	
// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Update
$modversion['onUpdate'] = "include/upgrade.php";

// Mysql file
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file
$modversion['tables'][0] = "unban_member";

$i = 0;
// Config items
$i++;
$modversion['config'][$i]['name'] = 'public_key';
$modversion['config'][$i]['title'] = '_UNBAN_MI_PUBLICKEY';
$modversion['config'][$i]['description'] = '_UNBAN_MI_PUBLICKEY_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '6Lf10wcAAAAAAGnRb892iZ1cjJO4DZvzle97Qqt9';

$i++;
$modversion['config'][$i]['name'] = 'private_key';
$modversion['config'][$i]['title'] = '_UNBAN_MI_PRIVATEKEY';
$modversion['config'][$i]['description'] = '_UNBAN_MI_PRIVATEKEY_DESC';
$modversion['config'][$i]['formtype'] = 'text';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '6Lf10wcAAAAAAApaZlJbHHd1hvhVhfdt81sKYfb1';

$i++;
$modversion['config'][$i]['name'] = 'display_name';
$modversion['config'][$i]['title'] = '_UNBAN_MI_DISPLAYNAME';
$modversion['config'][$i]['description'] = '_UNBAN_MI_DISPLAYNAME_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'Currently Latest Banned IP & Network names';

$i++;
$modversion['config'][$i]['name'] = 'display_text';
$modversion['config'][$i]['title'] = '_UNBAN_MI_DISPLAYTEXT';
$modversion['config'][$i]['description'] = '_UNBAN_MI_DISPLAYTEXT_DESC';
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'This is the top list of banned IP\'s they are the latest on the network and are available with the unbanned option to remove from the network banned list!';

$i++;
$modversion['config'][$i]['name'] = 'htaccess';
$modversion['config'][$i]['title'] = '_UNBAN_MI_HTACCESS';
$modversion['config'][$i]['description'] = '_UNBAN_MI_HTACCESS_DESC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;
// Templates
$i = 0;

$i++;
$modversion['templates'][$i]['file'] = 'unban_index.html';
$modversion['templates'][$i]['description'] = 'Picture Profile Screen';

$i++;
$modversion['templates'][$i]['file'] = 'unban_member.html';
$modversion['templates'][$i]['description'] = 'Member Picture Profile Screen';

$i++;
$modversion['templates'][$i]['file'] = 'unban_member_display.html';
$modversion['templates'][$i]['description'] = 'Member Ban Display Profile Screen';

$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'id';
$modversion['comments']['pageName'] = 'index.php';
$modversion['comments']['extraParams'] = array('op');

?>
