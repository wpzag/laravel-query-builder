<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Wpzag\QueryBuilder\Services\QueryBuilderPaginator;

    class PaginationPipeline extends BasePipeline
    {
        protected Builder $query;

        public function handle(Builder $query, Closure $next): LengthAwarePaginator
        {
            $this->query = $query;

            $defaults = $this->getDefaults('per_page');
            $defaultMaxPerPage = $defaults[ 'max' ];
            $defaultPerPage = $defaults[ 'default' ];

            $maxPerPage = $this->getOptions(query: $this->query, option: 'max_per_page') ?? $defaultMaxPerPage;
            $requestedPerPage = (int) request('limit', $defaultPerPage);

            $limit = min($requestedPerPage, $maxPerPage);

            $paginated = new QueryBuilderPaginator($this->query->paginate($limit));
            $paginated->setModelName($this->query->getModel()::class);


            return $next($paginated);
        }
    }
