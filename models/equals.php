<?php
	class Equals {
		
		public $TABLE = '';
		function __construct(){

		}
		public function setTable($table){
			$this->TABLE = $table;
		}
		
		/*private static function _getList($sqlPostfix, $simple){
		
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
		}*/
	
		/**
		 * 获取所有标签
		 * $simple-如果为真则只获取id和name
		 */
		/*static function getList($simple){
			
			global $DB;
			
			return self::_getList('', $simple);
		}*/
		
		/**
		 * 设置标签属性
		 * $params-参数集合
		 */
		function setGroup($params){
			
			global $DB;
			
			$id = $params['id'];
			$type = $params['type'];
			$notation = $params['notation'];
			$name = $params['name'];
			$memo = $params['memo'];
			
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
				$query = $DB->query("insert into `".($this->TABLE)."` (`type`, `notation`, `name`, `data`, `memo`) values ('$type', '$notation', '$name', '$data', '$memo')");
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
				$query = $DB->query("update `".($this->TABLE)."` set ".implode(', ', $sets)." where `id` = '$id'");
			}
			
			$meta['status'] = $query ? 1 : 0;
			
			return $meta;
		}
	
		/**
		 * 根据代号获取标签信息
		 * $notation-标签代号
		 */
		function getGroup($notation){
			
			global $DB;
			
			$query0 = $DB->query("select * from `".($this->TABLE)."` where `notation` = '$notation';");
			$group = mysql_fetch_array($query0, MYSQL_ASSOC);
			
			return $group;
		}
		
		/**
		 * 根据内容获取信息
		 * $notation-标签代号
		 */
		function getGroupsByData($segment){
			
			global $DB;

			$query0 = $DB->query("select * from `".($this->TABLE)."` where `data` like '%{$segment}%';");
			$groups = array();
			while($group = mysql_fetch_array($query0, MYSQL_ASSOC)){
				$groups[] = $group;
			}
			
			return $groups;
		}
		
		/**
		 * 获取所有标签
		 * $categories-需要获取的标签类型数组，没有则是全部类型
		 * $data-需要获取data部分与否
		 */
		function getGroups($categories = null, $data = false){
			
			global $DB;
			
			if ($categories)
				$query0 = $DB->query("select * from `".($this->TABLE)."` where `type`='".implode("' OR `type`='", $categories)."';");
			else
				$query0 = $DB->query("select * from `".($this->TABLE)."`;");
			$groups = array();
			$group;
			while($group = mysql_fetch_array($query0, MYSQL_ASSOC)){
				if (!$data){
					unset($group['data']);
				}
				$groups[] = $group;
			}
			
			return $groups;
		}
		
		
		/**
		 * 删除标签
		 * $id-要删除的标签的编号
		 */
		function removeGroup($id){
			
			global $DB;
			
			$meta['status'] = $DB->query("delete from `".($this->TABLE)."` where `id` = '{$id}';") ? 1 : 0;
			return $meta;
		}
	}
	
	
	class DiseaseEquals extends Equals{
		function __construct(){
			parent::__construct();
			$this->setTable('equal_diseases');
		}
		
		/**
		 * 根据诊断查找数据
		 * $diagnosis-诊断
		 */
		function getGroupByDiagnosis($diagnosis){
			
			global $DB;
			
			$groups = array();
			$groups0 = self::getGroups(null, true);
			foreach($groups0 as $group){
				$right = false;
				if (stripos($diagnosis, $group['name'])){
					$right = true;
				}else{
					$names = explode(' ', $group['data']);
					foreach($names as $name){
						if (stripos($diagnosis, $name)){
							$right = true;
							break;
						}
					}
				}
				if ($right){
					$groups[] = $group;
				}
			}
			
			return $groups;
		}
		
		function setGroup($params){
			
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
				$query = $DB->query("insert into `".($this->TABLE)."` (`type`, `notation`, `name`, `data`, `memo`, `drugs`) values ('$type', '$notation', '$name', '$data', '$memo', '$drugs')");
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
				$query = $DB->query("update `".($this->TABLE)."` set ".implode(', ', $sets)." where `id` = '$id'");
			}
			
			$meta['status'] = $query ? 1 : 0;
			
			return $meta;
		}
	}
	
	
	class CombineEquals extends Equals{
		function __construct(){
			parent::__construct();
			$this->setTable('equal_combines');
		}
	}
	
	class DosageEquals extends Equals{	
		function __construct(){
			parent::__construct();
			$this->setTable('equal_dosages');
		}
		
		function truncate(){
			global $DB;
			return $DB->query('delete from `'.($this->TABLE).'`');
		}
		
		/**
		 * 内部方法：将数组转换为XML格式，其中“@”键对应的为属性，其余为子节点
		 * $array-数组
		 */
		private function array2XML($array){
			global $DB;
			global $depth;
			
			$str = '';

			if (is_array($array)){
				
				$attrs = array();

				if (isset($array['@'])){
					if (is_array($array['@'])){
						foreach($array['@'] as $key=>$val){
							//if (is_numeric($val))
							//	$attrs[] = "$key=$val";
							//else
								$attrs[] = "$key='$val'";
						}
					}
				}
				if (empty($attrs))
					$str .= '>';
				else
					$str .= ' '.implode(' ', $attrs).'>';
					
				if (isset($array['@']))
					unset($array['@']);
				if (true){
				
					++$depth;				
					$str .= "\r\n";

					foreach ($array as $key=>$a){
						if ($key=='@')
							continue;
							
						if (count($a) == 1){
							$str .= str_repeat("\t", $depth)."<$key".$this->array2XML($a).str_repeat("\t", $depth)."</$key>\r\n";
						}else{
							for($i=0; isset($a[$i]); ++$i){
								$str .= str_repeat("\t", $depth)."<$key".$this->array2XML($a[$i]).str_repeat("\t", $depth)."</$key>\r\n";
							}
						}
					}
					--$depth;
				}else{

				}
			}else{
				$str .= $array;
			}
			return $str;
		}
		
		/**
		 * 导出数据库为XML
		 */
		function toXML(){
			global $DB;
			global $depth;
			
			$output = '<?xml version="1.0"?>'."\r\n";
			
			$depth=1;
			$q = $DB->query('select * from `'.($this->TABLE).'` order by `id`');
			$output .= "<rules>\r\n";
			while($r = mysql_fetch_array($q, MYSQL_ASSOC)){
				$output .= "\t<rule name='{$r['name']}' id='{$r['notation']}' unit='{$r['type']}'";
				//$output .= array2XML(Util::arrayIconvUTF82GBK(json_decode($r['data'],true)), array());
				$output .= $this->array2XML(Util::arrayIconvUTF82GBK(json_decode($r['data'],true)), array());
				$output .= "\t</rule>\r\n";
			}
			$output .= "</rules>\r\n";
			
			return $output;
		}
		
		/**
		 * 从XML中载入数据，但不清空原有的
		 */
		function loadXML($xmlFile){
			global $DB;
					
			$xml = simplexml_load_string(Util::arrayIconvGBK2UTF8($xmlFile));
			if ($xml){
				$i = 1;
				foreach ($xml->rule as $x){
					$values = Util::arrayIconvUTF82GBK("'{$x['id'][0]}','{$x['name'][0]}','{$x['unit'][0]}'");
					unset($x['name']);
					unset($x['id']);
					unset($x['unit']);
					$t = str_replace('@attributes', '@', json_encode($x));
					$t = preg_replace('/\"([.\d]+)\"/', '$1', $t);
					$t = preg_replace('/\,0\:\".+?\"/', '', $t);
					
					$query = "insert into `".($this->TABLE)."` (`id`,`notation`,`name`,`type`,`data`) values ('$i',$values,'".addSlashes($t)."');";

					$DB->query($query);

					++$i;
				}
				return 0;
			}else{
				return 1;
			}
		}
	}

	class PackageEquals extends DosageEquals{
		function __construct(){
			parent::__construct();
			$this->setTable('equal_packages');
		}
	}
?>