<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;

    class SortPipeline extends BasePipeline
    {
        /**
         * @param Builder $query
         * @param Closure $next
         * @return mixed
         */
        public function handle(Builder $query, Closure $next): Builder
        {
            $sortParams = explode(',', request()->sort);
            if (empty($sortParams)) {
                return $next($query);
            }

            foreach ($sortParams as $sortParam) {
                $columnName = str($sortParam)->remove('-')->value();
                $sortableArray = $this->getOptions(query: $query, option: 'sortable');
                if (! empty($sortableArray) && in_array($columnName, $sortableArray)) {
                    $query->orderBy($columnName, $this->getOrderDirection($sortParam));
                }
            }


            return $next($query);
        }

        /**
         * @param mixed $sortParam
         * @return string
         */
        private function getOrderDirection(mixed $sortParam): string
        {
            return str($sortParam)->contains('-') ? 'desc' : 'asc';
        }
    }
