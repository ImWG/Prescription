<?php
	class Database {
		var $connect;
		var $dbHost, $dbUserName, $dbPassword, $dbName;
		
		static $COLUMNS_PRESCS, 
			$COLUMNS_ITEMS, 
			$COLUMNS_EVALS, 
			$COLUMNS_EVALS_NOID, 
			$COLUMNS_EVALS_NOCHECKED;
		
		function __construct(){
			include('config.php');
			
			$this->dbHost = $DATABASE_HOST;
			$this->dbUserName = $DATABASE_USERNAME;
			$this->dbPassword = $DATABASE_PASSWORD;
			$this->dbName = $DATABASE_NAME;
			
			//��������Ϣ
			self::$COLUMNS_PRESCS = $COLUMNS_PRESCS;
			self::$COLUMNS_ITEMS = $COLUMNS_ITEMS;
			self::$COLUMNS_EVALS = $COLUMNS_EVALS;
			
			self::$COLUMNS_EVALS_NOID = $COLUMNS_EVALS;
			unset(self::$COLUMNS_EVALS_NOID['ItemId']);			
			
			self::$COLUMNS_EVALS_NOCHECKED = self::$COLUMNS_EVALS_NOID;
			unset(self::$COLUMNS_EVALS_NOCHECKED['Checked']);
		}
		
		public function connect(){
			$this->connect = mysql_connect($this->dbHost, $this->dbUserName, $this->dbPassword);
			mysql_select_db($this->dbName, $this->connect);
			mysql_query('SET NAMES GBK', $this->connect);
		}
		
		public function query($string){
			if ($query= mysql_query($string, $this->connect))
				return $query;
			else{
				echo mysql_error();
				return null;
			}
		}
		
		/* ���ã�ת���ַ����������ע��©�� */
		static function purify($string){
			return preg_replace('[\'\"]', '\\$1', $string);
		}
	}
?>