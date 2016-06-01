<?php
	class Test {
		
		/**
		 * ��ô���
		 */
		static function getTestData($params){
		
			global $DB;
		
			$mode = $params['mode'];
			$type = $params['type'];
			$limit = $params['limit'];
			$random = $params['random'];
			$task = $params['task'];
			$dateFrom = Database::purify($params['dateFrom']);
			$dateTo = Database::purify($params['dateTo']);
			$taskNum = $params['taskNum'];
			$limit2 = $params['limit2'];
			$limit3 = $params['limit3'] == 0 ? 0xFFFFFF : $params['limit3'];
			$drugGroups = $params['drugGroups'];
			$tasksId = $params['tasksId'];
			
			
			$condition0 = '';
			
			$condition;
			if ($task == 0){
				$condition = "WHERE TRUE";
			}else{
				$condition = $mode==1 ? "WHERE (`Checked` = 1 OR `Checked` = 2)" //�����鳤
					: "WHERE (`Checked` = 0 OR `Checked` = 1 OR `Checked` IS NULL)"; //������Ա
			}
			
			
			if ($task > 0){
				//���������
				$queryTask = $DB->query("select * from `tasks` where `User`='$task' order by `TasksId` desc limit 0,1");
				$row = mysql_fetch_array($queryTask);
				
				$items = array();
				$task1 = $row['ItemIds'];
				$l = strlen($task1);
				for ($i=0; $i<$l; $i+=4){
					$b = unpack('l', substr($task1, $i, 4));
					
					//����ʹ�õ��ǰ�ItemId����ģ�ʵ�������е������ֻ��ҽ�����Ʒ���
					$items[] = ' `presc_items`.`ItemId` = "'.$b[1].'"';
				}
				
				$condition .= ' AND ('.implode(' OR', $items).')';
				
			}else{
				$unassigned = false;
				if ($task == -2){ 
					// ������
					$queryTask = $DB->query("select * from `tasks` where `TasksId`='$tasksId' order by `User` asc");
					$items = array();
					while ($row = mysql_fetch_array($queryTask)){

						$task1 = $row['ItemIds'];
						$l = strlen($task1);
						for ($i=0; $i<$l; $i+=4){
							$b = unpack('l', substr($task1, $i, 4));
							
							//����ʹ�õ��ǰ�ItemId����ģ�ʵ�������е������ֻ��ҽ�����Ʒ���
							$items[] = ' `presc_items`.`ItemId` = "'.$b[1].'"';
						}
					}
					if (count($items) > 0)
						$condition .= ' AND ('.implode(' OR', $items).')';
					else //��û���κ��������ݣ�����Ϊ��δ��������
						$unassigned = true;
				}
				
				if ($task != -2 || $unassigned){ 
					//��ȡ��������
					if ($type == 'normal'){
						$condition0 .= " WHERE `Service` NOT LIKE '%����%' AND `Prescription` <> ''";
					}elseif ($type == 'emergency'){
						$condition0 .= " WHERE `Service` LIKE '%����%' AND `Prescription` <> ''";
					}elseif ($type == 'hospitalized'){
						$condition0 .= " WHERE `Prescription` = ''";
					}
					
					//����
					if ($limit > 0){
						if ($random == 1 && $limit > 0){
							$condition0 = "RIGHT JOIN (select distinct `Doctor` from `prescriptions` ORDER BY RAND() limit 0, $limit) as `n` ON `prescriptions`.`Doctor` = `n`.`Doctor` ".$condition0;
						}else{
							$condition0 = "RIGHT JOIN (select distinct `Doctor` from `prescriptions` ORDER BY `Doctor` limit 0, $limit) as `n` ON `prescriptions`.`Doctor` = `n`.`Doctor` ".$condition0;
						}
					}
				}
			}
			$query0 = $DB->query('SELECT * FROM  `prescriptions`'.$condition0);
			
			$data = array();
			while($row = mysql_fetch_array($query0, MYSQL_ASSOC)){
				$data[$row['Id'].'_'.$row['Prescription']] = $row;
			}
				
			if ($dateFrom != ''){
				$condition .= " AND `Time` >='$dateFrom'";
			}
			if ($dateTo != ''){
				$condition .= " AND `Time` <='$dateTo'";
			}

			//����ҩƷ��ǩ��ȡҩƷ����
			$drugs = Drugs::getListByGroups($drugGroups, true);
			$drugConditions = array();
			foreach ($drugs as $drug){
				$drugConditions[] = '`Name`="'.$drug['name'].'"';
			}
			if (count($drugConditions) == 0){
				$drugConditions[] = 'TRUE';
			}

			$query = $DB->query('SELECT * FROM (SELECT * FROM `presc_items` WHERE '.implode(' OR ', $drugConditions).
				') AS `presc_items` LEFT JOIN `presc_evals` USING(`ItemId`) '.$condition.' ORDER BY `Id`, `Prescription`');
				
			$itemIds = array();
			$key = "";
			
			//���������Χ�Ĳ���
			if ($limit2 > 0){
				$doctors2 = array();
				foreach($data as $key => $presc){
					if ($doctors2[$presc['Doctor']] >= $limit2){
						unset($data[$key]);
					}else{
						$doctors2[$presc['Doctor']] += 1;
					}
				}
			}
				
			while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
				
				$key = $row['Id'].'_'.$row['Prescription'];
				if(isset($data[$key])){
					if(count($data[$key]['data']) < $limit3)
						$data[$key]['data'][] = $row;
				}
			}
			
			return $data;
		}
		

		//�����������Ż�ȡ������Ϣ
		static function getTasksById($tasksId = 0){
		
			global $DB;
			
			if ($tasksId == 0){
				$taskSettingsMeta = self::loadLastTaskSettings();
				$tasksId = $taskSettingsMeta['data']['id'];
			}
			
			$meta = array();
			$query = $DB->query("select * from `tasks` where `TasksId`='$tasksId'");
			if ($query){
				$meta['error'] = 0;
				
				$meta['data'] = array();
				while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
					$ids = array();
					$oids = $row['ItemIds'];
					$l = strlen($oids);
					for ($i=0; $i<$l; $i+=4){
						$b = unpack('l', substr($oids, $i, 4));
						$ids[] = $b[1];
					}
					$meta['data'][$row['User']] = $ids;
				}
			}else{
				$meta['error'] = 1;
			}
			
			return $meta;
		}

		//�����������Ż�ȡ�б�
		static function getListByTasksId($tasksId = 0, $serial = false){
			global $DB;
		
			$tasksMeta = self::getTasksById($tasksId);
			$idss = $tasksMeta['data'];
			$lists = array();
			foreach ($idss as $user => $ids){
			
				//��������
				$queryStr = 
				'select * from `prescriptions` RIGHT JOIN (SELECT * FROM `presc_evals` RIGHT JOIN (SELECT * FROM `presc_items` where `ItemId` = "'.implode('" OR `ItemId` = "',$ids).'") AS `presc_evals` USING (`ItemId`)) AS `items` USING(`Prescription`, `Id`);';
				
				$query = $DB->query($queryStr);
				$list = array();
				while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
					$list[] = $row;
				}
				if ($serial){
					$lists = array_merge($lists, $list);
				}else{
					$lists[$user] = $list;
				}
				
			}
			
			return array('error'=>0, 'data'=>$lists);
		}
		
		
		
		static function postTest($_post){

			global $DB;

			//�����п��ܵ�ID��ȡ����
			$itemIds = explode(',', $_post['itemIds']);
			
			//����ID
			$tasksId = $_post['tasksId'];
			
			global $mode;
			
			//��ѯ��Щ�������е�ID
			$query0 = $DB->query('select `ItemId` from `presc_evals`');
			$existedIds = array();
			while($row = mysql_fetch_array($query0, MYSQL_ASSOC)){
				$existedIds[] = $row['ItemId'];
			}
			
			$insert = 'insert into `presc_evals` (`TasksId`,`';
			foreach(Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
				$insert .= $KEY.'`,`';
			}
			$insert .= 'ItemId`) values ';
			
			$update = '';
			
			//ͳ�Ʋ��롢�޸ĵ���������
			$inserts = 0;
			$updates = 0;
			
			function toColumn($_post, $id, $key){
				$value = $_post[$id.'_'.$key];
				if (isset($value)){
					if ($key == 'EOther'){
						return Database::purify($value);
					}elseif ($key == 'Checked'){
						if($_post['mode'] == 1)
							return $value+1;
						else
							return $value;
					}else{
						return ($_post[$id.'~'.$key]=='on' ? '.' : '*').Database::purify($value);
					}
				}else{
					return null;
				}
			}
			
			foreach($itemIds as $id){
				$exists = false;
				foreach($existedIds as $eid){
					if ($eid == $id){
						$exists = true;
						unset($eid);
						break;
					}
				}
				
				if ($exists){ //���ԭ���Ѿ�����������¼������UPDATE����ʽ����
					
					$line = 'update `presc_evals` set `TasksId`="'.$tasksId.'",';
					$empty = true;
					
					$kvs = array();
					foreach(Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
						$value = toColumn($_post, $id, $KEY);
						if ($value == null){
							continue;
						}
						if ($value != '' && $value != '*')
							$empty = false;
						
						$kvs[] = "`$KEY`='$value'";
					}
					
					if (!$empty){
						$line .= implode(',', $kvs);
						$line .= " where `ItemId` = '$id';";
						$update .= $line;
						++$updates;
					}
					
				}else{ //���û�м�¼������INSERT����ʽ����
					
					$line = '("'.$tasksId.'","';
					$empty = true;
					
					foreach(Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
						$value = toColumn($_post, $id, $KEY);
						if ($value != '' && $value != '*')
							$empty = false;
						
						$line .= $value.'","';
					}
					$line .= $id;
					$line .= '"),';
					
					if (!$empty){
						$insert .= $line;
						++$inserts;
					}
				}
				
			}
			
			//����ķ�����Ϣ
			$meta = array();
			
			$meta['insert']['num'] = $inserts;
			if ($inserts > 0){
				echo $insert;
				$insert = substr($insert, 0, -1); //ȥ�����ġ�,��
				$meta['insert']['status'] = $DB->query($insert) ? 1 : 0;
			}else{
				$meta['insert']['status'] = 1;
			}
			
			$meta['update']['num'] = $updates;
			if ($updates > 0){
				echo $update;
				$_update = explode(';', $update); //Ҫ����ִ��
				$t = true;
				foreach ($_update as $u){
					if ($u == '')
						continue;
					if (!($t = $t && $DB->query($u))){
						break;
					}
				}
				$meta['update']['status'] = $t ? 1 : 0;
			}else{
				$meta['update']['status'] = 1;
			}
			
			return $meta;
			
		}
	
		static function assignTasks($_post){

			if ($_post['task'] != 0){
				return array('error'=>1);
			}
			
			global $DB;
			
			$exDoctors = $_post['exDoctors'];
			$dIds = $_post['dIds'];
			$tasksId = $_post['tasksId'];
			$taskNum = $_post['taskNum'];
			
			$meta = array();
			
			$taskIds = array();
			$N = count($dIds);
			$i = 1;
			//����ҽ����ȥ������Ĳ���
			for($j=0; $j<$N; ++$j){
				$skip = false;
				if ($exDoctors != null)
					foreach($exDoctors as $ex){
						if($j.'' == $ex){
							$skip = true;
							break;
						}
					}
				if (!$skip){
					if (isset($taskIds[$i]))
						$taskIds[$i] .= ','.$dIds[$j];
					else
						$taskIds[$i] = $dIds[$j];
					($i == $taskNum) ? $i = 1 : ++$i;
				}
			}

			$tasks = array();
			
			foreach($taskIds as $user => $taskId){
				$ids = explode(',', $taskId);
				$str = '';
				foreach ($ids as $item){
					 $str .= pack('l', intval($item));
				}
				$tasks[$user] = $str;
			}
			
			$queryTask = "insert into `tasks` (`User`, `ItemIds`, `TasksId`) values";
			foreach ($tasks as $taskid => $task){
				$queryTask .= " ('$taskid', '".addslashes($task)."', '$tasksId'),";
			}
			$queryTask = substr($queryTask, 0, -1);
			
			//ԭ�ȵ����ã�Ҫ��ձ�Ȼ����д�룬���ڲ���ʹ�á�
			//$DB->query("DELETE FROM `tasks` WHERE 1");
			$meta['assign']['num'] = $taskNum;
			$meta['assign']['status'] = (int)$DB->query($queryTask);
			
			return $meta;
			
			exit;
			
			
			
			//��������
			
				
			$i = 1;
			$doctors = array();
			foreach($data as $presc){
				$dr = $presc['Doctor'];
				
				if (!isset($doctors[$dr])){
					$doctors[$dr] = $i;
					$i >= $taskNum ? $i=1 : ++$i;
				}
			}
			$tasks = array();
			foreach($data as $presc){
				$str = $tasks[$doctors[$presc['Doctor']]];
				foreach ($presc['data'] as $item){
					 $str .= pack('l', intval($item['ItemId']));
				}
				$tasks[$doctors[$presc['Doctor']]] = $str;
			}
			
			$queryTask = "insert into `tasks` (`User`, `ItemIds`) values";
			foreach ($tasks as $taskid => $task){
				$queryTask .= " ('$taskid', '".addslashes($task)."'),";
			}
			$queryTask = substr($queryTask, 0, -1);
			
			//ע�⣡Ҫ��ձ�Ȼ����д�룡
			$DB->query("DELETE FROM `tasks` WHERE 1");
			$DB->query($queryTask);
			
			//�����ⲿ�ֽ�������
			/*$query = $DB->query("select * from `tasks`");
			$row = mysql_fetch_array($query);
			$task1  = $row['ItemIds'];
			
			echo $l = strlen($task1);
			for ($i=0; $i<$l; $i+=4){
				$b = unpack('l', substr($task1, $i, 4));
				echo ', '.$b[1];
			}*/
		}

		static function saveTaskSettings($_post){
			if ($_post['task'] != 0){
				return array('error'=>1);
			}
			
			global $DB;
			
			$settings = $_post;
			unset($settings['mode']);
			unset($settings['task']);
			$value = addSlashes(json_encode($settings));
			$time = date('Y-m-d h:i:s', $_SERVER['REQUEST_TIME']);
			$queryStr = "insert into `task_settings` (`Time`,`Value`) values ('$time', '$value')";
			
			if (!$DB->query($queryStr)){
				return array('error'=>2);
			}else{
				$row1 = mysql_fetch_array($DB->query('select `TasksId` from `task_settings` order by `TasksId` desc limit 0,1'));
				return array('error'=>0, 'data'=>array('id'=>$row1['TasksId']));
			}
		}
		
		
		static function loadTaskSettings($id){
			global $DB;
			
			$query = $DB->query('select * from `task_settings` where `TasksId`="'.$id.'" limit 0,1');
			if (!$query){
				return array('error'=>1);
			}else{
				$row1 = mysql_fetch_array($query, MYSQL_ASSOC);
				return array('error'=>0, 
					'data'=>array(
						'id'=>$row1['TasksId'], 'time'=>$row1['Time'], 'value'=>json_decode($row1['Value'])
					)
				);
			}
		}
		
		static function loadAllTaskSettings($desc = true){
		
			global $DB;
			
			$query = $DB->query('select * from `task_settings` order by `TasksId` '.($desc ? 'desc' : 'asc'));
			if (!$query){
				return array('error'=>1);
			}else{
				$meta = array('error'=>0, 'data'=>array());
				
				while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
					$meta['data'][] = array('id'=>$row['TasksId'], 'time'=>$row['Time'], 'value'=>json_decode($row['Value']));
				}
				return $meta;
			}
		}
		
		static function loadLastTaskSettings(){
			global $DB;
			
			$query = $DB->query('select * from `task_settings` order by `TasksId` desc limit 0,1');
			if (!$query){
				return array('error'=>1);
			}else{
				$row1 = mysql_fetch_array($query, MYSQL_ASSOC);
				return array('error'=>0, 
					'data'=>array(
						'id'=>$row1['TasksId'], 'time'=>$row1['Time'], 'value'=>json_decode($row1['Value'])
					)
				);
			}
		}
	}
	
?>