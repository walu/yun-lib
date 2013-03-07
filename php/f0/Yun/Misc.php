<?php
/**
 * 一些小工具
 *
 *
 * @author walu
 */
class Yun_Misc {

    /**
     * 判断所有的参数是否相同（===）
     * 
     * 如果参数个数<2，则返回false
     * @return bool
     */
    public function equal() {
        $args = func_get_args();
        if (count($args) < 2) {
            return false;
        }

        $first = reset($args);
        foreach ($args as $value) {
            if ($first !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * 判断所有参数的值是否相等（==）
     *
     * 如果参数个数<2，则返回false
     * @return bool
     */
    public function valueEqual() {
        $args = func_get_args();
        if (count($args) < 2) {
            return false;
        }

        $first = reset($args);
        foreach ($args as $value) {
            if ($first != $value) {
                return false;
            }
        }

        return true;
    
    }

    /**
     * 为什么没有 第二个参数及以后的参数是否有一个参数与第一个参数相等(同)?
     * in_array($first, array($second, $third, $fourth...), true or false);写起来代码量是一样的
     */
}
