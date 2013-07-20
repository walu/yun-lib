<?php
/**
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Postgre_Adapter_Pdo extends Yun_Db_Adapter_Pdo {
	
	/**
	 * 
	 * @see Yun_Db__Adapter_Interface::connect()
	 */
	public function connect(array $conf) {
		try {
            $host = Yun_Array::get($conf, 'host');
            $port = Yun_Array::get($conf, 'port');
            $dbname = Yun_Array::get($conf, 'dbname');
            $user   = Yun_Array::get($conf, 'user');
            $pass   = Yun_Array::get($conf, 'pass');

			$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            
            $pdo = new Pdo($dsn, $user, $pass);
            $this->initPdo($pdo);
			return true;
			
		} catch (Exception $e) {
			$this->error_code = $e->getCode();
			$this->error_info = $e->getMessage();
			return false;
		}
	}
	
}
