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
}