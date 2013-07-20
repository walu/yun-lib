<?php

class Yun_Db_Postgre_Main extends Yun_Db_Main {

	/**
	 * 
	 * 插入数据并返回此行数据
	 * 用于获取自增字段、默认值字段的值
	 * 
	 * @param array $row
	 * @return boolean|Ambigous <boolean, multitype:, false>
	 */
	public function insertReturn(array $row) {
		$builder = $this->getMainBuilder();
		$table = $this->getMainTable();
		$row   = $this->quoteArray($row);
		
		if (false === $table || false === $row) {
			return false;
		}
		
		$sql = $builder->sqlOfInsert($table, $row);
		$sql = $sql . " RETURNING *";   
		return $this->query($sql);
	}
}