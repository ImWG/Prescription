<?php
	class Drugs {
	
		/* 几种标签类型的常量 */
		const TYPE_FIXED = 0;
		const TYPE_DYNAMIC = 1;
		const TYPE_SUPER = 2;
		
		static $COLUMNS_DYNAMIC_GROUP = array('name'=>'名称','specie'=>'基本药物','production'=>'产地');
	
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
			
			$data = '';
			if ($type == self::TYPE_FIXED ){ //是直接指定id的类型
				$ids = $params['data']['ids'];
				if (isset($ids)){
					foreach($ids as $tid){
						$data .= pack('N', $tid);
					}
				}
			} else if ($type == self::TYPE_DYNAMIC){ //是查询的类型
				$column = $params['data']['column'];
				$values = $params['data']['values'];
				$data .= $column;
				foreach($values as $value){
					if ($value != 'undefined')
						$data .= "\0" . $value;
				}
			} else if ($type == self::TYPE_SUPER){ //是包含其他标签的类型
				$ids = $params['data']['ids'];
				if (isset($ids)){
					$data .= implode(' ', $ids);
				}
			}
			//echo $data;
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
	
		/**
		 * 根据代号获取标签信息
		 * $notation-标签代号
		 */
		static function getGroup($notation){
			
			global $DB;
			
			$query0 = $DB->query("select * from `drug_groups` where `notation` = '$notation';");
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
				$query0 = $DB->query("select * from `drug_groups` where `type`='".implode("' OR `type`='", $categories)."';");
			else
				$query0 = $DB->query("select * from `drug_groups`;");
			$groups = array();
			$group;
			while($group = mysql_fetch_array($query0, MYSQL_ASSOC)){
				unset($group['data']);
				$groups[] = $group;
			}
			
			return $groups;
		}
		
		
		/**
		 * 从超级标签中获取标签集
		 * $notation-超级标签代号，$invert-是否反选；无论如何，均不会获取超级标签
		 */
		static function getGroupsByGroup($notation, $invert = false){
			
			global $DB;
			
			$superGroup = self::getGroup($notation);
			if ($superGroup['type'] != self::TYPE_SUPER)
				return array();
			$notations = explode(' ', $superGroup['data']);
			
			if (count($notations) > 0){
				$query0 = $DB->query("select * from `drug_groups` where ".($invert ? 'NOT' : '')."(`notation`='".implode("' OR `notation`='", $notations)."') and type <> ".self::TYPE_SUPER.";");
			}
			
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
		static function removeGroup($params){
			
			global $DB;
			
			$meta['status'] = $DB->query("delete from `drug_groups` where `id` = '{$id}';") ? 1 : 0;
			return $meta;
		}
	
	
		/**
		 * 通过标签获取药品列表
		 * $notation-标签代号；$simple-如果为真则只获取id和name；$invert-是否反选
		 */
		static function getListByGroup($notation, $simple, $invert = false){
			
			global $DB;
			
			$group = self::getGroup($notation);
			
			if ($group['type'] == self::TYPE_SUPER){
				$groups = self::getGroupsByGroup($notation);
				$notations = array();
				foreach ($groups as $group){
					$notations[] = $group['notation'];
				}
				
				return self::getListByGroups($notations, $simple, $invert);
				
			}else{	
				$postfix;
				if ($group['type'] == self::TYPE_FIXED){
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
				} else if ($group['type'] == self::TYPE_DYNAMIC){
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
		
		
		/**
		 * 通过标签组获取药品列表
		 * $notations-标签代号数组；$simple-如果为真则只获取id和name；$invert-是否反选
		 */
		static function getListByGroups($notations, $simple, $invert = false){
			$drugs = array();
			if (count($notations) > 0)
				foreach ($notations as $notation){
				
					$drugs1 = self::getListByGroup($notation, $simple, $invert);
					foreach ($drugs1 as $drug1){
						$exist = false;
						foreach ($drugs as $drug){
							if ($drug['id'] == $drug1['id']){
								$exist = true;
								break;
							}
						}
						if (!$exist){
							$drugs[] = $drug1;
						}
					}
				}
			return $drugs;
		}
		
	}
?>