<?php
/**
 * 
 * Postgre 数据库的SQL - builder
 * 用来生成一些常用的sql语句
 * 
 * @see Yun_Db_Builder_Interface
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Postgre_Builder implements Yun_Db_Builder_Interface {

    /**
     * 单例
     *
     * @var Yun_Db_Mysql_Builder
     */
    private static $instance = null;
    
    /**
     * 
     * 获取单例
     * @return Yun_Db_Mysql_Builder
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new Yun_Db_Postgre_Builder();
        }
        return self::$instance;
    }
    
    /**
     * 构造函数
     */
    private function __construct() {
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelect()
     */
    public function sqlOfSelect($table, $field, $value) {
       return "SELECT * FROM \"{$table}\" WHERE \"{$field}\"='{$value}'";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectAll()
     */
    public function sqlOfSelectAll($table) {
       return "SELECT * FROM \"{$table}\"";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectByMuiltyValue()
     */
    public function sqlOfSelectByMuiltyValue($table, $field, array $value) {
        $value = "'" . implode("', ", $value) . "'";
        return "SELECT * FROM \"{$table}\" WHERE \"{$field}\" IN ({$value})";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfSelectOneRow()
     */
    public function sqlOfSelectOneRow($table, $field, $value, array $order_by=array()) {
        
        $sql_order = '';
        if (count($order_by)>0) {
            foreach ($order_by as $k=>$v) {
                $v = strtoupper($v) == 'ASC' ? 'ASC' : 'DESC';
                $sql_order .= "\"{$k}\" {$v},";
            }
            $sql_order = 'ORDER BY ' . rtrim($sql_order, ',');
        }
        return "SELECT * FROM \"{$table}\" WHERE \"{$field}\"={$value} {$sql_order} LIMIT 1";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfInsert()
     */
    public function sqlOfInsert($table, array $row) {
        $sql_field = $this->arrayToInsertField($row);
        $sql_value = $this->arrayToInsertValue($row);
        return "INSERT INTO \"{$table}\"({$sql_field}) VALUES{$sql_value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfInsertMuiltyRow()
     */
    public function sqlOfInsertMuiltyRow($table, array $row_array) {
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
        $value = $this->filterValueToSql($value);
        return "DELETE FROM \"{$table}\" WHERE \"{$field}\"={$value}";
    }
    
    /**
     * @see Yun_Db_Builder_Interface::sqlOfUpdate()
     */
    public function sqlOfUpdate($table, array $row, $field, $value) {
        $value = $this->filterValueToSql($value);
        $sql_row = $this->arrayToUpdate($row);
        return "UPDATE \"{$table}\" SET {$sql_row} WHERE \"{$field}\"={$value}";
    }
    
    /**
     * 根据数组中的key得到insert into $table($field) 中的($field)部分
     * @param array $row
     * @return string
     */
    protected function arrayToInsertField(array $row) {
        $sql = '';
        foreach ($row as $k=>$v) {
            $sql .= "\"{$k}\",";
        }
        return rtrim($sql, ',');
    }
    
    /**
     * 根据数组中的值，得到insert语句中insert into $table(..) values($val)中的($val)部分
     * @param array $row
     * @return string
     */
    protected function arrayToInsertValue(array $row) {
        return "('" . implode("', '", $row) .  "')";
    }
    
    /**
     * 根据数组的key=>value，得到k1=v1, k2=v2, k3=v3的sql
     * @param array $row
     * @return string
     */
    protected function arrayToUpdate(array $row) {
        foreach ($row as $k=>$v) {
            $row[$k] = "\"{$k}\"='{$v}'";
        }
        return implode(',', $row);
    }
}
