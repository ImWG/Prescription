<?php
	class Util {
		static $timer;
		
		public function startTimer(){
			Util::$timer = microtime(true);
		}
		public function endTimer($show = true){
			Util::$timer = microtime(true) - Util::$timer;
			if ($show)
				echo '<p>ҳ�湲ִ��'.(Util::$timer).'����</p>';
		}
	}
?>