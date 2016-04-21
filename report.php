<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text/html; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include('models/test.php');

	//print_r(Test::getListByTasksId());
	$meta = Test::getListByTasksId();
	if ($meta['error'] != 0){
		die;
	}
	
	$lists = $meta['data'];
	$totalCount = 0;
	$userCount = count($lists);
	$tasksId = $lists[1][0]['TasksId'];
	foreach ($lists as $list){
		$totalCount += count($list);
	}
	$taskSettings = $taskSettingsMeta['data']['value'];
	$taskSettingsMeta = Test::loadTaskSettings($tasksId);
	
	echo "<p>�������� {$tasksId} ��һ���� {$totalCount} ��ҩƷ����Ϊ {$userCount} ������</p>";
	echo "<p>������������� {$taskSettingsMeta['data']['time']} ����ȡ�˴� {$taskSettings->dateFrom} �� {$taskSettings->dateTo} �Ĵ�����</p>";
	
	$pros = 0;
	$cons = 0;
	$uncheckeds = 0;
	$unfilleds = 0;
	$badItems = array();
	$blankItems = array();
	foreach ($lists as $user => $list){
		foreach ($list as $item){
			$filled = false;
			$approved = true;
			foreach (Database::$COLUMNS_EVALS as $KEY => $NAME){
				if ($KEY == 'Checked'){
				}elseif ($KEY != 'ItemId'){
					if ($item[$KEY][0] != null){
						$filled = true;
						if ($item[$KEY][0] == Database::$FLAG_APPROVED){
						
						}elseif ($item[$KEY][0] == Database::$FLAG_REJECTED){
						
							$approved = false;
						}
					}
				}
			}
			if ($filled){
				if ($approved){
					++$pros;
				}else{
					++$cons;
					$badItems[] = $item;
				}
			}else{
				++$unchecked;
				$blankItems[] = $item;
			}
		}
	}
	
	@$approvedRate = $pro / ($pros + $cons) * 100.0;
	
	
	echo "<p>�� {$pros} ����ȷ�ģ� {$cons} ���д���ģ� {$unchecked} ��δ��д�ġ�</p>";
	
	echo "<p>ҩƷ��ȷ��Ϊ {$approvedRate} ��</p>";
	
	
	
	
	/*
		echo "<p>�д����ҩƷ��</p>";
		echo '<table>';
			echo '<thead>';
			foreach (Database::$COLUMNS_PRESCS as $KEY => $NAME){
				echo "<th>$NAME</th>";
			}
			foreach (Database::$COLUMNS_ITEMS_NOX as $KEY => $NAME){
				echo "<th>$NAME</th>";
			}
			foreach (Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
				echo "<th>$NAME</th>";
			}
			echo '</thead>';
		foreach ($badItems as $item){
			echo '<tr>';
			foreach (Database::$COLUMNS_PRESCS as $KEY => $NAME){
				echo "<td>{$item[$KEY]}</td>";
			}
			foreach (Database::$COLUMNS_ITEMS_NOX as $KEY => $NAME){
				echo "<td>{$item[$KEY]}</td>";
			}
			foreach (Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
				$temp = substr($item[$KEY], 1);
				echo "<td>{$temp}</td>";
			}
			echo '</tr>';
		}
		echo '</table>';
		
		echo "<p>δ������ҩƷ��</p>";
		echo '<table>';
			echo '<thead>';
			foreach (Database::$COLUMNS_PRESCS as $KEY => $NAME){
				echo "<th>$NAME</th>";
			}
			foreach (Database::$COLUMNS_ITEMS_NOX as $KEY => $NAME){
				echo "<th>$NAME</th>";
			}
			echo '</thead>';
		foreach ($blankItems as $item){
			echo '<tr>';
			foreach (Database::$COLUMNS_PRESCS as $KEY => $NAME){
				echo "<td>{$item[$KEY]}</td>";
			}
			foreach (Database::$COLUMNS_ITEMS_NOX as $KEY => $NAME){
				echo "<td>{$item[$KEY]}</td>";
			}
			echo '</tr>';
		}
		echo '</table>';
	*/
	
	Util::endTimer();
?>