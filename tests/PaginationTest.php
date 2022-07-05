<?php

    // Creates 5 TestModels
    use function Pest\Laravel\getJson;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    beforeEach(function () {
        TestModel::factory(10)->create();
    });

    it('can paginate ', function () {
        $res = getJson('/test?page=1');
        expect($res->getData()->data)
            ->toHaveLength(10);
    });
    it('can paginate with limit', function () {
        $res = getJson('/test?page=1&limit=5');
        expect($res->getData()->data)
            ->toHaveLength(5);
    });
