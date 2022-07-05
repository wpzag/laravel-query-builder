<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use Wpzag\QueryBuilder\Filters\FilterQuery;

    class FilterPipeline extends BasePipeline
    {
        protected ?array $allowedFilters = null;
        protected ?array $requestFilters = null;
        protected array $methods = [];
        protected array $operators = [];
        protected Builder $query;
        protected Request $request;

        public function handle(Builder $query, Closure $next): Builder
        {
            if ($this->init($query)->canPerformFiltering()) {
                $this->filterQuery();
            }

            return $next($this->query);
        }

        private function init(Builder $query): self
        {
            $this->query = $query;
            $this->request = request();
            $this->requestFilters = $this->request->filter;
            $this->allowedFilters = $this->getOptions(query: $this->query, option: 'filterable');
            $this->methods = config('query-builder.methods');
            $this->operators = config('query-builder.operators');

            return $this;
        }

        /**
         * Check if it's possible to perform any filtering .
         * @return bool
         */
        private function canPerformFiltering(): bool
        {
            return ! empty($this->requestFilters) && is_array($this->requestFilters)
                && ! empty($this->allowedFilters) && is_array($this->allowedFilters);
        }

        /**
         * Loop over request filters and perform the necessary queries .
         * @return void
         */
        private function filterQuery(): void
        {
            foreach ($this->requestFilters as $columns => $value) {
                (new FilterQuery(
                    query: $this->query,
                    allowedFilters: $this->allowedFilters,
                    requestFilters: $value,
                    requestColumns: $columns,
                    methods: $this->methods
                ))->handle();
            }
        }
    }
