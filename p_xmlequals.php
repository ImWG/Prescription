<?php
	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	include_once('core/util.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include_once('models/equals.php');
	$table = $_GET['table'];
	if ($table == 'dosage'){
		$DosageEquals = new DosageEquals();
	}else if ($table == 'package'){
		$DosageEquals = new PackageEquals();
	}else{
		die('{"error":0}');
	}
	
	if ($_GET['action'] == 'export'){
		header('Content-Disposition: attachment; filename="output.xml"'); 
		echo $DosageEquals->toXML();
		exit;
		
	}else{
	
		$meta = array('error'=>0);
	
		if ($_GET['action'] == 'load'){
			if (file_exists($_FILES['xmlFile']['tmp_name'])){
				$xmlFile = file_get_contents($_FILES['xmlFile']['tmp_name']);
			
				if ($DosageEquals->truncate()){
					$meta['error'] = $DosageEquals->loadXML($xmlFile);
				}else{
					$meta['error'] = 2;
				}
			}else{
				$meta['error'] = 1;
			}
		}
		echo json_encode($meta);
	} 
	
?>