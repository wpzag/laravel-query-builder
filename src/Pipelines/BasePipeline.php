<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Pagination\LengthAwarePaginator;

    abstract class BasePipeline
    {
        protected function getOptions(Builder|LengthAwarePaginator $query, string $option): array|string|null
        {
            $model = $query instanceof LengthAwarePaginator ? $query->model::class : $query->getModel()::class;


            return config("query-builder.models.$model.$option");
        }

        protected function getDefaults(string $option): array
        {
            return config("query-builder.defaults.$option");
        }
    }
