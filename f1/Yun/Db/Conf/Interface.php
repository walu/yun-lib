<?php
/**
 * 
 * @author walu<imcnan@gmail.com>
 *
 */
interface Yun_Db_Conf_Interface {
    
    /**
     * $sql_prefix主要是用来辅助conf获取正确的adapter使用的
     * 比如select选择对应slave集群的adapter，insert对应master集群的adapter
     * 
     * @param string $sql_prefix 一小段sql[至少6个字符]，辅助Conf决定应该选择连接那一台服务器(如果需要的话)
     * @return Yun_Db_Adapter_Interface
     */
    public function getAdapter($sql_prefix='');
    
    /**
     * @param string int
     * @return Yun_Db_Builder_Interface
     */
    public function getBuilder();
    
    public function errorCode();
    
    public function errorInfo();
}