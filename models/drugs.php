<?php
	class Drugs {
	
		private static function _getList($sqlPostfix, $simple){
		
			global $DB;
			
			$query;
			if ($simple)
				$query = $DB->query("select `name`, `id` from `drugs`".$sqlPostfix);
			else
				$query = $DB->query("select * from `drugs`".$sqlPostfix);
				
			$data = array();
			
			while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
				$data[] = $row;
			}
			
			return $data;
		}
	
		static function getList($simple){
			
			global $DB;
			
			return self::_getList('', $simple);
		}
		
		static function setGroup($params){
			
			global $DB;
			
			$id = $params['id'];
			$type = $params['type'];
			$notation = $params['notation'];
			$name = $params['name'];
			$memo = $params['memo'];
			
			$data = '';
			if ($type == 0){ //是直接指定id的类型
				$ids = $params['data']['ids'];
				if (isset($ids)){
					foreach($ids as $tid){
						$data .= pack('N', $tid);
					}
				}
			} else if ($type == 1){ //是查询的类型
				$column = $params['data']['column'];
				$values = $params['data']['values'];
				$data .= $column;
				foreach($values as $value){
					if ($value != 'undefined')
						$data .= "\0" . $value;
				}
			}
			//$data = addslashes($data);
						
			$query;
			if ($id == -1)
				$query = $DB->query("insert into `drug_groups` (`type`, `notation`, `name`, `data`, `memo`) values ('$type', '$notation', '$name', '$data', '$memo')");
			else{
				$sets = array();
				if (isset($params['type']))
					$sets[] = "`type`='$type'";
				if (isset($params['notation']))
					$sets[] = "`notation`='$notation'";
				if (isset($params['name']))
					$sets[] = "`name`='$name'";
				if (isset($params['data']))
					$sets[] = "`data`='$data'";
				if (isset($params['memo']))
					$sets[] = "`memo`='$memo'";
				$query = $DB->query("update `drug_groups` set ".implode(', ', $sets)." where `id` = '$id'");
			}
			
			$meta['status'] = $query ? 1 : 0;
			
			return $meta;
		}
	
	
		static function getGroup($notation){
			
			global $DB;
			
			$query0 = $DB->query("select * from `drug_groups` where `notation` = '$notation';");
			$group = mysql_fetch_array($query0, MYSQL_ASSOC);
			
			return $group;
		}
		
		static function getGroups(){
			
			global $DB;
			
			$query0 = $DB->query("select * from `drug_groups`;");
			$groups = array();
			$group;
			while($group = mysql_fetch_array($query0, MYSQL_ASSOC)){
				unset($group['data']);
				$groups[] = $group;
			}
			
			return $groups;
		}
		
		static function removeGroup($params){
			
			global $DB;
			
			$meta['status'] = $DB->query("delete from `drug_groups` where `id` = '{$params['id']}';") ? 1 : 0;
			return $meta;
		}
	
		static function getListByGroup($notation, $simple, $invert = false){
			
			global $DB;
			
			$query0 = $DB->query("select `type`, `data` from `drug_groups` where `notation` = '$notation';");
			$group = mysql_fetch_array($query0, MYSQL_ASSOC);
			
			$postfix;
			if ($group['type'] == 0){
				$n = strlen($group['data']) / 4;
				$conditions = array();
				for ($i=0; $i<$n; ++$i){
					$segment = substr($group['data'], $i*4, 4);
					$id = unpack('N', $segment);
					if ($invert)
						$conditions[] = " `id` <> '{$id[1]}' ";
					else
						$conditions[] = " `id` = '{$id[1]}' ";
				}
			} else if ($group['type'] == 1){
				$datas = explode("\0", $group['data']);
				$column = $datas[0];
				$conditions = array();
				for ($i=1; $i<count($datas); ++$i){
					$data = $datas[$i];
					if ($invert)
						$conditions[] = " `$column` not like '$data' ";
					else
						$conditions[] = " `$column` like '$data' ";
				}
			} else {
				return null;
			}
			
			if (count($conditions) == 0){
				if ($invert)
					$postfix = '';
				else
					$postfix = 'where false';
			}else{
				if ($invert)
					$postfix = 'where ' . implode('and', $conditions);
				else
					$postfix = 'where ' . implode('or', $conditions);
			}
			
			return self::_getList($postfix, $simple);
		}
		
	}
?>