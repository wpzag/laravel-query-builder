<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Wpzag\QueryBuilder\Exceptions\InvalidSortQuery;

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
                if ($this->isValidSortParam($sortableArray, $columnName)) {
                    $this->query->orderBy($columnName, $this->getOrderDirection($sortParam));
                }
            }


            return $next($this->query);
        }

        private function getOrderDirection(mixed $sortParam): string
        {
            return str($sortParam)->contains('-') ? 'desc' : 'asc';
        }

        /**
         * @param array|string|null $sortableArray
         * @param string $columnName
         * @return bool
         */
        private function isValidSortParam(array|string|null $sortableArray, string $columnName): bool
        {
            $isValid = ! empty($sortableArray) && in_array($columnName, $sortableArray);
            if (! $isValid && ! config('query-builder.disable_invalid_sort_exception')) {
                throw new InvalidSortQuery($columnName, $sortableArray);
            }

            return $isValid;
        }
    }
