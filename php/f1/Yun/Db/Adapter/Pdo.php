<?php
/**
 * 这里写了一个通用的PDO驱动的Adapter
 * 
 * 1. 本类已经默认完成了pdo的实现
 * 
 * 但是本类不允许直接使用，如果你要贡献一个数据库插件(如Mysql)且支持pdo，需要在自己的adapter里：
 * class Yun_Db_Mysql_Adapter_Pdo     extends Yun_Db_Adapter
 * class Yun_Db_Mysql_Adapter_Mysqli  extends Yun_Db_Adapter
 * 
 * 以此说明mysql插件支持两种驱动
 * 
 * @author walu<imcnan@gmail.com>
 */
abstract class Yun_Db_Adapter_Pdo implements Yun_Db_Adapter_Interface {
    
    /**
     * 
     * @var Pdo
     */
    protected $pdo;
    
    /**
     * 这个方法就别覆盖了～
     * 也别自己造别的方法了，主要是害怕忘了设置PDO::ATTR_ERRMODE
     * 
     * @param PDO $pdo
     */
    final public function initPdo(PDO $pdo) {
        $this->pdo  = $pdo;
        
        //设置错误处理方式，通过返回值来表达。既不要去log warning，也不要throw exception
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }
    
    /**
     * @see Yun_Db_Adapter_Interface::query()
     */
	public function query($sql) {
				
	    $sql = trim($sql);
	    //" DELETE UPDATE INSERT SELECT "
	    $act = strtoupper(substr($sql, 0, 6));
	    if ("DELETE"==$act || "UPDATE"==$act || "INSERT"==$act) {
	        $re = $this->pdo->exec($sql);
	        $re = false !== $re;
	        return $re;
	    } 
	    $re = $this->pdo->query($sql);
	    if ($re instanceof PDOStatement) {
	        return $re->fetchAll(PDO::FETCH_ASSOC);
	    }
	    return false;
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::beginTransaction()
	 */
	public function beginTransaction() {
	    return $this->pdo->beginTransaction();	
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::commit()
	 */
	public function commit() {
		return $this->pdo->commit();
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::quote()
	 */
	public function quote($string) {
		$string = $this->pdo->quote($string);
		$string = substr($string, 1, strlen($string)-2);
		return $string;
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::isConnect()
	 */
	public function isConnect() {
		return true;
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::lastInsertId()
	 */
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
	
	
	/**
	 * @see Yun_Db_Adapter_Interface::errorCode()
	 */
	public function errorCode() {
	    return $this->pdo->errorCode();	
	}
	
	/**
	 * @see Yun_Db_Adapter_Interface::errorInfo()
	 */
	public function errorInfo() {
		$info = $this->pdo->errorInfo();
		return implode(' ', $info);
	}
}
