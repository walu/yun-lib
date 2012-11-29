<?php
/**
 * 对Conf进行统一管理的地方
 * 
 * 区分Conf与Setting
 * 	1. 如果一个值随着部署环境不同而改变，我们称之为Conf。
 *  2. 如果一个值永远固定，我们称之为Setting。
 *  3. Setting配置中决不允许有Conf。
 * 
 * 备注：
 * 1. conf的值不允许是null
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Conf {
	
	private static $instance ;
	
	public static 
	
	/**
	 * 
	 * @param string $key
	 * @return null|mixed
	 */
	public function get($key) {
		
	}
	
	/**
	 * 是否有某个conf配置
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function hasConf($key) {
		
	}
}