<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Wpzag\QueryBuilder\Exceptions\InvalidSortQuery;

    class SortPipeline extends BasePipeline
    {
        protected Builder $query;
        protected array $sortableArray;

        public function handle(Builder $query, Closure $next): Builder
        {
            $this->query = $query;
            if (empty(request()->sort)) {
                return $next($this->query);
            }
            $sortParams = explode(',', request()->sort);
            $this->sortableArray = $this->getOptions(query: $this->query, option: 'sortable');

            foreach ($sortParams as $sortParam) {
                $columnName = str($sortParam)->remove('-')->value();
                if ($this->isValidSortParam($columnName)) {
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
         * @param string $columnName
         * @return bool
         */
        private function isValidSortParam(string $columnName): bool
        {
            $isValid = ! empty($this->sortableArray) && in_array($columnName, $this->sortableArray);
            if (! empty($this->sortableArray) && ! in_array($columnName, $this->sortableArray) && ! config('query-builder.disable_invalid_sort_exception')) {
                throw new InvalidSortQuery($columnName, $this->sortableArray);
            }

            return $isValid;
        }
    }
