<?php
/**
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Mysql_Adapter_Pdo extends Yun_Db_Adapter_Pdo implements Yun_Db_Mysql_Adapter_Interface {
	
	/**
	 * 
	 * 如果$socket不为空，会优先使用socket
	 * 
	 * @see Yun_Db_Mysql_Adapter_Interface::connect()
	 */
	public function connect($host, $user, $pass, $dbname, $port, $socket='') {
		try {
			$dsn = ('' === $socket) 
				? "mysql:host={$host};port={$port};dbname={$dbname}"  
				: "mysql:unix_socket={$socket};dbname={$dbname}"
			;
			
			$pdo = new Pdo($dsn, $user, $pass);
			$this->initPdo($pdo);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
}