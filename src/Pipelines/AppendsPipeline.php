<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;

    class AppendsPipeline extends BasePipeline
    {
        public function handle(LengthAwarePaginator|Collection $query, Closure $next): LengthAwarePaginator|Collection
        {
            $requestedAppends = request('append', []);

            $allowedAppends = $this->getOptions(query: $query, option: 'appends');

            if (empty($requestedAppends) || empty($allowedAppends)) {
                return $next($query);
            }
            $requestedAppends = explode(',', $requestedAppends);

            foreach ($requestedAppends as $append) {
                if (! in_array($append, $allowedAppends)) {
                    continue;
                }
                $query->append($append);
            }


            return $next($query);
            //			dd($query);
        }
    }
