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
     * $arr = [$success, $code, $msg, $data];
     *
     * @create 2020-9-13
     * @author deatil
     */
    public function json($arr);

}
