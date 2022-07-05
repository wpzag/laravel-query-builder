<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;

    abstract class BasePipeline
    {
        protected function getOptions(Builder|LengthAwarePaginator|Collection $query, string $option): array|string|null
        {
            if ($query instanceof LengthAwarePaginator) {
                $model = $query->model::class;
            } elseif ($query instanceof Collection) {
                $model = $query->first()->getModel()::class;
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
