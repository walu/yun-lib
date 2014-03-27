<?php
/**
 * 本adapter使用Yun_Db_Mysql_Conf配置
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Mysql_Adapter_Mysqli implements Yun_Db_Adapter_Interface {
    
    /**
     * mysqli句柄
     * 
     * @var mysqli
     */
    private $mysqli;
    
    /**
     * 错误码
     * 
     * @var int|string
     */
    private $error_code;
    
    /**
     * 错误信息
     * @var string
     */
    private $error_info;
    
    /** 
     * @see Yun_Db_Mysql_Adapter_Interface::connect()
     */
    public function connect(array $conf) {
        	
    	
    		$socket = Yun_Array::get($conf, 'socket', '');
    		$host   = Yun_Array::get($conf, 'host');
    		$port   = Yun_Array::get($conf, 'port');
    		$dbname = Yun_Array::get($conf, 'dbname');
    		
    		$user   = Yun_Array::get($conf, 'user');
    		$pass   = Yun_Array::get($conf, 'pass');
    	
    		$this->mysqli = new mysqli($host, $user, $pass, $dbname, $port, $socket);
        if ($this->mysqli->connect_errno) {
            $this->error_code = $this->mysqli->connect_errno;
            $this->error_info = $this->mysqli->connect_error;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * @return mysqli
     */
    public function getMysqli() {
        return $this->mysqli;
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::query()
     */
    public function query($sql) {
        $re = $this->mysqli->query($sql);
        $this->error_code = $this->mysqli->errno;
        $this->error_info = $this->mysqli->error;
        if ($re instanceof mysqli_result) {
            $list = array();
            while ($row = $re->fetch_array(MYSQLI_ASSOC)) {
                $list[] = $row;
            }
            $re->free();
            return $list;
        }
        return $re;
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::beginTransaction()
     */
    public function beginTransaction() {
        return $this->mysqli->query("BEGIN");
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::commit()
     */
    public function commit() {
        return $this->query("COMMIT");
    }
 
    /**
     * @see Yun_Db_Adapter_Interface::rollback()
     */
    public function rollback() {
        return $this->query("ROLLBACK");
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::lastInsertId()
     */
    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::quote()
     */
    public function quote($string) {
        return $this->mysqli->escape_string($string);
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::errorCode()
     */
    public function errorCode() {
        return $this->error_code;
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::errorInfo()
     */
    public function errorInfo() {
        return $this->error_info;
    }
}
