<?php

    use Illuminate\Http\Request;
    use Wpzag\QueryBuilder\QueryBuilder;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('can perform filtering', function () {
        $request = new Request(['name' => 'moahmed']);
        QueryBuilder::for(subject: TestModel::class, request: $request);
    });
