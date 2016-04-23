<?php
	class Equals {
	
		private static function _getList($sqlPostfix, $simple){
		
			global $DB;
			
			$query;
			if ($simple)
				$query = $DB->query("select `name`, `id` from ".$sqlPostfix);
			else
				$query = $DB->query("select * from ".$sqlPostfix);
				
			$data = array();
			
			while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
				$data[] = $row;
			}
			
			return $data;
		}
	
		/**
		 * 获取所有标签
		 * $simple-如果为真则只获取id和name
		 */
		static function getList($simple){
			
			global $DB;
			
			return self::_getList('', $simple);
		}
		
		/**
		 * 设置标签属性
		 * $params-参数集合
		 */
		static function setGroup($params){
			
			global $DB;
			
			$id = $params['id'];
			$type = $params['type'];
			$notation = $params['notation'];
			$name = $params['name'];
			$memo = $params['memo'];
			$drugs = $params['drugs'];
			
			$data = '';

			$values = $params['data']['values'];
			foreach($values as $value){
				if ($value != 'undefined')
					$data .= "\0" . $value;
			}
			$data = substr($data, 1);
			
			//echo $data;
			//$data = addslashes($data);
						
			$query;
			if ($id == -1)
				$query = $DB->query("insert into `equal_diseases` (`type`, `notation`, `name`, `data`, `memo`, `drugs`) values ('$type', '$notation', '$name', '$data', '$memo', '$drugs')");
			else{
				$sets = array();
				$sets[] = "`type`='0'";
				if (isset($params['notation']))
					$sets[] = "`notation`='$notation'";
				if (isset($params['name']))
					$sets[] = "`name`='$name'";
				if (isset($params['data']))
					$sets[] = "`data`='$data'";
				if (isset($params['memo']))
					$sets[] = "`memo`='$memo'";
				if (isset($params['drugs']))
					$sets[] = "`drugs`='$drugs'";
				$query = $DB->query("update `equal_diseases` set ".implode(', ', $sets)." where `id` = '$id'");
			}
			
			$meta['status'] = $query ? 1 : 0;
			
			return $meta;
		}
	
		/**
		 * 根据代号获取标签信息
		 * $notation-标签代号
		 */
		static function getGroup($notation){
			
			global $DB;
			
			$query0 = $DB->query("select * from `equal_diseases` where `notation` = '$notation';");
			$group = mysql_fetch_array($query0, MYSQL_ASSOC);
			
			return $group;
		}
		
		
		/**
		 * 获取所有标签
		 * $categories-需要获取的标签类型数组，没有则是全部类型
		 */
		static function getGroups($categories = null){
			
			global $DB;
			
			if ($categories)
				$query0 = $DB->query("select * from `equal_diseases` where `type`='".implode("' OR `type`='", $categories)."';");
			else
				$query0 = $DB->query("select * from `equal_diseases`;");
			$groups = array();
			$group;
			while($group = mysql_fetch_array($query0, MYSQL_ASSOC)){
				unset($group['data']);
				$groups[] = $group;
			}
			
			return $groups;
		}
		
		
		/**
		 * 删除标签
		 * $id-要删除的标签的编号
		 */
		static function removeGroup($id){
			
			global $DB;
			
			echo "delete from `equal_diseases` where `id` = '{$id}';";
			
			$meta['status'] = $DB->query("delete from `equal_diseases` where `id` = '{$id}';") ? 1 : 0;
			return $meta;
		}
	}
?>