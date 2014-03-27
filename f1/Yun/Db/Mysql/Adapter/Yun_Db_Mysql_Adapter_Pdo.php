<?php
/**
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Mysql_Adapter_Pdo extends Yun_Db_Adapter_Pdo {
	
	/**
	 * 
	 * 如果$socket不为空，会优先使用socket
	 * 
	 * @see Yun_Db_Mysql_Adapter_Interface::connect()
	 */
	public function connect(array $conf) {
		try {
			$socket = Yun_Array::get($conf, 'socket', '');
			$host   = Yun_Array::get($conf, 'host');
			$port   = Yun_Array::get($conf, 'port');
			$dbname = Yun_Array::get($conf, 'dbname');
			
			$user   = Yun_Array::get($conf, 'user');
			$pass   = Yun_Array::get($conf, 'pass');
			
			$dsn = ('' === $socket) 
				? "mysql:host={$host};port={$port};dbname={$dbname}"  
				: "mysql:unix_socket={$socket};dbname={$dbname}"
			;
			
			$pdo = new Pdo($dsn, $user, $pass);
			$this->initPdo($pdo);
			return true;
		} catch (Exception $e) {
			$this->error_code = $e->getCode();
			$this->error_info = $e->getMessage();
			return false;
		}
	}
	
	public function errorInConnect() {
		return array(
			'error_code' => $this->error_code_in_connect,
			'error_info' => $this->error_info_in_connect,
		);
	}
}