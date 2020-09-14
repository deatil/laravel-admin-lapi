<?php

namespace Lake\Admin\Lapi\Lib;

/**
 * sha256签名
 * 
 * @create 2020-9-5
 * @author deatil
 */
class Sha256Sign 
{
    private static $instance; 
    
    /**
     * 单例
     *
     * @create 2020-9-5
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
     * @create 2020-9-5
     * @author deatil
     */
    public function makeSign($data, $key = '') 
    {
        ksort($data);
        $str = $this->makeSignContent($data);
        $sign = base64_encode(hash_hmac('sha256', $str, $key, true));
        return $sign;
    }

    /**
     * 生成签名内容
     * @param $data
     * @return string
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function makeSignContent($data)
    {
        $buff = '';
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $v = trim($v);
                $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
            }
        }
        return trim($buff, '&');
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     *
     * @create 2020-9-5
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
     * @create 2020-9-5
     * @author deatil
     */
    private function __construct()   
    {   
    } 
    
    /**
     * 禁止克隆
     *
     * @create 2020-9-5
     * @author deatil
     */
    private function __clone()  
    {  
    }
    
}
