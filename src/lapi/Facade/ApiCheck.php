<?php

namespace Lake\Admin\Lapi\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * API检测
 *
 * @create 2020-9-5
 * @author deatil
 */
class ApiCheck extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lapi.check';
    }
}
