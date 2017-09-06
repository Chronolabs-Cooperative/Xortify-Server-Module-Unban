<?php

function unban_search($queryarray, $andor, $limit, $offset, $userid)
{
	$ret = array();
	$ban_handler = xoops_getmodulehandler('members', 'unban');
	$criteria = array();
	if ($queryarray == ''||$userid>0){
		$keywords= '';
		$hightlight_key = '';
		$criteria_final = new CriteriaCompo(new Criteria('suid', $userid), "AND");
	} else {
		$criteria_final = new CriteriaCompo();
		foreach($queryarray as $id => $keyword) {
			$criteria[$id] = new CriteriaCompo(new Criteria('`ip4`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`ip6`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`proxy-ip4`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`proxy-ip6`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`network-addy`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`uname`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`email`', $keyword), 'OR');
			$criteria[$id]->add(new Criteria('`mac-addy`', $keyword), 'OR');
		}
		foreach($criteria as $id => $crit) {
			$criteria_final->add($criteria[$id], $andor);
		}
	}	

	$criteria_final->setLimit($limit);
	$criteria_final->setStart($offset);
	$criteria_final->setSort('`made`');
	$criteria_final->setOrder('DESC');
	$bansObj =& $ban_handler->getObjects($criteria_final, true);
	
	foreach ($bansObj as $member_id => $result) {
		$item['image'] = "images/unban.png";
		$item['link'] = "index.php?op=member&id=" . $member_id;
		$item['title'] = "Unban: " . $result->ipaddy() . ' : '.$result->getVar('network-addy');
		$item['time'] = $result->getVar('made');
		$item['uid'] = $result->getVar('suid');
		$ret[] = $item;
		unset($item);
	}	
	
	return $ret;
}

?>