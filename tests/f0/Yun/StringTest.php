<?php
/**
 * 
 * @author walu 
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

    public function test_randomString() {
        $re = Yun_String::randomString(100);
        $this->assertEquals(100, strlen($re));

        $re = Yun_String::randomString(5, '0');
        $this->assertEquals('00000', $re);

        $re = Yun_String::randomString(5, '');
        $this->assertEmpty($re);
    }

    public function test_str2Hex() {
        $str_hex = "\xef\x12";
        $this->assertEquals("\\xef\\x12", Yun_String::str2Hex($str_hex));
    }


    public function test_str2Oct() {
        $str_hex = "\77\12";
        $this->assertEquals("\\77\\12", Yun_String::str2Oct($str_hex));
    }

}
