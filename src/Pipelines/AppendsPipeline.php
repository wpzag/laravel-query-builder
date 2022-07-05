<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Wpzag\QueryBuilder\Exceptions\InvalidAppendQuery;

    class AppendsPipeline extends BasePipeline
    {
        public function handle(LengthAwarePaginator|Builder $query, Closure $next): LengthAwarePaginator|Collection
        {
            $requestedAppends = request('append', []);

            $allowedAppends = $this->getOptions(query: $query, option: 'appends');

            $query = $query instanceof LengthAwarePaginator ? $query : $query->get();

            if (empty($requestedAppends) || empty($allowedAppends)) {
                return $next($query);
            }

            $requestedAppends = explode(',', $requestedAppends);

            foreach ($requestedAppends as $append) {
                if (! $this->appendIsAllowed($append, $allowedAppends)) {
                    continue;
                }
                $query->append($append);
            }


            return $next($query);
        }

        /**
         * @param string $append
         * @param array|string $allowedAppends
         * @return bool
         */
        private function appendIsAllowed(string $append, array|string $allowedAppends): bool
        {
            $isAllowed = in_array($append, $allowedAppends);

            if (! $isAllowed && ! config('query-builder.disable_invalid_appends_exception')) {
                throw new InvalidAppendQuery($append, $allowedAppends);
            }

            return $isAllowed;
        }
    }
