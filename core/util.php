<?php
	class Util {
		static $timer;
		
		public function startTimer(){
			Util::$timer = microtime(true);
		}
		public function endTimer($show = true){
			Util::$timer = microtime(true) - Util::$timer;
			if ($show)
				echo '<p>“≥√Êπ≤÷¥––'.(Util::$timer).'∫¡√Î</p>';
		}
		
		static public function arrayIconv($from, $to, $arg){
			if (is_array($arg)){
				foreach ($arg as $key=>$item){
					$arg[$key] = self::arrayIconv($from, $to, $item);
				}
			}else{
				$arg = iconv($from, $to, $arg);
			}
			return $arg;
		}
		
		static public function arrayIconvGBK2UTF8($arg){
			return self::arrayIconv('gbk', 'utf-8', $arg);
		}
		static public function arrayIconvUTF82GBK($arg){
			return self::arrayIconv('utf-8', 'gbk', $arg);
		}
	}
?>