<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include_once('models/drugs.php');
	include_once('models/equals.php');
	include_once('models/assess.php');
		
	global $dosageEquals;
	$dosageEquals = new DosageEquals();
	
	//$dosage = Assess::getDosage('0d');
	$dosages = Assess::getAllDosages();
	
	foreach ($dosages as $dosage){
		echo "\r\n编号：{$dosage['notation']} 规则名：{$dosage['name']} 单位：{$dosage['type']} \r\n";
		$data = $dosage['data'];
		if (isset($data['fixed'])){
			echo "其值固定为：{$data['fixed']['b']}\r\n";
		}else{
			if (isset($data['x'])){
				$xs = array();
				foreach ($data['x'] as $x){
					$xs[] = "{$x['name']}（{$x['id']}，单位{$x['unit']}）";
				}
				echo "自变量有：".implode('、', $xs)."\r\n";
			}
			if (isset($data['level'])){
				foreach ($data['level'] as $level){
					$ranges = array();
					$formula1 = array();
					$formula2 = array();
					if (isset($level['x'])){
						foreach ($level['x'] as $x){
							if ($x['min'] != $x['max'])
								$ranges[] = "{$x['min']}≤{$x['id']}≤{$x['max']}";
							else
								$ranges[] = "{$x['id']}＝{$x['max']}";
							
							if (isset($x['k'])){
								if ($x['k'] != 0)
									$formula2[] = $formula1[] = "{$x['k']}×{$x['id']}";
							}else{
								if ($x['k1'] != 0)
									$formula1[] = "{$x['k1']}×{$x['id']}";
								if ($x['k2'] != 0)
									$formula2[] = "{$x['k2']}×{$x['id']}";
							}
						}
					}
					if (isset($level['b'])){
						if ($level['b'] != 0)
							$formula2[] = $formula1[] = $level['b'];
					}else{
						if ($level['b1'] != 0)
							$formula1[] .= $level['b1'];
						if ($level['b2'] != 0)
							$formula2[] .= $level['b2'];
					}
					$formula1 = "y＝".implode('＋',$formula1);
					$formula2 = "y＝".implode('＋',$formula2);
					if ($formula1 == $formula2){
						$formula = $formula1;
					}else{
						$formula = "$formula1 至 $formula2";
					}
					echo "在自变量满足".implode('、', $ranges)."的时候，其公式为：{$formula}。\r\n";
				}
			}
		}
		if (isset($data['factor'])){
			foreach ($data['factor'] as $factor){
				echo "当 {$factor['name']} 时，用量要乘以{$factor['k']}。\r\n";
			}
		}
	}
	
	Util::endTimer(false);
?>