<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
		
	//print_r($_POST);
	
	include('models/drugs.php');
	if ($_GET['type'] == 'modify'){
		if (isset($_POST['id']) && $_POST['notation'] != ''){
			$params = array(
				'id' => $_POST['id']);
				
			$params['type'] = $_POST['type'];
			if ($_POST['type'] == Drugs::TYPE_FIXED || $_POST['type'] == Drugs::TYPE_SUPER){
				if (isset($_POST['drugs']))
					$params['data']['ids'] = $_POST['drugs'];
			}else if ($_POST['type'] == Drugs::TYPE_DYNAMIC){
				if (isset($_POST['column']))
					$params['data']['column'] = $_POST['column'];
				if (isset($_POST['conditions']))
					$params['data']['values'] = $_POST['conditions'];
			}
			if (isset($_POST['notation']) && $_POST['notation'] != '')
				$params['notation'] = $_POST['notation'];
			if (isset($_POST['name']) && $_POST['name'] != '')
				$params['name'] = $_POST['name'];
			
			if (isset($_POST['memo']))
				$params['memo'] = $_POST['memo'];
				
			$meta = Drugs::setGroup($params);
		} else {
			$meta['status'] = 0;
		}
	}else if ($_GET['type'] == 'remove'){
		if (isset($_POST['id'])){
			$id = $_POST['id'];
			$meta = Drugs::removeGroup($id);
		}
	}else if ($_GET['type'] == 'getGroups'){
		$meta['status'] = 1;
		$meta['data'] = Util::arrayIconvGBK2UTF8(Drugs::getGroups());	
	}else if ($_GET['type'] == 'getDrugs'){
		$meta['status'] = 1;
		$meta['data'] = Util::arrayIconvGBK2UTF8(Drugs::getList(false));
	}else if ($_GET['type'] == 'getDrugsByIds'){
		$meta['status'] = 1;
		$meta['data'] = Util::arrayIconvGBK2UTF8(Drugs::getListByIds($_POST['ids'], true));
	}else if ($_GET['type'] == 'getDrugProperties'){
		$meta['status'] = 1;
		$meta['data'] = Util::arrayIconvGBK2UTF8(Drugs::getListProperties($_POST['column']));
	}
	
	Util::endTimer(false);
	
	$meta['time'] = Util::$timer;
	
	echo urldecode(json_encode($meta));
?>