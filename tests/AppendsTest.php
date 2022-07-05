<?php

    use function Pest\Laravel\getJson;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('can append columns', function () {
        TestModel::factory(1)->create();

        $res = getJson('/test?append=appended_field');
        expect($res->getData())->{0}
            ->toHaveKey('appended_field');
    });

    it('can append columns with pagination ', function () {
        TestModel::factory(1)->create();
        $res = getJson('/test?append=appended_field&page=1');
        expect($res->getData()->data)->{0}
            ->toHaveKey('appended_field');
    });
