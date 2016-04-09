<?php
	class Test {
		
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
			
			
			$condition0 = '';
			
			$condition;
			if ($task == 0){
				$condition = "WHERE TRUE";
			}else{
				$condition = $mode==1 ? "WHERE (`Checked` = 1 OR `Checked` = 2)" //对于组长
					: "WHERE (`Checked` = 0 OR `Checked` = 1 OR `Checked` IS NULL)"; //对于组员
			}
			
			//如果是任务
			if ($task > 0){
				$queryTask = $DB->query("select * from `tasks` where `User`='$task'");
				$row = mysql_fetch_array($queryTask);
				
				$items = array();
				$task1 = $row['ItemIds'];
				$l = strlen($task1);
				for ($i=0; $i<$l; $i+=4){
					$b = unpack('l', substr($task1, $i, 4));
					
					//现在使用的是按ItemId分配的，实际上现有的设计中只按医生名称分配
					$items[] = ' `presc_items`.`ItemId` = "'.$b[1].'"';
				}
				
				$condition .= ' AND ('.implode(' OR', $items).')';
			}else{
				//获取处方部分
				if ($type == 'normal'){
					$condition0 .= " WHERE `Service` NOT LIKE '%急诊%' AND `Prescription` <> ''";
				}elseif ($type == 'emergency'){
					$condition0 .= " WHERE `Service` LIKE '%急诊%' AND `Prescription` <> ''";
				}elseif ($type == 'hospitalized'){
					$condition0 .= " WHERE `Prescription` = ''";
				}
				
				//人数
				if ($limit > 0){
					if ($random == 1 && $limit > 0){
						$condition0 = "RIGHT JOIN (select distinct `Doctor` from `prescriptions` ORDER BY RAND() limit 0, $limit) as `n` ON `prescriptions`.`Doctor` = `n`.`Doctor` ".$condition0;
					}else{
						$condition0 = "RIGHT JOIN (select distinct `Doctor` from `prescriptions` ORDER BY `Doctor` limit 0, $limit) as `n` ON `prescriptions`.`Doctor` = `n`.`Doctor` ".$condition0;
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
			
			
			$query = $DB->query('SELECT *, `presc_items`.`ItemId`  FROM `presc_items` LEFT JOIN `presc_evals` ON `presc_items`.`ItemId`=`presc_evals`.`ItemId` '
				.$condition.' ORDER BY `Id`, `Prescription`');
				
			$itemIds = array();
			$key = "";
			
			//清除出超范围的部分
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
		
		
		static function postTest($_post){

			global $DB;

			//将所有可能的ID提取出来
			$itemIds = explode(',', $_post['itemIds']);
			
			global $mode;
			
			//查询那些表中已有的ID
			$query0 = $DB->query('select `ItemId` from `presc_evals`');
			$existedIds = array();
			while($row = mysql_fetch_array($query0, MYSQL_ASSOC)){
				$existedIds[] = $row['ItemId'];
			}
			
			$insert = 'insert into `presc_evals` (`';
			foreach(Database::$COLUMNS_EVALS_NOID as $KEY => $NAME){
				$insert .= $KEY.'`,`';
			}
			$insert .= 'ItemId`) values ';
			
			$update = '';
			
			//统计插入、修改的数据条数
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
				
				if ($exists){ //如果原本已经存在这条记录，就以UPDATE的形式更新
					
					$line = 'update `presc_evals` set ';
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
					
				}else{ //如果没有记录，就以INSERT的形式插入
					
					$line = '("';
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
			
			//输出的返回信息
			$meta = array();
			
			$meta['insert']['num'] = $inserts;
			if ($inserts > 0){
				$insert = substr($insert, 0, -1); //去掉最后的“,”
				$meta['insert']['status'] = $DB->query($insert) ? 1 : 0;
			}else{
				$meta['insert']['status'] = 1;
			}
			
			$meta['update']['num'] = $updates;
			if ($updates > 0){
				$_update = explode(';', $update); //要分条执行
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
			$taskNum = 3;
			
			$meta = array();
			
			$taskIds = array();
			$N = count($dIds);
			$i = 1;
			//分配医生，去除例外的部分
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
			
			$queryTask = "insert into `tasks` (`User`, `ItemIds`) values";
			foreach ($tasks as $taskid => $task){
				$queryTask .= " ('$taskid', '".addslashes($task)."'),";
			}
			$queryTask = substr($queryTask, 0, -1);
			
			//注意！要清空表然后再写入！
			$DB->query("DELETE FROM `tasks` WHERE 1");
			$meta['assign']['num'] = $taskNum;
			$meta['assign']['status'] = (int)$DB->query($queryTask);
			
			return $meta;
			
			exit;
			
			
			
			//分配任务
			
				
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
			
			//注意！要清空表然后再写入！
			$DB->query("DELETE FROM `tasks` WHERE 1");
			$DB->query($queryTask);
			
			//以下这部分仅供测试
			/*$query = $DB->query("select * from `tasks`");
			$row = mysql_fetch_array($query);
			$task1  = $row['ItemIds'];
			
			echo $l = strlen($task1);
			for ($i=0; $i<$l; $i+=4){
				$b = unpack('l', substr($task1, $i, 4));
				echo ', '.$b[1];
			}*/
		}
	}
	
?>