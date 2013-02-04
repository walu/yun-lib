<?php
/**
 * Yun_Db_Adapter_Interface
 * 
 * 和数据库交互的驱动
 * 
 * @author walu<imcnan@gmail.com>
 */
interface Yun_Db_Adapter_Interface {
    
    /**
     * 是否已连接或打开数据库
     * 
     * @return bool
     */
    public function isConnect();
    
    /**
     * 执行一条SQL
     * 
     * SELECT类语句：成功返回array, 数据库错误返回false
     * 其它语句：成功执行返回true，错误返回false
     * 
     * @param string $sql
     * @return array|false
     */
	public function query($sql);
	
	/**
	 * 开始事务
	 * 
	 * @return bool
	 */
	public function beginTransaction();
	
	/**
	 * 提交事务
	 * 
	 * @return bool
	 */
	public function commit();
	
	/**
	 * 返回刚才执行insert语句对应的自增id或序列的值
	 * 
	 * 如果在执行insert之后又执行过其它语句，则会返回不可预料的结果
	 * 
	 * @return int
	 */
	public function lastInsertId();
	
	/**
	 * 将字符串进行必要的转义，防止SQL注入等安全攻击
	 * 
	 * 刚性要求：
	 * 1. 只转义字符串，不要做转义之外的操作（比如在两边加上单引号等）。
	 * 2. 如果底层驱动确实没有合适的方法，那就请用htmlspecialchars($string, ENT_QUOTES)，addslashes等代替;
	 * 3. 必须优先使用底层驱动自带的转义方法，如$mysqli->real_escape_string 
	 * @param string $string
	 * @return string
	 */
	public function quote($string);
	
	/**
	 * 返回最近一次的错误码
	 * 
	 * @return int
	 */
	public function errorCode();
	
	/**
	 * 返回最近一次的错误信息
	 * 
	 * 注意！有的驱动返回的不是array，需要转。
	 * @return string
	 */
	public function errorInfo();
}