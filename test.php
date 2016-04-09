<html>
	<head>
		<meta charset='gbk'>
		<style>
			body, table{
				font-size:9px;
			}
		</style>
	</head>
	<body>
		<form method="post" action="testpost.php">
			<input type="submit" />
		<?php
			include_once('core/util.php');
			Util::startTimer();
			
			include_once('core/database.php');
			global $DB;
			$DB = new Database();
			$DB->connect();
			
			//��ȡ����
			$mode = $_POST['mode'];
			$type = $_POST['type'];
			$limit = $_POST['limit'];
			$random = $_POST['random'];
			$task = $_POST['task'];
			$dateFrom = $_POST['dateFrom'];
			$dateTo = $_POST['dateTo'];
			$taskNum = $_POST['taskNum'];
			$limit2 = $_POST['limit2'];
			$limit3 = $_POST['limit3'];
			if ($task == 0 && $mode == 0){
				$task = -1;
			}
			
			echo "<input type='hidden' name='task' value=$task />";
			
			
			//ɸѡ��Ҫ����
			$columnEvals;
			if ($_POST['columns'] != null){
				foreach($_POST['columns'] as $column){
					$columnEvals[$column] = Database::$COLUMNS_EVALS_NOID[$column];
				}
			}else{
				$columnEvals = Database::$COLUMNS_EVALS_NOID;
			}
			
			
			
			//��ģ���л�ȡ����
			include('models/test.php');
			$params = array(
				'mode' => $mode,
				'type' => $type,
				'limit' => $limit,
				'random' => $random,
				'task' => $task,
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
				'taskNum' => $taskNum,
				'limit2' => $limit2,
				'limit3' => $limit3,
			);
			$data = Test::getTestData($params);
			
			if ($task == 0){
				$doctors = array();
				foreach($data as $presc){
					$doctor = $presc['Doctor'];
					foreach($presc['data'] as $item){
						$doctors[$doctor][] = $item['ItemId'];
					}
				}
				echo '�����<table id="table_doctors"><tr>';
				$i = 0;
				foreach($doctors as $doctor=>$itemIds){
					$val = implode(',', $itemIds);
					echo "<input name='dIds[]' type='hidden' value='$val' />";
					echo "<td><input name='exDoctors[]' class='input_doctor' type='checkbox' id='input_doctor_$doctor' value='$i' /><label for='input_doctor_$doctor'>$doctor</label></td>";
					++$i;
				}
				echo '</tr></table>';
			}
			
			$itemIds = array();
			foreach($data as $presc){
				if ($presc['data'] == null){
					continue; //û�����ݵĲ���ʾ
				}
				echo '<p>';
				foreach(Database::$COLUMNS_PRESCS as $KEY => $NAME){
					echo $NAME.':'.$presc[$KEY].'<br/>';
				}
				echo '</p>';
				
				echo '<table>';
				echo '<tr>';
				foreach(Database::$COLUMNS_ITEMS as $NAME){
						echo "<th>$NAME</th>";
				}
				if ($task != 0){
					foreach($columnEvals as $NAME){
							echo "<th>$NAME</th>";
					}
				}
				echo '</tr>';
				
				foreach($presc['data'] as $item){
					$id = $item['ItemId'];
					$itemIds[] = $id;
					echo '<tr>';
					foreach(Database::$COLUMNS_ITEMS as $KEY => $NAME){
						echo '<td>'.$item[$KEY].'</td>';
					}
					if ($task != 0){
						foreach($columnEvals as $KEY => $NAME){
							$value = $item[$KEY];
							if ($KEY == 'EOther'){
								echo "<td><textarea name='$id.$KEY'>$value</textarea></td>";
							}elseif ($KEY == 'Checked'){
								if ($mode == 1)
									echo "<td><input type='checkbox' name='$id.$KEY' ".($value==2 ? 'checked' : '').' value=1 /></td>';
								else
									echo "<td><input type='checkbox' name='$id.$KEY' ".($value==1 ? 'checked' : '').' value=1 /></td>';
							}else{
								$checked = $value[0]=='.' ? 'checked' : '';
								echo "<td><input type='checkbox' name='$id~$KEY' $checked/><textarea name='$id.$KEY'>".substr($value,1).'</textarea></td>';
							}
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
					
			if ($task == 0){
				echo "<input type='hidden' name='taskNum' value='$taskNum'/>";
			}else{
				echo "<input type='hidden' name='itemIds' value='".implode(',', $itemIds)."'/>";
			}
			echo "<input type='hidden' name='mode' value='$mode'/>";
			
			Util::endTimer();
			//echo '<p>ҳ�湲ִ��'.(microtime(true) - $timer).'����</p>';
		?>
		</form>
	</body>
</html>
