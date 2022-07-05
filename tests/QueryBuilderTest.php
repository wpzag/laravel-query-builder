<?php

    use Wpzag\QueryBuilder\Exceptions\InvalidAppendQuery;
    use Wpzag\QueryBuilder\Exceptions\InvalidColumnException;
    use Wpzag\QueryBuilder\Exceptions\InvalidSortQuery;
    use Wpzag\QueryBuilder\Exceptions\InvalidSubject;
    use Wpzag\QueryBuilder\QueryBuilder;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('can accept model qualified name', function () {
        expect(QueryBuilder::for(TestModel::class))->not()->toThrow(InvalidSubject::class);
    });

    it('can alter query with provided callback ', function () {
        TestModel::factory(10)->create();
        $res = QueryBuilder::for(TestModel::class, callback: fn ($query) => $query->whereIn('id', [1, 2]))->get()->toArray();
        expect($res)->toHaveLength(2);
    });

    it('can be initialized with a relation', function () {
        TestModel::factory()->create();
        TestModel::find(1)->relatedModels()->create(['name' => 'John Doe']);
        $relation = QueryBuilder::for(TestModel::find(1)->relatedModels());
        expect($relation)->not()->toThrow(InvalidSubject::class);
    });

    it('can accept instance of query builder', function () {
        expect(QueryBuilder::for(TestModel::query()))->not()->toThrow(InvalidSubject::class)
            ->and(QueryBuilder::for(TestModel::whereName('one')))->not()->toThrow(InvalidSubject::class);
    });

    it('throws error if not instance of Model|Builder|Relation', function () {
        QueryBuilder::for('Not\An\Instance\Of\Model');
    })->throws(InvalidSubject::class);

    it('throws error if filter column not allowed', function () {
        config(['query-builder.disable_invalid_filter_query_exception' => false]);
        request()->query->set('filter', ['random' => 'not_allowed']);
        QueryBuilder::for(TestModel::class);
    })->throws(InvalidColumnException::class);

    it('throws error if append column not allowed', function () {
        config(['query-builder.disable_invalid_appends_exception' => false]);
        request()->query->set('append', 'asdasd');
        QueryBuilder::for(TestModel::class)->withAppends();
    })->throws(InvalidAppendQuery::class);

    it('throws error if sort column not allowed', function () {
        config(['query-builder.disable_invalid_appends_exception' => false]);
        request()->query->set('sort', 'asdasd');
        QueryBuilder::for(TestModel::class)->withAppends();
    })->throws(InvalidSortQuery::class);
