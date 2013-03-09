<?php
class Yun_Db_Mysql_BuilderTest extends PHPUnit_Framework_TestCase {
	
	private $builder = null;
	
	public function getBuilderForTest() {
		
		if (null === $this->builder) {		
			$this->builder = Yun_Db_Mysql_Builder::getInstance();
		}
		
		return array(array($this->builder));
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfSelect(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfSelect('table', 'field', 'value');
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`='value'");
		

		$sql = $builder->sqlOfSelect('table', 'field', 7);
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`=7");
		

		$sql = $builder->sqlOfSelect('table', 'field', 7.01);
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`=7.01");
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfSelectAll(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfSelectAll('table');
		$this->assertEquals($sql, "SELECT * FROM `table`");
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfSelectByMuiltyValue(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfSelectByMuiltyValue('table', 'key', array('s1', 's2'));
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `key` IN ('s1','s2')");
		

		$sql = $builder->sqlOfSelectByMuiltyValue('table', 'key', array('s1', 7));
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `key` IN ('s1',7)");
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfSelectOneRow(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfSelectOneRow('table', 'field', 'value');
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`='value'  LIMIT 1");

		$sql = $builder->sqlOfSelectOneRow('table', 'field', 'value', array('id'=>'desc'));
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`='value' ORDER BY `id` DESC LIMIT 1");
		

		$sql = $builder->sqlOfSelectOneRow('table', 'field', 'value', array('id'=>'desc', 'name'=>'ASC'));
		$this->assertEquals($sql, "SELECT * FROM `table` WHERE `field`='value' ORDER BY `id` DESC,`name` ASC LIMIT 1");
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfInsert(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfInsert('table', array('name'=>'jim', 'age'=>29));
		$ex_sql = "INSERT INTO `table`(`name`,`age`) VALUES('jim',29)";
		$this->assertEquals($sql, $ex_sql);
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfInsertMuiltyRow(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfInsertMuiltyRow('table', array( array('name'=>'jim', 'age'=>29), array('name'=>'tom', 'age'=>9)  ));
		$ex_sql = "INSERT INTO `table`(`name`,`age`) VALUES('jim',29),('tom',9)";
		$this->assertEquals($sql, $ex_sql);
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfDelete(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfDelete('table', 'key', 'value');
		$ex_sql = "DELETE FROM `table` WHERE `key`='value'";
		$this->assertEquals($sql, $ex_sql);
	}
	
	/**
	 * @dataProvider getBuilderForTest
	 */
	public function test_sqlOfUpdate(Yun_Db_Mysql_Builder $builder) {
		$sql = $builder->sqlOfUpdate('table', array('name'=>'tom', 'age'=>30), 'key', 'value');
		$ex_sql = "UPDATE `table` SET `name`='tom',`age`=30 WHERE `key`='value'";
		$this->assertEquals($sql, $ex_sql);
	}
	
}
