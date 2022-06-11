<?php

    use Illuminate\Http\Request;
    use Wpzag\QueryBuilder\Pipelines\FilterPipeline;
    use Wpzag\QueryBuilder\QueryBuilder;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('it wont filter if the request doesnt contain filter param', function () {
        $request = new Request(['random' => ['name' => 'ahmed']]);
        $query = TestModel::query();
        $queryBeforeFiltering = clone $query;

        $queryAfterFiltering = QueryBuilder::for(
            subject: $query,
            request: $request,
            pipelines: [FilterPipeline::class]
        );
        expect($queryBeforeFiltering->toSql())->toBe($queryAfterFiltering->toSql());
    });
