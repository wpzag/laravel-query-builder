<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Illuminate\Database\Eloquent\Builder;
    use Wpzag\QueryBuilder\Services\QueryBuilderPaginator;

    abstract class BasePipeline
    {
        protected function getOptions(Builder|QueryBuilderPaginator $query, string $option): array|string|null
        {
            if ($query instanceof QueryBuilderPaginator) {
                $model = $query->getModelName();
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
