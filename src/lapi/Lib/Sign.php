<?php

namespace Lake\Admin\Lapi\Lib;

/**
 * 签名
 * 
 * @create 2020-8-12
 * @author deatil
 */
class Sign 
{
    private static $instance; 
    
    /**
     * 单例
     *
     * @create 2020-8-12
     * @author deatil
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * 生成内容签名
     * @param $data
     * @return string
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function makeSign($data, $key = '') 
    {
        ksort($data);
        $string = md5($this->makeSignContent($data) . '&key=' . $key);
        return strtoupper($string);
    }

    /**
     * 生成签名内容
     * @param $data
     * @return string
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function makeSignContent($data)
    {
        $buff = '';
        foreach ($data as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
        }
        return trim($buff, '&');
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
    /**
     * 禁止克隆
     *
     * @create 2020-8-12
     * @author deatil
     */
    private function __construct()   
    {   
    } 
    
    /**
     * 禁止克隆
     *
     * @create 2020-8-12
     * @author deatil
     */
    private function __clone()  
    {  
    }
    
}
    