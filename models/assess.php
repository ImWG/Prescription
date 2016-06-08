<?php
	class Assess{
		public static function assessDrug($data){
			global $DB;
			
			$PatientAge = $data['PatientAge'];
			$Diagnosis = $data['Diagnosis'];
			
			$Name = $data['Name'];
			$Names = $data['Names'];
			$Dosage = $data['Dosage'];
			$DrugId = $data['DrugId'];
			
			$assessment = array();
			
			
			$Drugs = Drugs::getListById($DrugId, true);
			
			
			
			/* ***** 用药是否对症 ***** */
			global $diseaseEquals;
			$diseaseEquals = new DiseaseEquals();
			
			$equals = $diseaseEquals->getGroupByDiagnosis($Diagnosis);
			$groups = array();
			foreach ($equals as $equal){
				$drugs2 = explode(' ', $equal['drugs']);
				$groups = array_merge($groups, $drugs2);
			}
			
			$drugs2 = Drugs::getListByGroups($groups, true);
			foreach ($drugs2 as $drug){
				if ($Name == $drug['name']){
					$assessment['EMatch'] = 1; //此药品符合对症
					break;
				}
			}
			
			
			/* ***** 联合用药是否合理 ***** */
			global $combineEquals;
			$combineEquals = new CombineEquals();
			
			$assessment['ECombine'] = 1;
			$badDrugNames = array();
			foreach ($Drugs as $drug){
				//后搜索（XX药不能和该药同用）
				$combines = $combineEquals->getGroupsByData($drug['id']);
				foreach ($Names as $nam){
					$drugs2 = Drugs::getListByName($nam, true);
					foreach ($drugs2 as $drug2){
						$id2 = $drug2['id'];
						foreach ($combines as $combine){
							if ($combine['notation'] == $id2){
								$assessment['ECombine'] = 0; //此药品有冲突
								$badDrugNames[] = $nam;
							}
						}
					}
				}
				//前搜索（该药不能和XX药同用）
				$ocombine = $combineEquals->getGroup($drug['id']);
				$combines = explode("\0", $ocombine['data']);
				foreach ($Names as $nam){
					$drugs2 = Drugs::getListByName($nam, true);
					foreach ($drugs2 as $drug2){
						$id2 = $drug2['id'];
						foreach ($combines as $combine){
							if ($id2 == $combine){
								$assessment['ECombine'] = 0; //此药品有冲突
								$badDrugNames[] = $nam;
							}
						}
					}
				}
			}
			$assessment['ECombine:'] = $badDrugNames;
			
			
			/* ***** 用药剂量、频次、方式是否合理 ***** */
			global $dosageEquals;
			$dosageEquals = new DosageEquals();
			global $packageEquals;
			$packageEquals = new PackageEquals();
			
			preg_match_all('/^(?:(\d+)(?:年|岁)){0,1}(?:(\d+)(?:月|个月)){0,1}(?:(\d+)(?:日|天)){0,1}$/', $PatientAge, $matches);
			$ageYear = intval($matches[1][0]) + intval($matches[2][0]) / 12 + intval($matches[3][0]) / 365;
			$ageDay = intval($matches[1][0]) * 365 + intval($matches[2][0]) * 30 + intval($matches[3][0]);
			//年龄到体重的换算
			$mass = $age * 10;
			
			
			$values = array('a'=>$ageYear, 'a..'=>$ageDay, '_diagnosis'=>$Diagnosis,
				'm'=>$mass);

			
			//剂量
			$range = self::dosageCheck($values, $Drugs[0]['id'].'d');
			
			//echo "min=$min, max=$max";
			preg_match_all('/^([.\d]+)(.+)$/', $Dosage, $matches);
			$DosageAmount = $matches[1][0];
			$DosageUnit = $matches[2][0];
			$package = self::getPackageRate($Drugs[0]['id'], Util::arrayIconvGBK2UTF8($DosageUnit));
			$dosage = doubleval($DosageAmount)*$package[1];
			
			if ($dosage >= $range[0] && $dosage <= $range[1]){
				$assessment['EDosage'] = 1; //此药品剂量合理
			}else{
				$assessment['EDosage'] = 0; //此药品剂量超出范围
			}
			$assessment['EDosage:'] = array($range[0].$range[2], $range[1].$range[2],
				$range[0]/$package[1].$DosageUnit, $range[1]/$package[1].$DosageUnit);
			
			
			//频次
			$range = self::dosageCheck($values, $Drugs[0]['id'].'f');
			
			$frequencies = self::frequencyConvert($data['Frequency']);
			if ($range[2]=='小时/日'){
				$frequency = $frequencies[1];
			}else{
				$frequency = $frequencies[0];
			}
			
			if ($frequency >= $range[0] && $frequency <= $range[1]){
				$assessment['EFrequency'] = 1; //此药品频次合理
			}else{
				$assessment['EFrequency'] = 0; //此药品频次超出范围
			}
			$assessment['EFrequency:'] = array($range[0].$range[2], $range[1].$range[2],
				$range[0], $range[1]);
					
					
			//方式
			$range = self::dosageCheck($values, $Drugs[0]['id'].'m');
			$method = self::methodConvert($data['Method']);
			
			$method = 1<<$method[0];
			$range = $range[0];
			
			if (($range & $method) == 0){
				$assessment['EMethod'] = 0;
				$assessment['EMethod:'] = array();
				for ($i=0; $i<8; ++$i){
					if (((1<<$i) & $range) != 0)
						$assessment['EMethod:'][] = self::methodConvertInv($i);
				}
			}else{
				$assessment['EMethod'] = 1;
			}
			
			
			
						
			return $assessment;
		}
		
		
		private static function dosageCheck($values, $notation){
			global $DB;
			global $dosageEquals;
		
			$min = 0; $max = 0xFFFF;
			$unit = '';

			$p = $dosageEquals->getGroup($notation);
			$unit = $p['type'];
			$t = json_decode($p['data'], true);
			
			if ($t != null){
				if (isset($t['fixed'])){
					$t['fixed'];
					$min = $max = $t['fixed']['@']['b'];
				}else{
					if (isset($t['level']))
					foreach($t['level'] as $level){
						
						$inLevel = true;

						if (isset($level['@']['b'])){
							$b1 = $level['@']['b']; $b2 = $level['@']['b'];
						}else{
							$b1 = $level['@']['b1']; $b2 = $level['@']['b2'];
						}
						$_min = $b1;
						$_max = $b2;
						
						//print_r($level);
						foreach($level['x'] as $x){
							if (isset($x['@']))
								$x = $x['@'];
							
							$id = $x['id'];
							if (!isset($values[$id]))
								continue;
							
							if (isset($values[$id])){
								$a = $values[$id];
								if ($a>=$x['min'] && $a<$x['max']){
									if (isset($x['k'])){
										$_min += $a * $x['k']; $_max += $a * $x['k'];
									}else{
										$_min += $a * $x['k1']; $_max += $a * $x['k2'];
									}
								}else{
									$inLevel = false;
								}
							}
						}
						
						if($inLevel){
							$min = $_min; $max = $_max;
				
							break;
						}
					}
				}
				if (isset($t['factor'])){
					foreach ($t['factor'] as $factor){
						if (isset($factor['@']))
							$factor = $factor['@'];
							
						if (stripos($Diagnosis, $factor['name']) !== false){
							$min *= $factor['k']; $max *= $factor['k'];
						}
					}
				}
			}
			
			return array($min, $max, $unit);
		}

		private static function getPackageRate($notation, $unit){
			global $DB;
			global $packageEquals;
		
			$p = $packageEquals->getGroup($notation);
			$baseUnit = $p['type'];
			$t = json_decode($p['data'], true);
			$packages = $t['p'];
			if ($packages)
				foreach ($packages as $package){
					if ($package['@']['unit'] == $unit){
						return array($baseUnit, $package['@']['rate']);
					}
				}
			return array($baseUnit, 1);
		}
		
		static private function frequencyConvert($_freq){
			$freq = strtolower($_freq);
			$figure = 0;
			if (preg_match('/q\d+h/', $freq)){
				$figure = 24 / intval(substr($freq, 1));
			}else{
				$map = array(
					'qh' => 24,
					'qd' => 1,	'bid' => 2,	'tid' => 3,	'qid' => 4,	'qod' => .5,
					'qw' => 1/7, 'biw' => 1/14
				);
				$figure = $map[$freq];
			}
			return array($figure, $figure/24);
		}
		
		static private function methodConvert($_method){
			$method = strtolower(preg_replace('/\W+/', '', $_method));
			$figure = 0;
			$map = array(
				'po' => 0,	'ig' => 1,	'inhal' => 2,
				'ip' => 3,	'sc' => 3,	'im' => 4,	'ih' => 5,
				'iv' => 6,	'ivgtt' => 7,	'ivdrip' => 7
			);
			$figure = $map[$method];
			
			return array($figure);
		}
		
		static private function methodConvertInv($figure){
			$map = array(
				0 => 'po',	1 => 'ig',	2 => 'inhal',
				3 => 'ip',	4 => 'im',	5 => 'ih',
				6 => 'iv',	7 => 'ivgtt'
			);
			$method = $map[$figure];
			
			return $method;
		}
		
		
		
		public static function getDosage($notation){
			global $DB;
			global $dosageEquals;
			
			$p = $dosageEquals->getGroup($notation);
			$unit = $p['type'];
			$t = Util::arrayIconvUTF82GBK(json_decode($p['data'], true));
			
			if ($t != null){
			
				$line = array(
					'id' => $p['id'],
					'type' => $p['type'],
					'notation' => $p['notation'],
					'name' => $p['name'],
					'memo' => $p['memo']
					);
				
				$data = array();
				
				if (isset($t['fixed'])){
					$data['fixed'] = $t['fixed']['@'];
				}else{
					if (isset($t['x'])){
						$xs = array();
						foreach($t['x'] as $_x){
							if (isset($_x['@']))
								$_x = $_x['@'];
								
							$xs[] = $_x;
						}
						$data['x'] = $xs;
					}
				
					$levels = array();
					if (isset($t['level']))
						foreach($t['level'] as $_level){
							$level = array();
							
							$inLevel = true;
							$level = $_level['@'];
							
							$level['x'] = array();
							foreach($_level['x'] as $_x){
								if (isset($_x['@']))
									$_x = $_x['@'];
								
								$level['x'][] = $_x;
							}
							$levels[] = $level;
						}
					$data['level'] = $levels;
				}
				if (isset($t['factor'])){
					$data['factor'] = array();
					foreach ($t['factor'] as $_factor){
						if (isset($_factor['@']))
							$_factor = $_factor['@'];
							
						$data['factor'][] = $_factor;
					}
				}
				
				$line['data'] = $data;
			}
			
			return $line;
		}
		
		public static function getAllDosages(){
			global $DB;
			global $dosageEquals;
			
			$groups = $dosageEquals->getGroups();
			$dosages = array();
			foreach($groups as $group){
				$dosages[] = self::getDosage($group['notation']);
			}
			return $dosages;
		}
	}
?>