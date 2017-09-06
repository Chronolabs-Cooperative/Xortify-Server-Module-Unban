<?php
	include ('../../mainfile.php');
	
	if ($GLOBALS['xoopsModuleConfig']['htaccess']==true) {
		$url = XOOPS_URL.'/unban/'.basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'];
		if ($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']!=$url && strpos($_SERVER['REQUEST_URI'], 'odules/')>0) {
			header( "HTTP/1.1 301 Moved Permanently" ); 
			header('Location: '.$url);
			exit(0);
		}
	}

	include('include/functions.php');	
	include('include/forms.php');
		
	switch ((string)$_REQUEST['op']) {
	default:
	case "latest":
		
		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=XOOPS_URL . '/unban/latest/'.(isset($_REQUEST['num'])?$_REQUEST['num']:'45') . '/ipsec.html'){
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.XOOPS_URL . '/unban/latest/'.(isset($_REQUEST['num'])?$_REQUEST['num']:'45') . '/ipsec.html');
				exit(0);
			}
		}
			
		$membershandler =& xoops_getmodulehandler('members','unban');
		$criteria = new CriteriaCompo();
		$criteria->setOrder('DESC');
		$criteria->setSort('made');
		if (isset($_REQUEST['num'])&&$_REQUEST['num']<200)
			$criteria->setLimit(intval($_REQUEST['num']));
		else
			$criteria->setLimit(45);
			
		$bans = $membershandler->getObjects($criteria, true);

		$xoopsOption['template_main'] = 'ban_member.html';
		include $GLOBALS['xoops']->path('/header.php');								
		$xoTheme->addStylesheet(XOOPS_URL."/modules/ban/language/".$GLOBALS['xoopsConfig']['language']."/unban_style.css");
		$lbn = array();
		foreach($bans as $key => $ban){
			$ii++;
			$lbn[$ii]['id'] = $ban->getVar('member_id');
			$lbn[$ii]['ip'] = $ban->ipaddy();
			$lbn[$ii]['network_addy'] = $ban->getVar('network-addy');
			$lbn[$ii]['email'] = $ban->getVar('email');
			$lbn[$ii]['uname'] = $ban->getVar('uname');	
			$lbn[$ii]['countrycode'] = $ban->getVar('country-code');
			$lbn[$ii]['countryname'] = $ban->getVar('country-name');
			$lbn[$ii]['postcode'] = $ban->getVar('postcode');		
			$lbn[$ii]['when'] = date(_MEDIUMDATESTRING, $ban->getVar('made'));				
		}
		$GLOBALS['xoopsTpl']->assign('php_self', $_SERVER['PHP_SELF']);
		$GLOBALS['xoopsTpl']->assign('latestbans', $lbn);		
		$GLOBALS['xoopsTpl']->assign('display_name', $GLOBALS['xoopsModuleConfig']['display_name']);		
		$GLOBALS['xoopsTpl']->assign('display_text', $GLOBALS['xoopsModuleConfig']['display_text']);				
		include $GLOBALS['xoops']->path('/footer.php');		
		exit(0);
		break;
	case "member":
		
		xoops_loadLanguage('main', 'ban');
		
		$membershandler = xoops_getmodulehandler('members','unban');
		$ban = $membershandler->get($_REQUEST['id']);
		
		
		if (!is_object($ban)) {
			header( "HTTP/1.1 301 Moved Permanently" );
			header('Location: '.XOOPS_URL.'/modules/unban/?op=latest&num=35');
			exit(0);
		}
		
		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=$ban->getURL() ) {
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.$ban->getURL());
				exit(0);
			}
		}
		
		$dontinclude = array();
		$dontinclude[] = 'suid';
		$xoopsOption['template_main'] = 'ban_member_display.html';
		include $GLOBALS['xoops']->path('/header.php');								
		$xoTheme->addStylesheet(XOOPS_URL."/modules/unban/language/".$GLOBALS['xoopsConfig']['language']."/unban_style.css");
		foreach($ban->toArray() as $field => $value) {
			if ($field!='comments'&&!empty($value)&&!in_array($field, $dontinclude)&&$value!='00:00'&&(intval($value)!=0||is_string($value))) {
				$titleconst = '_BAN_MF_'. strtoupper(str_replace('-', '_', $field));
				$GLOBALS['xoopsTpl']->append('ban', array('title'=>defined($titleconst)?constant($titleconst):$titleconst,
														  'value'=>$value));
			}
		}		
			
		$GLOBALS['xoopsTpl']->assign('display_name', $GLOBALS['xoopsModuleConfig']['display_name']);		
		$GLOBALS['xoopsTpl']->assign('display_text', $GLOBALS['xoopsModuleConfig']['display_text']);

		include_once XOOPS_ROOT_PATH."/modules/tag/include/tagbar.php";
		$xoopsTpl->assign('tagbar', tagBar($ban->getVar('member_id'), $ban->getVar('category_id')));
		$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $ban->ipaddy() . ' :: ' . $ban->getTitle());
		include $GLOBALS['xoops']->path('/include/comment_view.php');		
		include $GLOBALS['xoops']->path('/footer.php');		
		exit(0);
		break;		
		
	case "members":
		
		if ( $_REQUEST['fct']=='search' ) {

			xoops_load("captcha");
 			$xoopsCaptcha =& XoopsCaptcha::getInstance();
			if (! $xoopsCaptcha->verify() ) {
				redirect_header('index.php', 5, 'You didn\'t solve the puzzel correctly');
				exit(0);
			}
				
					
			$criteria = new CriteriaCompo();
			
			if (!empty($_REQUEST['ip4']))
				$criteria->add(new Criteria('`ip4`', '%'.$_REQUEST['ip4'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['ip6']))
				$criteria->add(new Criteria('`ip6`', '%'.$_REQUEST['ip6'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['proxy_ip4']))
				$criteria->add(new Criteria('`proxy-ip4`', '%'.$_REQUEST['proxy-ip4'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['proxy_ip6']))
				$criteria->add(new Criteria('`proxy-ip6`', '%'.$_REQUEST['proxy-ip6'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['network_addy']))
				$criteria->add(new Criteria('`network-addy`', '%'.$_REQUEST['network_addy'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['uname']))
				$criteria->add(new Criteria('`uname`', '%'.$_REQUEST['uname'].'%', 'LIKE'), 'OR');
			if (!empty($_REQUEST['email']))
				$criteria->add(new Criteria('`email`', '%'.$_REQUEST['email'].'%', 'LIKE'), 'OR');
				
			$criteria->setOrder('DESC');
			$criteria->setSort('made');
			if ($_REQUEST['num']<30)
				$criteria->setLimit($_REQUEST['num']);
			else
				$criteria->setLimit(15);
				
			

			$membershandler = xoops_getmodulehandler('members','ban');

			$bans = $membershandler->getObjects($criteria, true);

			$xoopsOption['template_main'] = 'unban_member.html';
			include $GLOBALS['xoops']->path('/header.php');								
			$xoTheme->addStylesheet(XOOPS_URL."/modules/ban/language/".$GLOBALS['xoopsConfig']['language']."/unban_style.css");
			$lbn = array();
			foreach($bans as $key => $ban){
				$ii++;
				$lbn[$ii]['id'] = $ban->getVar('member_id');
				$lbn[$ii]['ip'] = $ban->ipaddy();
				$lbn[$ii]['email'] = $ban->getVar('email');
				$lbn[$ii]['uname'] = $ban->getVar('uname');	
				$lbn[$ii]['network_addy'] = $ban->getVar('network-addy');
				$lbn[$ii]['countrycode'] = $ban->getVar('country-code');
				$lbn[$ii]['countryname'] = $ban->getVar('country-name');
				$lbn[$ii]['postcode'] = $ban->getVar('postcode');
					
				$lbn[$ii]['when'] = date(_MEDIUMDATESTRING, $ban->getVar('made'));				
			}
			$GLOBALS['xoopsTpl']->assign('display_name', $GLOBALS['xoopsModuleConfig']['display_name']);		
			$GLOBALS['xoopsTpl']->assign('display_text', $GLOBALS['xoopsModuleConfig']['display_text']);					
			$GLOBALS['xoopsTpl']->assign('latestbans', $lbn);		
			include $GLOBALS['xoops']->path('/footer.php');		
			exit(0);
			break;
			
		} elseif ( $_REQUEST['fct']=='unban' ) {
		
			xoops_load('cache');
			if (!$unbanitems = XoopsCache::read('current_unban_on_servers'))
				$unbanitems = array();
				
			$banmemberhandler =& xoops_getmodulehandler('members','ban');
			$unbanmemberhandler =& xoops_getmodulehandler('members','unban');
			$servershandler =& xoops_getmodulehandler('servers','xortify');
			
			$modulehandler = xoops_gethandler('module');
			
			$xoBanModule = $modulehandler->getByDirname('ban');
			$xoUnbanModule = $modulehandler->getByDirname('unban');
			 
			foreach( $_REQUEST['item'] as $key => $id ) {
				
				$ban = $banmemberhandler->get( $key );
				
				foreach($servershandler->getObjects(new Criteria('`online`', true)) as $sid => $server) {
					$rui = parse_url($server->getVar('server'));
					$unbanitems[$rui['host']][$ban->ipaddy()] = $ban->toArray();
				}	
								
				$unban = $unbanmemberhandler->create();

				$unban->setVars($ban->toArray());
				$unban->setVar('made', time());
			
				if (!$banmemberhandler->delete( $ban, true )) {
					redirect_header('index.php', 3, 'Ban was unremovable!');
					exit(0);
				}			
				if (!$unbanmemberhandler->insert($unban)) {
					redirect_header('index.php', 3, 'Unban was not saved!');
					exit(0);
				}
				$sql = "UPDATE ".$xoopsDB->prefix('xoopscomments'). " set `com_modid` = '".$xoUnbanModule->getVar('mid')."' WHERE `com_modid` = '".$xoBanModule->getVar('mid')."' AND `com_itemid` = '".$ban->getVar('member_id')."'" ;
				$xoopsDB->queryF($sql);
					
			}
			
			XoopsCache::write('current_unban_on_servers', $unbanitems, 3600*24*7*4*12);
			
			redirect_header('index.php', 3, 'Bans successfully removed!');
			exit(0);

		} else {
	
			redirect_header('index.php', 3, 'Member Item Updated Unsuccessfully');
			exit(0);
	
		}
		break;
	
	case "unban":
		if ($GLOBALS['xoopsModuleConfig']['htaccess']==true && !isset($_GET['extra'])) {
			if ($_SERVER['REQUEST_URI']!=XOOPS_URL . '/unban/remove/ipsec.html' ) {
				header( "HTTP/1.1 301 Moved Permanently" );
				header('Location: '.XOOPS_URL . '/unban/remove/ipsec.html');
				exit(0);
			}
		}
			
	    $xoopsOption['template_main'] = 'unban_index.html';
		include $GLOBALS['xoops']->path('/header.php');
			
		$GLOBALS['xoTheme']->addStylesheet(XOOPS_URL."/modules/unban/language/".$GLOBALS['xoopsConfig']['language']."/unban_style.css");
		
		$GLOBALS['xoopsTpl']->assign('unban_member_form', search_members_form());
		
		include $GLOBALS['xoops']->path('/footer.php');
		
	}

?>