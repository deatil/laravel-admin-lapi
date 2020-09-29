<?php

namespace Lake\Admin\Lapi\Contracts;

/*
 * API检测
 *
 * @create 2020-9-28
 * @author deatil
 */
interface ApiCheck
{
    /*
     * 设置数据
     *
     * @param array $data
     * @return self
     */
    public function withData($data = []);
    
    /*
     * 获取数据
     * @return array
     */
    public function getData();
    
    /*
     * 获取默认数据
     * @return array
     */
    public function getDefaultData();
    
    /*
     * 检测API
     * @return array
     */
    public function checkApi();

}
