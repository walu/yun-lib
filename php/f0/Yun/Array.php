<?php
/**
 * Yun_Array
 * 
 * 有关数组操作的一些工具方法
 * 
 * @author walu<imcnan@gmail.com>
 * 
 */

class Yun_Array {
	
	/**
	 * 根据key，获取某个数组第一维的某一个值
	 * 
	 * @param array  $arr
	 * @param string|number $key
	 * @param mixed  $default_value 如果key不存在，则返回此默认值
	 * @return mixed
	 */
	public static function get(array $arr, $key, $default_value=null) {
		return false!==array_key_exists($key, $arr) ? $arr[$key] : $default_value;
	}
	
	public static function getByPath(array $arr, $path_key, $default_value=null, $path_separator='.') {
		
	}
	
	public static function rekey() {
		
	}
	
	public static function rekeyByPath() {
		
	}
	
}