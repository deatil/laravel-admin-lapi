<?php

namespace Lake\Admin\Lapi\Middleware;

use Closure;

use Lake\Admin\Lapi\Traits\Lapi as LapiTrait;

/*
 * API检测中间件
 *
 * @create 2020-9-5
 * @author deatil
 */
class Lapi
{
    use LapiTrait;
    
    /**
     * 入口
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function handle($request, Closure $next)
    {
        $this->checkApi();
        
        return $next($request);
    }

}
