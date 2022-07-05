<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;

    class ResponsePipeline
    {
        public function handle(Builder $query, Closure $next): Collection|LengthAwarePaginator
        {
            $results = $query->get();

            return $next($results);
        }
    }
