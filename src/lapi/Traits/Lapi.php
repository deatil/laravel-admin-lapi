<?php

namespace Lake\Admin\Lapi\Traits;

use Lake\Admin\Lapi\Facade\ApiCheck as ApiCheckFacade;

/*
 * API检测
 *
 * @create 2020-9-7
 * @author deatil
 */
trait Lapi
{
    /*
     * 设置数据
     *
     * @create 2020-9-28
     * @author deatil
     */
    protected function withData($data = [])
    {
        ApiCheckFacade::withData($data);
    }
    
    /*
     * 检测签名
     *
     * @create 2020-9-7
     * @author deatil
     */
    protected function checkApi()
    {
        ApiCheckFacade::checkApi();
    }
    
    /*
     * 返回成功json
     *
     * @create 2020-9-7
     * @author deatil
     */
    protected function successJson($msg = '获取成功', $data = null, $code = 0) 
    {
        return ApiCheckFacade::successJson($msg, $data, $code);
    }
    
    /*
     * 返回错误json
     *
     * @create 2020-9-7
     * @author deatil
     */
    protected function errorJson($msg = null, $code = 1, $data = []) 
    {
        return ApiCheckFacade::errorJson($msg, $code, $data);
    }
    
}
