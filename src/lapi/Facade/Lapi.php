<?php

namespace Lake\Admin\Lapi\Facade;

use Illuminate\Support\Facades\Facade;

use Lake\Admin\Lapi\Service\Lapi as LapiService;

/**
 * API
 *
 * @create 2020-9-5
 * @author deatil
 */
class Lapi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LapiService::class;
    }
}
