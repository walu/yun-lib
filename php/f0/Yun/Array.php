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
	
	/**
	 * 根据一个带格式的key，来获取数组中的某个值
	 * 
	 * @param array $arr
	 * @param string|number $path_key
	 * @param mixed $default_value
	 * @param string $path_separator 必须为string，否则请用Yun_Array::get
	 * @return mixed
	 */
	public static function getByPath(array $arr, $path_key, $default_value=null, $path_separator='.') {
		$path_key_arr = explode($path_separator, $path_key);
		$retval = $arr;
		foreach ($path_key_arr as $current_key) {
			$retval = self::get($retval, $current_key);
			if (!is_array($retval)) {
				break;
			}
		}
		return false === next($path_key_arr) ? $retval : $default_value;
	}
	
	public static function rekeyByPath() {
		
	}
	
}