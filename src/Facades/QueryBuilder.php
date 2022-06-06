<?php

namespace Wpzag\QueryBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wpzag\QueryBuilder\QueryBuilder
 */
class QueryBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-query-builder';
    }
}
