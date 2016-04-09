<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include('models/test.php');
	if ($_POST['task'] == 0){
		$meta = Test::assignTasks($_POST);
	}else{
		$meta = Test::postTest($_POST);
	}
	
	Util::endTimer(false);
	
	$meta['time'] = Util::$timer;
	
	echo json_encode($meta);
?>