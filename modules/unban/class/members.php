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
class unbanMembers extends XoopsObject
{

    function __construct($fid = null)
    {
        $this->initVar('member_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);		
		$this->initVar('suid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);	
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('email', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('ip4', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('ip6', XOBJ_DTYPE_TXTBOX, null, false, 65535);
		$this->initVar('long', XOBJ_DTYPE_TXTBOX, null, false, 120);
		$this->initVar('proxy-ip4', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('proxy-ip6', XOBJ_DTYPE_TXTBOX, null, false, 65535);						
		$this->initVar('network-addy', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('mac-addy', XOBJ_DTYPE_TXTBOX, null, false, 255);	
		$this->initVar('country-code', XOBJ_DTYPE_TXTBOX, null, false, 3);
		$this->initVar('country-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('region-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('city-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('postcode', XOBJ_DTYPE_TXTBOX, null, false, 15);
		$this->initVar('latitude', XOBJ_DTYPE_DECIMAL, null, false);
		$this->initVar('longitude', XOBJ_DTYPE_DECIMAL, null, false);
		$this->initVar('timezone', XOBJ_DTYPE_TXTBOX, null, false, 6);
		$this->initVar('made', XOBJ_DTYPE_INT, null, false);			
		// Fraud System Added for Card Numbers of Any Origin
		$this->initVar('card-number', XOBJ_DTYPE_TXTBOX, null, false, 42);
		$this->initVar('card-number-parts', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('card-type', XOBJ_DTYPE_TXTBOX, null, false, 96);
		$this->initVar('card-expire-month', XOBJ_DTYPE_INT, null, false);
		$this->initVar('card-expire-year', XOBJ_DTYPE_INT, null, false);
		$this->initVar('card-country-use', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-country-collected', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-country-shipped', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('card-shipping-to', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('card-shipping-from', XOBJ_DTYPE_ARRAY, array(), false);
		$this->initVar('tags', XOBJ_DTYPE_TXTBOX, null, false, 255);
		
    }

    
    function getURL() {
    	if ($GLOBALS['xoopsModuleConfig']['htaccess']==true) {
    		return XOOPS_URL.'/unban/retracted/'.$this->getVar('member_id').'/'.strtolower(str_replace(' ', '-', $this->getVar('country-name'))).'/'.$this->ipaddy() . '/ipsec.html';
    	} else {
    		return XOOPS_URL.'/modules/unban/?op=member&id='.$this->getVar('member_id').'&ip='.$this->ipaddy();
    		
    	}
    }

    function getTitle() {
    	$categories_handler = xoops_getmodulehandler('categories', 'unban');
    	$category = $categories_handler->get($this->getVar('category_id'));
    	if (!is_object($category))
    		return false;
    	return ucwords($category->getVar('category_type')) . ' Unban ~ Made on the '. date(_DATESTRING, $this->getVar('made'))." by a remote client!";
    }
    
    
    function toArray() {
    	$ret = parent::toArray();
    	$ret['made'] = date(_DATESTRING, $this->getVar('made'));
    	$categories_handler = xoops_getmodulehandler('categories', 'unban');
    	$category = $categories_handler->get($this->getVar('category_id'));
    	if (is_object($category))
    		$ret['category'] = $category->toArray();
    	unset($ret['card-number-parts']);
    	$comment_handler = xoops_gethandler('comment');
		$module_handler = xoops_gethandler('module');
		$GLOBALS['moduleBan'] = $module_handler->getByDirname('unban');
		$criteria = new CriteriaCompo(new Criteria('com_itemid', $this->getVar('member_id')));
		$criteria->add(new Criteria('com_modid', $GLOBALS['moduleBan']->getVar('mid')));
		$comments = $comment_handler->getObjects($criteria, true);
		if (count($comments)>0) {
			foreach($comments as $com_id => $comment);
				$ret['comments'][$com_id] = $comment->toArray();
		}
		if (!empty($ret['card-country-use']))
			$ret['card-country-use'] = unbanMembersHandler::getCountry($ret['card-country-use'], 'key', 'Country');
		else
			unset($ret['card-country-use']);
		if (!empty($ret['card-country-collected']))
			$ret['card-country-collected'] = unbanMembersHandler::getCountry($ret['card-country-collected'], 'key', 'Country');
		else
			unset($ret['card-country-use']);
		if (!empty($ret['card-country-shipped']))
			$ret['card-country-shipped'] = unbanMembersHandler::getCountry($ret['card-country-shipped'], 'key', 'Country');
		else
			unset($ret['card-country-shipped']);
		if (count($ret['card-shipping-to'])==0||empty($ret['card-shipping-to']))
			unset($ret['card-shipping-to']);
		if (count($ret['card-shipping-from'])==0||empty($ret['card-shipping-from']))
			unset($ret['card-shipping-from']);
		if (empty($ret['card-number']))
			unset($ret['card-number']);
		if (empty($ret['card-type']))
			unset($ret['card-type']);
		if ($ret['card-expire-month']==0)
			unset($ret['card-expire-month']);
		if ($ret['card-expire-year']==0)
			unset($ret['card-expire-year']);
    	return $ret;
    }
    
	function ipaddy() {
		if (strlen($this->getVar('ip4'))>0)
			return $this->getVar('ip4');
		else
			return $this->getVar('ip6');
	}
	
	function story() {
		$categories_handler = xoops_getmodulehandler('categories', 'unban');
		$category = $categories_handler->get($this->getVar('category_id'));
		if (!is_object($category))
			return false;
		$txt .= '<img src="'.XOOPS_URL.'/modules/unban/images/unban_slogo.png"><br/>';
		$txt .= '<strong>'.ucwords($category->getVar('category_type')) . ' Unban</strong> ~ Made on the '. date(_DATESTRING, $this->getVar('made'))." by a remote client of the Xortify Cloud this attempted security intrusions details are as follow:<br/><br/>";
		if (strlen($this->getVar('uname'))>0)
			$txt .= _UNBAN_MF_UNAME.': '. $this->getVar('uname')."<br/>";
		if (strlen($this->getVar('email'))>0)
			$txt .= _UNBAN_MF_EMAIL.': '. $this->getVar('email')."<br/>";
		if (strlen($this->getVar('ip4'))>0)
			$txt .= _UNBAN_MF_IP4.': '. $this->getVar('ip4')."<br/>";
		if (strlen($this->getVar('ip6'))>0)
			$txt .= _UNBAN_MF_IP6.': '. $this->getVar('ip6')."<br/>";
		if (strlen($this->getVar('long'))>0)
			$txt .= _UNBAN_MF_LONG.': '. $this->getVar('long')."<br/>";
		if (strlen($this->getVar('proxy-ip4'))>0)
			$txt .= _UNBAN_MF_PROXY_IP4.': '. $this->getVar('proxy-ip4')."<br/>";
		if (strlen($this->getVar('proxy-ip6'))>0)
			$txt .= _UNBAN_MF_PROXY_IP6.': '. $this->getVar('proxy-ip6')."<br/>";
		if (strlen($this->getVar('network-addy'))>0)
			$txt .= _UNBAN_MF_NETWORK_ADDY.': '. $this->getVar('network-addy')."<br/>";
		if (strlen($this->getVar('mac-addy'))>0)
			$txt .= _UNBAN_MF_MAC_ADDY.': '. $this->getVar('mac-addy')."<br/>";
		if (strlen($this->getVar('country-name'))>0)
			$txt .= _UNBAN_MF_COUNTRY_NAME.': '. $this->getVar('country-name')."(".$this->getVar('country-code').")<br/>";
		if (strlen($this->getVar('region-name'))>0)
			$txt .= _UNBAN_MF_REGION_NAME.': '. $this->getVar('region-name')."<br/>";
		if (strlen($this->getVar('city-name'))>0)
			$txt .= _UNBAN_MF_CITY_NAME.': '. $this->getVar('city-name')."<br/>";
		if (strlen($this->getVar('postcode'))>0)
			$txt .= _UNBAN_MF_POSTCODE.': '. $this->getVar('postcode')."<br/>";
		if (strlen($this->getVar('latitude'))>0)
			$txt .= _UNBAN_MF_LATITUDE.': '. $this->getVar('latitude')."<br/>";
		if (strlen($this->getVar('longitude'))>0)
			$txt .= _UNBAN_MF_LONGITUDE.': '. $this->getVar('longitude')."<br/>";
		if (strlen($this->getVar('timezone'))>0)
			$txt .= _UNBAN_MF_TIMEZONE.': '. $this->getVar('timezone')."<br/>";
		if (strlen($this->getVar('card-number'))>0) {
			$parts = $this->getVar('card-number-parts');
			$txt .= _UNBAN_MF_CARDNUMBER.': '. implode(' ', $parts['display'])."<br/>";
			
			if (strlen($this->getVar('card-type'))>0)
				$txt .= _UNBAN_MF_CARDTYPE.': '. ucwords($this->getVar('card-type'))."<br/>";
			if (strlen($this->getVar('card-expire-month'))>0)
				$txt .= _UNBAN_MF_CARDEXPIREMONTH.': '. $this->getVar('card-expire-month')."<br/>";
			if (strlen($this->getVar('card-type'))>0)
				$txt .= _UNBAN_MF_CARDEXPIREYEAR.': '. $this->getVar('card-expire-year')."<br/>";
			if (strlen($this->getVar('card-country-use'))==32)
				$txt .= _UNBAN_MF_CARDCOUNTRYUSE.': '. unbanMembersHandler::getCountry($this->getVar('card-country-use'), 'key', 'Country')."<br/>";
			if (strlen($this->getVar('card-country-collected'))==32)
				$txt .= _UNBAN_MF_CARDCOUNTRYCOLLECTED.': '. unbanMembersHandler::getCountry($this->getVar('card-country-collected'), 'key', 'Country')."<br/>";
			if (strlen($this->getVar('card-country-shipped'))==32)
				$txt .= _UNBAN_MF_CARDCOUNTRYSHIPPED.': '. unbanMembersHandler::getCountry($this->getVar('card-country-shipped'), 'key', 'Country')."<br/>";
		}
		
		$comment_handler = & xoops_gethandler('comment');
		$module_handler = & xoops_gethandler('module');	
		$xoModule = $module_handler->getByDirname('unban');
		
		$criteria = new CriteriaCompo(new Criteria('com_modid', $xoModule->getVar('mid')));
		$criteria->add(new Criteria('com_itemid', $this->getVar('member_id')));
		$comments = $comment_handler->getObjects($criteria);
		if (count($comments)>0) {
			$txt .= "<br/>";
			foreach($comments as $id => $comment) {
				$txt .= str_replace(chr(0), '', str_replace('\n', '<br/>', stripslashes($comment->getVar('com_text'))));
			}
		}
			
		return $txt;
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
class unbanMembersHandler extends XoopsPersistableObjectHandler
{

	var $countries = array();

    function __construct(&$db) 
    {
        parent::__construct($db, "unban_member", 'unbanMembers', "member_id", "display_name");

        xoops_load('cache');
        if (!$this->countries = XoopsCache::read('api_countries_list')) {
        	$this->countries = json_decode(unban_get_curl('http://places.labs.coop/v1/list/list/json.api'), true);
        	XoopsCache::write('api_countries_list', $this->countries, 3600*24*7*4*3);
        }
    }
	
    static function getCountry($strtomatch = '', $from = 'ISO2', $return = 'key')
    {
		static $countries = array();
		
    	xoops_load('cache');
    	if (empty($countries)||count($countries)==0) {
	    	if (!$countries = XoopsCache::read('api_countries_list')) {
	    		$countries = json_decode(unban_get_curl('http://places.labs.coop/v1/list/list/json.api'), true);
	    		XoopsCache::write('api_countries_list', $countries, 3600*24*7*4*3);
	    	}
    	}
    	
   		foreach($countries['countries'] as $key => $data)
   		{
   			if (strtolower($data[$from])==strtolower($strtomatch))
   				return $data[$return];
   		}
    	return false;
    }
    
    function insert($obj, $forced = true) {

    	$itemid = parent::insert($obj, $forced);
    	
    	if (strlen(trim($obj->getVar('tags')))>0) {
    		$tag_handler = xoops_getmodulehandler('tag', 'tag');
    		$tag_handler->updateByItem($obj->getVar('tags'), $itemid, 'unban', $obj->getVar('category_id'));
    	}
    	
    	return $itemid;
    }
    
    function get($id, $force = true) {
    	$obj = parent::get($id, $force);
    	if (strpos((string)$obj->getVar('latitude'), '.99999')!=0 && strpos((string)$obj->getVar('longitude'), '.99999')!=0 ) {
    		$places = json_decode(unban_get_curl($uri = 'http://places.labs.coop/v1/'.strtolower($obj->getVar('country-code')).'/'.urlencode(strtolower($obj->getVar('city-name'))).'/22/json.api'), true);
    		$keys = array_keys($places['places']);
    		shuffle($keys);
    		$key = $keys[mt_rand(0, count($keys)-1)];
    		$obj->setVar('latitude', (string)$places['places'][$key]['Latitude_Float']);
    		$obj->setVar('longitude', (string)$places['places'][$key]['Longitude_Float']);
    	}
    	if ($obj->isDirty())
    		parent::insert($obj, $force);
    	return $obj;
    }
    

    function __destruct()
    {
    	$crypt = new CriteriaCompo(new Criteria('made', '0', '='), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '='), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'LIKE'), 'OR');
    	$crypt->add($addy);
    	self::deleteAll($crypt, true);
    }
    
    function getObjects($criteria, $id_as_key = false, $as_object = true) {
    	$crypt = new CriteriaCompo($criteria);
    	$crypt->add(new Criteria('made', '0', '>'), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '>'), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'NOT LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'NOT LIKE'), 'OR');
    	$crypt->add($addy);
    	$objs = parent::getObjects($crypt, $id_as_key, $as_object);
    	return $objs;
    }
    
    
    function getCount($criteria) {
    	$crypt = new CriteriaCompo($criteria);
    	$crypt->add(new Criteria('made', '0', '>'), 'AND');
    	$crypt->add(new Criteria('category_id', '0', '>'), 'AND');
    	$addy = new CriteriaCompo(new Criteria('ip4', '', 'NOT LIKE'), 'OR');
    	$addy->add(new Criteria('ip6', '', 'NOT LIKE'), 'OR');
    	$crypt->add($addy);
    	return parent::getCount($crypt, $id_as_key, $as_object);
    }
}
?>
