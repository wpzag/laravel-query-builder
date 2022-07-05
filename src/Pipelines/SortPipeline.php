<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;

    class SortPipeline extends BasePipeline
    {
        protected Builder $query;

        public function handle(Builder $query, Closure $next): Builder
        {
            $this->query = $query;
            $sortParams = explode(',', request()->sort);
            if (empty($sortParams)) {
                return $next($this->query);
            }

            foreach ($sortParams as $sortParam) {
                $columnName = str($sortParam)->remove('-')->value();
                $sortableArray = $this->getOptions(query: $this->query, option: 'sortable');
                if (! empty($sortableArray) && in_array($columnName, $sortableArray)) {
                    $this->query->orderBy($columnName, $this->getOrderDirection($sortParam));
                }
            }


            return $next($this->query);
        }

        private function getOrderDirection(mixed $sortParam): string
        {
            return str($sortParam)->contains('-') ? 'desc' : 'asc';
        }
    }
