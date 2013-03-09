<?php
/**
 * 
 * @author work
 *
 */
class Yun_StringTest extends PHPUnit_Framework_TestCase {
	
	
	public function test_safeExplode() {
		$empty_string 	= "";
		$string			= "1,2";
		
		$re = Yun_String::safeExplode(',', $empty_string);
		$this->assertCount(0, $re);
		
		$re = Yun_String::safeExplode(',', $string);
		$this->assertTrue(in_array('2', $re) && count($re)==2);
	}
}