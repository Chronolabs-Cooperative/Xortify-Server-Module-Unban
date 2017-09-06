<?php

	function search_members_form() 
	{
	
		$category_id = 0;
		if (is_object($GLOBALS['xoopsUser'])) {
			$uid = $GLOBALS['xoopsUser']->getVar('uid');
			$uname = $GLOBALS['xoopsUser']->getVar('uname');
		} else {
			$uid = 0;
			$uname = '';
		}
		if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){ 
			$ip = (string)$_SERVER["HTTP_X_FORWARDED_FOR"]; 
			$is_proxied = true;
			$proxy_ip = $_SERVER["REMOTE_ADDR"]; 
			$network_addy = @gethostbyaddr($ip); 
			$long = @ip2long($ip);
			if (is_ipv6($ip)) {
				$ip6 = $ip;
				$proxy_ip6 = $proxy_ip;
			} else {
				$ip4 = $ip;
				$proxy_ip4 = $proxy_ip;
			}
		}else{ 
			$is_proxied = false;
			$ip = (string)$_SERVER["REMOTE_ADDR"]; 
			$network_addy = @gethostbyaddr($ip); 
			$long = @ip2long($ip);
			if (is_ipv6($ip)) {
				$ip6 = $ip;
			} else {
				$ip4 = $ip;
			}
		} 
		
		exec('arping -c 1 '.$_SERVER["REMOTE_ADDR"],$user_mac);
		$mac_addy = substr($user_mac[1],strpos($user_mac[1],':')-2, '17');
		
		$made = time();						
		$title = 'Search for Banned Item';
		
		$form = new XoopsThemeForm($title, "search_item", "", "post");
		$form->addElement(new XoopsFormText('IPv4:', "ip4", 35, 255, $ip4));		
		$form->addElement(new XoopsFormText('IPv6:', "ip6", 35, 255, $ip6));		
		$form->addElement(new XoopsFormText('Proxy IPv4:', "proxy_ip4", 35, 255, $proxy_ip4));
		$form->addElement(new XoopsFormText('Proxy IPv6:', "proxy_ip6", 35, 255, $proxy_ip6));			
		$form->addElement(new XoopsFormText('Network Address:', "network_addy", 35, 128, $network_addy));					
		$form->addElement(new XoopsFormText('Mac Address:', "mac_addy", 35, 17, $mac_addy));
		$form->addElement(new XoopsFormText('Email Address:', "email", 35, 255, ''));
		$form->addElement(new XoopsFormText('Username:', "uname", 35, 128, $uname));
		$form->addElement(new XoopsFormCaptcha('Solve Puzzel:'));
		$form->addElement(new XoopsFormHidden("op", "members"));		
		$form->addElement(new XoopsFormHidden("fct", "search"));		
		$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));

		return $form->render();

	}
	
	function sel_members_form()
	{
	
		$form = new XoopsThemeForm('Current unbanned List', "current", "", "post");

		$membershandler = xoops_getmodulehandler('members','unban');
		$criteria = new Criteria('1', 1);
		$members = $membershandler->getObjects($criteria);	
		$element = array();
		foreach($members as $key => $item)
		{
			$element[$key] = new XoopsFormElementTray('Item '.$item->getVar('member_id').':');
			if ($item->getVar('uid')>0)
				$element[$key]->setDescription('unbanned By <a href="'.XOOPS_URL.'/userinfo.php?uid='.$item->getVar('uid').'">'.$item->getVar('uname').'</a>');
			$element[$key]->addElement(new XoopsFormLabel('', '<a href="index.php?op=members&fct=edit&id='.$item->getVar('member_id').'">Edit</a>&nbsp;<a href="index.php?op=members&fct=delete&id='.$item->getVar('member_id').'">Delete</a>'));
			$element[$key]->addElement(new XoopsFormLabel('IP:', ''.$item->getVar('ip4').''));			
			$element[$key]->addElement(new XoopsFormLabel('Network Address:', $item->getVar('network-addy')));
			$form->addElement($element[$key]);
		}
		$form->display();
		
	}
		
?>