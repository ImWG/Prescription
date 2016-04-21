<html>
	<head>
		<meta charset='gbk'>
		<!--<style>
			body, table{
				font-size:9px;
			}
		</style>-->
	</head>
	<link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
	<body>
		<form method="post" action="testpost.php">
			<input type="submit" /><input type='button' onclick="history.back();" value="����"/>
		<?php
			include_once('core/util.php');
			Util::startTimer();
			
			include_once('core/database.php');
			global $DB;
			$DB = new Database();
			$DB->connect();
			
			include('models/test.php');
			include('models/drugs.php');
			
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
			$drugGroups = $_POST['drugGroups'];
			$tasksId = $_POST['tasksId'];
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
				'drugGroups' => $drugGroups,
				'tasksId' => $tasksId,
			);
			if ($task == 0){
				$taskSettings = Test::saveTaskSettings($params);
				echo '״��:'.json_encode($taskSettings);
			}else{
				if($task == -2){
					//������
					$taskSettings = Test::loadTaskSettings($tasksId);
					$value = $taskSettings['data']['value'];
					foreach ($params as $key => $val){
						if ($key == 'mode')
							$params[$key] = 1;
						elseif ($key == 'task')
							$params[$key] = 0;
						else{
							$params[$key] = $value->$key;
						}
					}
				}else{
					$taskSettings = Test::loadLastTaskSettings();
				}
				echo "�������ţ�{$taskSettings['data']['id']}���ƶ�ʱ�䣺{$taskSettings['data']['time']} ";
				if ($task == -1){
					echo "����ţ�ȫ��";
				}elseif ($task > 0){
					echo "����ţ�$task";
				}
			}
			$data = Test::getTestData($params);
			
			echo "<input type='hidden' value='{$taskSettings['data']['id']}' name='tasksId'/>";
			
			if ($task == 0){
				$doctors = array();
				foreach($data as $presc){
					$doctor = $presc['Doctor'];
					if ($presc['data'] != null)
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
				
				echo '<div class="panel panel-default">';
				
				//��ʾ����������Ϣ
				echo '<div class="col-sm-12 panel-heading">';
				foreach(Database::$COLUMNS_PRESCS as $KEY => $NAME){
					echo '<span class="col-sm-'.Database::$COLUMNS_STRIDE['P'][$KEY].'">'.$NAME.':'.$presc[$KEY].'</span>';
				}
				echo '</div>';
				echo '<div class="panel-body">';
				echo '<ul class="list-group">';
				
				foreach($presc['data'] as $item){
					$id = $item['ItemId'];
					$itemIds[] = $id;
					
					echo '<li class="list-group-item col-sm-12">';
					
					//��ʾ������ͷ����Ϣ
					foreach(Database::$COLUMNS_ITEMS_NOX as $KEY => $NAME){
							echo "<span class='col-sm-".Database::$COLUMNS_STRIDE['I'][$KEY]."'><label>{$NAME}��</label>{$item[$KEY]}</span>";
					}
					
					
					//��ʾ���۱�ͷ
					/*if ($task != 0){
						foreach($columnEvals as $NAME){
								echo "<th>$NAME</th>";
						}
					}*/

					//��ʾ������Ϣ
					if ($task != 0){
						foreach($columnEvals as $KEY => $NAME){
							$value = $item[$KEY];
							if ($KEY == 'EOther'){
								echo "<label class='col-sm-1 text-right'>$NAME</label><textarea class='col-sm-10' name='$id.$KEY'>$value</textarea></td>";
							}elseif ($KEY == 'Checked'){
								if ($mode == 1)
									$checked = $value==2 ? 'checked' : '';
								else
									$checked = $value==1 ? 'checked' : '';
									
								echo "<span class='col-sm-1 text-right'><input class='form-control' type='checkbox' name='$id.$KEY' $checked value=1 /></span>";
							}else{
								$checked = $value[0]=='.' ? 'checked' : '';
								echo "<span class='col-sm-2 text-right'><label>$NAME</label><input type='checkbox' name='$id~$KEY' $checked/></span><textarea class='col-sm-10' name='$id.$KEY'>".substr($value,1).'</textarea>';
							}
						}
					}

					echo '</li>';
					
				}
				echo '</ul>';
				echo '</div>';
				echo '</div>';
				
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
	<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</html>
