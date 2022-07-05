<?php

    namespace Wpzag\QueryBuilder\Services;

    use Illuminate\Pagination\LengthAwarePaginator;

    class QueryBuilderPaginator extends LengthAwarePaginator
    {
        protected string $modelName;

        public function __construct(LengthAwarePaginator $paginator)
        {
            parent::__construct($paginator->items, $paginator->total, $paginator->perPage, $paginator->currentPage, $paginator->options);
        }

        public function getModelName(): string
        {
            return $this->modelName;
        }

        public function setModelName(string $modelName): void
        {
            $this->modelName = $modelName;
        }
    }
