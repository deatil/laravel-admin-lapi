<?php

use Lake\Admin\Lapi\Facade\Lapi as LapiFacade;

if (!function_exists('lapi_check_api_sign')) {
    /*
     * 检测签名
     *
     * @create 2020-9-5
     * @author deatil
     */
    function lapi_check_api_sign() {
        return LapiFacade::checkApi();
    }
}

if (!function_exists('lapi_success_json')) {
    /*
     * 返回成功JSON
     *
     * @create 2020-9-5
     * @author deatil
     */
    function lapi_success_json($msg = '获取成功', $data = null, $code = 0) {
        return LapiFacade::successJson($msg, $data, $code);
    }
}

if (!function_exists('lapi_error_json')) {
    /*
     * 返回错误JSON
     *
     * @create 2020-9-5
     * @author deatil
     */
    function lapi_error_json($code = null, $msg = 1, $data = []) {
        return LapiFacade::errorJson($code, $msg, $msg);
    }
}