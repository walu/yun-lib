<?php
/**
 * 
 * @author walu<imcnan@gmail.com>
 *
 */
class Yun_ArrayTest extends PHPUnit_Framework_TestCase {
	
	public function test_getByPath() {
		$val = array('name'=>'lilei', 'age'=>19);
		$arr = array(
			0 => $val,
		);
		
		$this->assertEquals(19, Yun_Array::getByPath($arr, '0.age'));
		$this->assertEquals(19, Yun_Array::getByPath($arr, '0-age', null, '-'));
		$this->assertEquals('default', Yun_Array::getByPath($arr, '0-age-none', 'default', '-'));
		$this->assertEquals($val, Yun_Array::getByPath($arr, '0'));
	}
	
	public function test_rekey() {
		$target = array('id'=>9, 'name'=>'jim');
		$arr = array(
			array('id'=>8, 'name'=>'tom'),
			$target,
			array('id'=>10, 'name'=>'tom'),
		);
		
		$re = Yun_Array::rekey($arr, 'id');
		
		$this->assertArrayHasKey(8, $re);
		$this->assertArrayHasKey(9, $re);
		$this->assertArrayHasKey(10, $re);
		
		$this->assertEquals($re[9], $target);
	}
}