<?php

    namespace Wpzag\QueryBuilder;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Request;
    use Illuminate\Pipeline\Pipeline;
    use Wpzag\QueryBuilder\Pipelines\FilterPipeline;

    class QueryBuilder
    {
        public static function for($subject, ?callable $callback = null, ?Request $request = null, array $pipelines = null): mixed
        {
            $query = is_subclass_of($subject, Model::class) ? $subject::query() : $subject;
            $request ??= request();
            $pipelines ??= array_filter([
                FilterPipeline::class,
                $callback,
            ]);

            return app(Pipeline::class)
                ->send(['query' => $query, 'request' => $request])
                ->through($pipelines)
                ->thenReturn();
        }
    }
