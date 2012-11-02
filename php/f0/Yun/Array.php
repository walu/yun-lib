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
	 * <code>
	 * <?php
	 * 	$arr = array(0=>array('name'=>'lilei'), 1=>array('name'=>'hanmeimei'));
	 * 	echo Yun_Array::getByPath($arr, '0.name'); //输出：lilei
	 * 	echo Yun_Array::getByPath($arr, '1-name', null, '-');//输出：hanmeimei
	 * </code>
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
	
	/**
	 * 根据二位数组第二维上的某个索引的值重排数组
	 * 
	 * 1.如果第二维没有这个索引，则舍弃此条数据。
	 * 2.新数组的顺序与原数组一致。
	 * 
	 * <code>
	 * <?php
	 * 	$list = array(array('name'=>'lilei', 'sex'=>'boy'), array('name'=>'hanmeimei', 'sex'=>'girl'));
	 * 	$list = Yun_Array::rekey($list, 'name');
	 *  //$list为: array(
	 *  //	'lilei'=>array('name'=>'lilei', 'sex'=>'boy'), 
	 *  //	'hanmeimei'=>array('name'=>'hanmeimei', 'sex'=>'girl')
	 *  //);
	 * </code>
	 * 
	 * @param array $arr
	 * @param string $key
	 * @return array
	 */
	public static function rekey(array $arr, $key) {
		$fun_re = array();
		foreach ($arr as $value) {
			if (empty($value[$key])) {
				continue;
			}
			$fun_re[$value[$key]] = $value;
		}
		return $fun_re;
	}
}
