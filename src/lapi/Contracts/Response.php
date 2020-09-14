<?php

namespace Lake\Admin\Lapi\Contracts;

/*
 * 响应契约
 *
 * @create 2020-9-13
 * @author deatil
 */
interface Response
{
    /*
     * 响应json输出
     * @param boolen $success
     * @param int $code
     * @param string|null $msg
     * @param array|null $data
     * @return string json
     *
     * @create 2020-9-13
     * @author deatil
     */
    public function json($success, $code, $msg, $data);

}
