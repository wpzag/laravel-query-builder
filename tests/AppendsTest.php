<?php

    use function Pest\Laravel\getJson;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    beforeEach(function () {
        TestModel::factory(1)->create();
    });

    it('can append columns', function () {
        $this->withoutExceptionHandling();
        $res = getJson('/test?append=appended_field');
        expect($res->getData())->{0}
            ->toHaveKey('appended_field');
    });

    it('can append columns with pagination ', function () {
        $this->withoutExceptionHandling();
        $res = getJson('/test?append=appended_field&page=1');
        expect($res->getData()->data)->{0}
            ->toHaveKey('appended_field');
    });
