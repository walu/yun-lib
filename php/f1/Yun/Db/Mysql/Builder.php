<?php
/**
 * 
 * Mysql数据库的SQL - builder
 * 用来生成一些常用的sql语句
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Mysql_Builder implements Yun_Db_Builder_Interface {
	
    private static $instance = array();
    
    /**
     * @var Yun_Db_Adapter_Interface
     */
    private $adapter;
    
    /**
     * 
     * 传递conf主要是因为要进行quote，每一个Conf对应一个instance
     * 
     * @param Yun_Db_Conf_Interface $conf
     * @return Yun_Db_Mysql_Builder
     */
    public static function getInstance(Yun_Db_Conf_Interface $conf) {
        $hash = spl_object_hash($conf);
        if (!isset(self::$instance[$hash])) {
            self::$instance[$hash] = new Yun_Db_Mysql_Builder($conf);
        }
        return self::$instance[$hash];
    }
    
    /**
     * 构造函数
     * 
     * @param Yun_Db_Conf_Interface $conf
     */
    private function __construct(Yun_Db_Conf_Interface $conf) {
        $this->adapter = $conf->getAdapter();
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelect()
     */
    public function sqlOfSelect($table, $field, $value) {
       $table = $this->adapter->quote($table);
       $field = $this->adapter->quote($field);
       $value = $this->filterValueToSql($value);
       return "SELECT * FROM `{$table}` WHERE `{$field}`={$value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectAll()
     */
    public function sqlOfSelectAll($table) {
       $table = $this->adapter->quote($table);
       return "SELECT * FROM `{$table}`"; 
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectByMuiltyValue()
     */
    public function sqlOfSelectByMuiltyValue($table, $field, array $value) {
        $table = $this->adapter->quote($table);
        $field = $this->adapter->quote($field);
        $value = array_map( array($this, 'filterValueToSql'), $value);
        $value = implode(',', $value);
        return "SELECT * FROM `{$table}` WHERE `{$field}` IN ({$value})";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectOneRow()
     */
    public function sqlOfSelectOneRow($table, $field, $value, array $order_by=array()) {
        $table = $this->adapter->quote($table);
        $field = $this->adapter->quote($field);
        $value = $this->filterValueToSql($value);
        
        $sql_order = '';
        if (count($order_by)>0) {
            foreach ($order_by as $k=>$v) {
                $k = $this->adapter->quote($k);
                $v = strtoupper($v) == 'ASC' ? 'ASC' : 'DESC';
                $sql_order .= "`{$k}` {$v},";
            }
            $sql_order = 'ORDER BY ' . rtrim($sql_order, ',');
        }
        return "SELECT * FROM `{$table}` WHERE `{$field}`={$value} {$sql_order} LIMIT 1";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfInsert()
     */
    public function sqlOfInsert($table, array $row) {
        $table = $this->adapter->quote($table);
        $sql_field = $this->arrayToInsertField($row);
        $sql_value = $this->arrayToInsertValue($row);
        return "INSERT INTO `{$table}`({$sql_field}) VALUES{$sql_value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfInsertMuiltyRow()
     */
    public function sqlOfInsertMuiltyRow($table, array $row_array) {
       $table = $this->adapter->quote($table);
       $first_row = reset($row_array);
       $sql_field = $this->arrayToInsertField($first_row);
       
       $sql_value = array_map(array($this, 'arrayToInsertValue'), $row_array);
       $sql_value = implode(',', $sql_value);
       return "INSERT INTO `{$table}`({$sql_field}) VALUES{$sql_value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfDelete()
     */
    public function sqlOfDelete($table, $field, $value) {
        $table = $this->adapter->quote($table);
        $field = $this->adapter->quote($field);
        $value = $this->filterValueToSql($value);
        return "DELETE FROM `{$table}` WHERE `{$field}`={$value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfUpdate()
     */
    public function sqlOfUpdate($table, array $row, $field, $value) {
        $table = $this->adapter->quote($table);
        $field = $this->adapter->quote($field);
        $value = $this->filterValueToSql($value);
        $sql_row = $this->arrayToUpdate($row);
        return "UPDATE `{$table}` SET {$sql_row} WHERE `{$field}`={$value}";
    }
    
    /**
     * 根据数组中的key得到insert into $table($field) 中的($field)部分
     * @param array $row
     * @return string
     */
    protected function arrayToInsertField(array $row) {
        $sql = '';
        foreach ($row as $k=>$v) {
            $k = $this->adapter->quote($k);
            $sql .= "`{$k}`,";
        }
        return rtrim($sql, ',');
    }
    
    /**
     * 根据数组中的值，得到insert语句中insert into $table(..) values($val)中的($val)部分
     * @param array $row
     * @return string
     */
    protected function arrayToInsertValue(array $row) {
        $row = array_map(array($this, 'filterValueToSql'), $row);
        return '(' . implode(',', $row) .  ')';
    }
    
    /**
     * 根据数组的key=>value，得到k1=v1, k2=v2, k3=v3的sql
     * @param array $row
     * @return string
     */
    protected function arrayToUpdate(array $row) {
        foreach ($row as $k=>$v) {
            $k = $this->adapter->quote($k);
            $v = $this->filterValueToSql($v);
            $row[$k] = "`{$k}`={$v}";
        }
        return implode(',', $row);
    }
    
    /**
     * 过滤一个$value字段
     * 如果是int、float，则不在sql中拼接引号 where id=9
     * 否则，转义，拼接引号          where name='walu'
     * 
     * @param mixed $value
     * @return number|string
     */
    protected function filterValueToSql($value) {
        if (is_int($value) || is_float($value)) {
            return $value;
        } else {
            $value = $this->adapter->quote($value);
            return "'{$value}'";
        }
    }
}