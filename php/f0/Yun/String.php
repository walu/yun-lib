<?php
/**
 * Yun_String
 * 
 * 用于有效操作String的一些方法
 * 
 * @author walu
 */
class Yun_String {
	
	/**
	 * 
	 * 用于修复explode(',', '')得到array('')的问题
	 * 
	 * 在平常工作中，对于explode(',', '')，我们需要得到空数组
	 * 而不是array('')
	 * 
	 * @param string $delimiter
	 * @param string $string
	 * @return array
	 */
	public static function safeExplode($delimiter, $string) {
		if (''===$string || null===$string) {
			return array();
		}
		$re = explode($delimiter, $string);
		return $re;
	}
	
	/**
	 * 判断一个字符串是否以某个串结尾
	 * 
	 * @param string $haystack
	 * @param string $search
	 * @return boolean 
	 */
	public function endWith($haystack, $search) {
		$len = strlen($search);
		return substr($haystack, 0, -$len)===$search;
	}
	
	/**
	 * 判断一个字符串是否以某个串开始
	 * 
	 * @param string $haystack
	 * @param string $search
	 * @return boolean
	 */
	public function startWith($haystack, $search) {
		return strpos($haystack, $search) === 0;
	}
}
