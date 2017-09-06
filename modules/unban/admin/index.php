<?php

    
    include(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'mainfile.php');
    include(dirname(dirname(dirname(__DIR__))). 'include' . DIRECTORY_SEPARATOR . 'cp_functions.php');
    include(dirname(__DIR__). 'include' . DIRECTORY_SEPARATOR . 'functions.php');
    include(dirname(__DIR__). 'include' . DIRECTORY_SEPARATOR . 'forms.php');

	
	xoops_cp_header();
	
	switch($_REQUEST['op']) {
	case "members":	
	
		switch ($_REQUEST['fct'])
		{
			case "edit":
				unban_adminMenu(2);
				echo search_members_form();
				break;
			case "new":
				unban_adminMenu(3);
				echo search_members_form();
				break;
			case "unban":

				$banmemberhandler =& xoops_getmodulehandler('members','ban');
				$unbanmemberhandler =& xoops_getmodulehandler('members','unban');
	
				foreach( $_REQUEST['item'] as $key => $id ) {
					
					$ban = $banmemberhandler->get( $id );
					$unban = $unbanmemberhandler->create();
					$unban->assignVars((array)$ban);
					if (!$banmemberhandler->delete( $ban, true ))
						redirect_header('index.php', 3, 'Ban was unremovable!');			
					if (!$unbanmemberhandler->insert($unban))
						redirect_header('index.php', 3, 'Unban was not saved!');			
				}
	
				redirect_header('index.php', 3, 'Bans successfully removed!');
				exit(0);

			case "search":	
		
				include_once( $GLOBALS['xoops']->path('/modules/unban/class/recaptchalib.php') );
				$resp = recaptcha_check_answer ($GLOBALS['xoopsModuleConfig']['private_key'], $_SERVER["REMOTE_ADDR"],
												$_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
					redirect_header('index.php', 5, 'You didn\'t solve the puzzel correctly');
					exit(0);
				}
						
				$criteria = new CriteriaCompo();
		
				if (!empty($_REQUEST['ip4']))
					$criteria->add(new Criteria('ip4', '%'.$_REQUEST['ip4'].'%', 'LIKE'), 'OR');
				if (!empty($_REQUEST['ip6']))
					$criteria->add(new Criteria('ip6', '%'.$_REQUEST['ip6'].'%', 'LIKE'), 'OR');
				if (!empty($_REQUEST['proxy_ip4']))
					$criteria->add(new Criteria('proxy-ip4', '%'.$_REQUEST['proxy-ip4'].'%', 'LIKE'), 'OR');
				if (!empty($_REQUEST['proxy_ip6']))
					$criteria->add(new Criteria('proxy-ip6', '%'.$_REQUEST['proxy-ip6'].'%', 'LIKE'), 'OR');
				if (!empty($_REQUEST['network_addy']))
					$criteria->add(new Criteria('network-addy', '%'.$_REQUEST['network_addy'].'%', 'LIKE'), 'OR');
		
				$criteria->setOrder('DESC');
				$criteria->setSort('made');
				if ($_REQUEST['num']<30)
					$criteria->setLimit($_REQUEST['num']);
				else
					$criteria->setLimit(15);
	
				$membershandler = xoops_getmodulehandler('members','ban');
	
				$bans = $membershandler->getObjects($criteria, true);
	
				$lbn = array();
				foreach($bans as $key => $ban){
					$ii++;
					$lbn[$ii]['id'] = $ban->getVar('member_id');
					$lbn[$ii]['ip'] = $ban->getVar('ip4');
					if (empty($lbn[$ii]['ip']))
						$lbn[$ii]['ip'] = $ban->getVar('ip6');
					$lbn[$ii]['network_addy'] = $ban->getVar('network-addy');
					$lbn[$ii]['when'] = date(_MEDIUMDATESTRING, $ban->getVar('made'));				
				}
?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo XOOPS_URL."/modules/unban/templates/unban_style.css"; ?>" /> 
<div class="div_profile" id="div_profile">
	<form enctype="application/x-www-form-urlencoded" method="post" id="unbanning" name="unbanning">
	<?php foreach($lbn as $key => $banned) { ?>
    	<div class="div_item" id="div_item">
        	<div class="div_check" id="div_check"><input type="checkbox" name="item[<?php echo $banned['id']; ?>]"></div>
       	  	<div class="div_ip" id="div_ip"><?php echo $banned['ip']; ?></div>
            <div class="div_netaddy" id="div_netaddy"><?php echo $banned['network_addy']; ?></div>
            <div class="div_when" id="div_when"><?php echo $banned['when']; ?></div>
        </div>
    <?php } ?>
    <input name="op" type="hidden" value="member" />
    <input name="fct" type="hidden" value="unban" />
    <p><input name="submit" type="submit" value="Submit" /></p>
    </form>
</div>
<?php
                exit(0);
				break;

			default:
				unban_adminMenu(2);
				echo search_members_form();
				
		}
		break;
	case "categories":	
	
		switch ($_REQUEST['fct'])
		{
			case "edit":
				unban_adminMenu(3);
				edit_categories_form();
				break;
			case "new":
				unban_adminMenu(4);
				edit_categories_form();
				break;
			case "delete":
				$id = intval($_REQUEST['id']);				
				$categorieshandler = xoops_getmodulehandler('categories','unban');
				$categories = $categorieshandler->get($id);	
				if ($categorieshandler->delete($categories, true))			
					redirect_header('index.php', 3, 'Category Item Delete Successfully');
				else
					redirect_header('index.php', 3, 'Category Item Delete Unsuccessfully');
				break;
				exit;
			case "save":
	
				$id = intval($_REQUEST['id']);				
				$categorieshandler = xoops_getmodulehandler('categories','unban');
				if ($id>0)
					$categories = $categorieshandler->get($id);	
				else
					$categories = $categorieshandler->create();	
	
				$categories->setVar('category_name', $_REQUEST['category_name']);

				if ($categorieshandler->insert($categories))			
					redirect_header('index.php', 3, 'Category Item Updated Successfully');
				else
					redirect_header('index.php', 3, 'Category Item Updated Unsuccessfully');
					
				exit;
				break;
			default:
				unban_adminMenu(3);
				sel_categories_form();
				
		}
		break;
	default:

		unban_adminMenu(1);
		sel_categories_form();
		search_members_form();
		
	}
	
	unban_footer_adminMenu();
	xoops_cp_footer();
?>