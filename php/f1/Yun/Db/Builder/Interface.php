<?php
/**
 * 
 * 本类用于生成一系列常用的Sql供Main使用
 * 
 * 
 * 一、关于$value实现的解释：
 * 1.区分string与int、float，也就是在传递前先强制转换类型，有助于builder的转义与拼接。
 * 对string一般使用quote，对int与float一般就使用floatval()
 * 
 * 二、各个Sql拼装方法不允许有过于复杂的逻辑，全部能够成功返回String。 
 *  1. quote不在Builder里做，防止失败。比如mysql_real_escape_string依赖于连接
 *  2. 传递给各个参数的方法我们认为都是已经转义好的！
 *
 * 我们通过第二个约定来简化整个Yun_Db的结构
 *
 * @author walu<imcnan@gmail.com>
 */
interface Yun_Db_Builder_Interface {
	
	/**
	 * 获取一条select语句，含义：WHERE $field = $value;
	 * 
	 * 要求$field的值必须完全等于$value，不能是like或者postgresql中any之类的部分相等。
	 * 
	 * @param string $field
	 * @param mixed  $value
	 * @param bool   $is_number
	 * @return string
	 */
	public function sqlOfSelect($table, $field, $value);
	
	/**
	 * 获取某个表的全部记录
	 * 
	 * @return string
	 */
	public function sqlOfSelectAll($table);
	
	/**
	 * 获取一条select语句，且只取符合条件的一条记录
	 * 
	 * @param string $field
	 * @param string $value
	 * @param array $order_by
	 * @return string
	 */
	public function sqlOfSelectOneRow($table, $field, $value, array $order_by);
	
	/**
	 * 获取一条select语句，要求$field的值与$value的某个值相等
	 * 
	 * @param string $field
	 * @param array $value
	 * @return string
	 */
	public function sqlOfSelectByMuiltyValue($table, $field, array $value);
	
	/**
	 * 向$table中插入一行数据
	 * 
	 * @param string $table
	 * @param array $row
	 * @return string
	 */
	public function sqlOfInsert($table, array $row);
	
	/**
	 * 向$table中插入多行数据，采用insert...values(),(),()批量插入的形式
	 * 
	 * @param string $table
	 * @param array $row_array
	 * @return string
	 */
	public function sqlOfInsertMuiltyRow($table, array $row_array);
	
	/**
	 * 获取UPDATE $table SET {$row} WHERE $field=$value的sql
	 *  
	 * @param string $table
	 * @param array $row
	 * @param string $field
	 * @param string $value
	 * @return string
	 */
	public function sqlOfUpdate($table, array $row, $field, $value);
	
	/**
	 * 获取删除table中$field=$value的sql
	 * 
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 */
	public function sqlOfDelete($table, $field, $value);
}
