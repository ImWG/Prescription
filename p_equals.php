<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
		
	//print_r($_POST);
	
	include('models/equals.php');
	if ($_GET['type'] == 'modify'){
		if (isset($_POST['id']) && $_POST['notation'] != ''){
			$params = array(
				'id' => $_POST['id']);

			if (isset($_POST['conditions']))
				$params['data']['values'] = $_POST['conditions'];

			if (isset($_POST['notation']) && $_POST['notation'] != '')
				$params['notation'] = $_POST['notation'];
			if (isset($_POST['name']) && $_POST['name'] != '')
				$params['name'] = $_POST['name'];
				
			if (isset($_POST['type']) && $_POST['type'] != '')
				$params['type'] = $_POST['type'];
				
			if (isset($_POST['memo']))
				$params['memo'] = $_POST['memo'];
				
			$meta = Equals::setGroup($params);
		} else {
			$meta['status'] = 0;
		}
	}else if ($_GET['type'] == 'remove'){
		if (isset($_POST['id'])){
			$id = $_POST['id'];
			$meta = Equals::removeGroup($id);
		}
	}
	
	Util::endTimer(false);
	
	$meta['time'] = Util::$timer;
	
	echo json_encode($meta);
?>