<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Pagination\LengthAwarePaginator;

    abstract class BasePipeline
    {
        protected function getOptions(Builder|LengthAwarePaginator $query, string $option): array|string|null
        {
            if ($query instanceof LengthAwarePaginator) {
                $model = $query->model::class;
            } else {
                $model = $query->getModel()::class;
            }


            return config("query-builder.models.$model.$option");
        }

        protected function getDefaults(string $option): array
        {
            return config("query-builder.defaults.$option");
        }
    }
