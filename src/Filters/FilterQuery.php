<?php

    namespace Wpzag\QueryBuilder\Filters;

    use Illuminate\Database\Eloquent\Builder;

    class FilterQuery
    {
        use FilterHelpers;

        public function __construct(
            private Builder                    $query,
            private readonly array             $allowedFilters,
            private readonly array|string|null $requestFilters,
            private readonly string            $requestColumns,
            private readonly array             $methods,
        ) {
        }

        public function handle(): void
        {
            $this->filter($this->requestColumns, $this->requestFilters);
        }

        private function filter(string $columns, string|array|null $values): void
        {
            $columns = explode(',', $columns);
            $values = $this->makeFiltersArray($values);


            if (! is_array($values)) {
                return;
            }
            $this->query->where(function (Builder $query) use ($columns, $values) {
                foreach ($columns as $column_key => $column) {
                    $this->filterColumn($column, $column_key, $values, $query);
                }
            });
        }

        private function isRelationColumn(string $key): bool
        {
            return str($key)->contains('.');
        }

        private function filterColumn(string $column, int $column_key, array $values, Builder $query): void
        {
            foreach ($values as $key => $value) {
                $this->applyFilters($column, $column_key, $key, $value, $query);
            }
        }

        private function applyFilters(string $column, int $column_key, int $key, array $filter, Builder $query): void
        {
            if (! $this->columnIsAllowed($column)) {
                return;
            }

            $isRelationColumn = $this->isRelationColumn($column);
            $relation = $isRelationColumn ? $this->getRelationFromColumn($column) : null;

            $column = $isRelationColumn ? $this->getColumnNameFromRelation($column) : $column;


            $operator = ($this->isColumnExact($column) && $filter[ 'operator' ] === 'like') ? '=' : $filter[ 'operator' ];


            $method = $filter[ 'method' ];
            $method = $key === 0 && $column_key === 0 ? $method : 'or' . str($method)->title();

            $value = $filter[ 'value' ];
            $params_count = $filter[ 'params_count' ];
            if ($isRelationColumn) {
                $query->whereHas(
                    $relation,
                    fn ($q) => $this->buildQuery($q, $params_count, $method, $column, $value, $operator)
                );
            } else {
                $this->buildQuery($query, $params_count, $method, $column, $value, $operator);
            }
        }

        private function getRelationFromColumn(string $column): string
        {
            return str($column)->before('.')->value();
        }

        private function getColumnNameFromRelation(string $column): string
        {
            return str($column)->after('.')->value();
        }

        private function buildQuery(Builder $query, int $params_count, string $method, string $column, string|array|null $value, ?string $operator): void
        {
            if ($params_count === 1) {
                $query->$method($column);
            }
            if ($params_count === 2) {
                $query->$method($column, $value);
            }
            if ($params_count === 3) {
                $value = $operator === 'like' ? "%$value%" : $value;
                $query->$method($column, $operator, $value);
            }
        }
    }
